<?php
require 'autoload.php';

$data = new Apriori\Apriori(3/5-0.01, 3/4-0.01, [["a","b","e"],["a","c","e"],["b","c"],["a","b","c","e"],["a","c","d"]]);
foreach ($data->getAssociationRule()->getAssociationPairs() as $pair) {
    echo implode(',', $pair->getFromItemSet()->getItems()) . '->' . implode(',', $pair->getToItemSet()->getItems()) . PHP_EOL;
}
