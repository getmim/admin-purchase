<?php
/**
 * PurchaseController
 * @package admin-purchase
 * @version 0.0.1
 */

namespace AdminPurchase\Controller;

use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibPagination\Library\Paginator;
use Purchase\Model\Purchase;
use PurchasePayment\Model\PurchasePayment;
use Purchase\Model\PurchaseProduct;
use PurchaseDelivery\Model\PurchaseDelivery;
use LibCourier\Library\Courier;
use LibWorker\Library\Worker;

class PurchaseController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['product', 'purchase']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_purchase)
            return $this->show404();

        $cond = $pcond = [];
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;

        if($status = $this->req->getQuery('status'))
            $pcond['status'] = $cond['status'] = $status;
        else
            $cond['status'] = ['__op', '>', 0];

        list($page, $rpp) = $this->req->getPager(25, 50);

        $purchases = Purchase::get($cond, $rpp, $page, ['created'=>false]) ?? [];

        if($purchases)
            $purchases = Formatter::formatMany('purchase', $purchases, ['user']);

        $params          = $this->getParams('Purchase');
        $params['purchases'] = $purchases;
        $params['form']  = new Form('admin.purchase.index');

        $params['form']->validate( (object)$this->req->get() );

        // pagination
        $params['total'] = $total = Purchase::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminPurchase'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }

        $this->resp('purchase/index', $params);
    }

    public function courierAction()
    {
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_purchase)
            return $this->show404();

        $id = $this->req->param->id;

        $purchase = Purchase::getOne(['id' => $id]);
        if (!$purchase) {
            return $this->show404();
        }

        $form = new Form('admin.purchase.courier');

        if(!($valid = $form->validate($purchase)) || !$form->csrfTest('noob'))
            return $this->show404();

        $set = [
            'courier_receipt' => $valid->courier_receipt
        ];

        if ($purchase->status == 3 && !module_exists('cli-purchase-delivery')) {
            $set['status'] = 4;
        }

        Purchase::set($set, ['id' => $purchase->id]);

        // register the worker if module exists
        if (module_exists('cli-purchase-delivery')) {
            $name = 'purchase-delivery-' . $purchase->id;
            Worker::remove($name);
            $route = ['cliPurchaseDeliveryCheck', ['id' => $purchase->id]];
            Worker::add($name, $route, [], time());
        }

        $next = $this->router->to('adminPurchaseDetails', ['id' => $purchase->id]);

        $this->res->redirect($next);
    }

    public function detailsAction()
    {
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_purchase)
            return $this->show404();

        $id = $this->req->param->id;

        $purchase = Purchase::getOne(['id' => $id]);
        if (!$purchase) {
            return $this->show404();
        }

        $params = $this->getParams('Purchase Details');
        $params['purchase'] = Formatter::format('purchase', $purchase, ['user']);
        $params['form'] = new Form('admin.purchase.courier');

        $params['form']->validate($purchase);

        $params['payment'] = null;
        if (module_exists('purchase-payment')) {
            $payment = PurchasePayment::getOne(['purchase' => $purchase->id]);
            if ($payment) {
                $params['payment'] = Formatter::format('purchase-payment', $payment);
            }
        }

        $params['products'] = null;
        $products = PurchaseProduct::get(['purchase' => $purchase->id]);
        if ($products) {
            $params['products'] = Formatter::formatMany('purchase-product', $products, ['product']);
        }

        $params['tracks'] = null;
        if (module_exists('purchase-delivery')) {
            $delivery = PurchaseDelivery::getOne(['purchase' => $purchase->id]);
            if ($delivery) {
                $params['tracks'] = json_decode($delivery->data);
            }
        }

        $this->resp('purchase/details', $params);
    }
}
