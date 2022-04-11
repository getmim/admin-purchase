# admin-purchase

## Instalasi

Jalankan perintah di bawah di folder aplikasi:

```
mim app install admin-purchase
```

```
// Old Statuses
$old_statuses = [
    'Canceled',
    'Draft',
    'Checkout',
    'Paid',
    'On the way',
    'Delivering',
    'Returning',
    'Returned'
];

// New Statuses
$new_statuses = [
    0  => 'Canceled',
    10 => 'Draft',
    20 => 'Expired',
    30 => 'Checkout',
    40 => 'Paid',
    50 => 'Approved',
    60 => 'Delivering',
    70 => 'Delivered',
    80 => 'Returning',
    90 => 'Returned'
];
```
