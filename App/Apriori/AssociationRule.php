<?php
namespace Apriori;

/**
 * 关联规则
 *
 * @author az13js
 */
class AssociationRule
{
    /**
     * 关联规则对
     * @var array
     */
    private $associationPair = [];

    /**
     * 生成关联规则
     *
     * @param float $minConfidence 最小置信度
     * @param array $frequentItemSet 频繁项集的数组
     * @param Dataset $dataset 数据集
     * @author az13js
     */
    public function __construct(float $minConfidence, array $frequentItemSet, Dataset $dataset)
    {
        foreach ($frequentItemSet as $itemSet) {
            if (!($itemSet instanceof ItemSet)) {
                throw new Exception('The type of variable $itemSet is not the instance of ItemSet, type: ' . gettype($itemSet));
            }
            $support = $dataset->support($itemSet);
            $items = $itemSet->getItems();
            $total = count($items);
            foreach ((new Combinatorial($items))->getCombinatorial() as $fromItems) {
                if (isset($fromItems[$total - 1])) {
                    continue;
                }
                $from = new ItemSet($fromItems);
                $confidence = $dataset->confidence($from, $itemSet);
                if ($confidence >= $minConfidence) {
                    $this->associationPair[] = new AssociationPair($from, new ItemSet($items), $confidence, $support);
                }
            }
        }
    }

    /**
     * 数组方式返回关联规则对
     *
     * @return array
     * @author az13js
     */
    public function getAssociationPairs()
    {
        foreach ($this->associationPair as $pair) {
            yield $pair;
        }
    }
}
