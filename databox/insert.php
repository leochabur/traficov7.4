<?php
require __DIR__ . '/vendor/autoload.php';

use Databox\Client;

$c = new Client('687qou26v44ks88sks4wowccowg8kgw8',['verify' => false]);

$ok = $c->push('sales', 203);
if ($ok) {
    echo 'Inserted,...';
}

$c->insertAll([
    ['sales', 203],
    ['sales', 103, '2015-01-01 17:00:00'],
]);

// Or push some attributes
$ok = $c->push('sales', 203, null, [
    'city' => 'Boston'
]);

print_r(
    $c->lastPush(3)
);

// Or push with units
$c->insertAll([
    ['transaction', 43243, null, null, 'TOMASES'],
    ['transaction', 24434, null, null, 'OSVALDOS'],
    ['transaction', 123, null, null, 'DANIELES'],
    ['transaction', 314553, null, null, 'MARIOS'],
    ['transaction', 1366, null, null, 'ANGELES'],
    ['transaction', 345, null, null, 'RESTOS']
]);

?>
