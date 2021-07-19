<?php
require __DIR__ . '/vendor/autoload.php';

use Databox\Client;

$c = new Client('bzvaoteolq7dfo08s1ujvc');

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
    ['transaction', 12134, null, null, 'USD'],
    ['transaction', 3245, null, null, 'EUR']
]);

?>
