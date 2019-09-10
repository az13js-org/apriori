<?php
require 'autoload.php';

$data = [];
for ($j = 1; $j <= 100; $j++) {
    $data[$j] = [];
    for ($i = 0; $i < $j; $i++) {
        $data[$j][$i] = $i;
    }
}

foreach ($data as $test) {
    $start = new Timer\Timer();
    $obj = new Apriori\Combinatorial($test);
    $res = [];
    foreach ($obj->getCombinatorial() as $item) {
        $res[] = $item;
    }
    $total = count($res, COUNT_NORMAL);
    $end = new Timer\Timer();
    echo count($test) . ' ' . $total . ' ' . ($end->sub($start)) . PHP_EOL;
}