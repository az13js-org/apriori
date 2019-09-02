<?php
require 'autoload.php';

$x = [];
for ($i = 0; $i < 4000; $i++) {
    $temp = [];
    for ($j = 0; $j < 10; $j++) {
        $temp[] = (string)mt_rand(0, 9);
    }
    $x[] = array_unique($temp);
}

$data = new Apriori\Apriori(0.4, 0.6, $x);
foreach ($data->getAssociationRule()->getAssociationPairs() as $pair) {
    echo implode(',', $pair->getFromItemSet()->getItems()) . '->' . implode(',', $pair->getToItemSet()->getItems()) . ' Support:' . $pair->getSupport() . ' Confidence:' . $pair->getConfidence() . PHP_EOL;
}
