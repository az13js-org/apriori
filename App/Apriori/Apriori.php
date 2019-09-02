<?php
namespace Apriori;

/**
 * Apriori算法的实现
 *
 * @author az13js
 */
class Apriori
{
    /** @var float 最小支持度 */
    protected $minSupport = 0;

    /** @var float 最小置信度 */
    protected $minConfidence = 0;

    /** @var Dataset 数据集对象 */
    protected $dataset;

    /** @var array 寻找到的频繁项集对象的数组 */
    private $frequentItemSet = [];

    /** @var AssociationRule 关联规则 */
    private $associationRule;

    /**
     * 初始化参数
     *
     * @param float $minSupport 最小支持度，当小于这个值的时候项集被认为不是频繁的
     * @param float $minConfidence 最小置信度，小于这个值的时候不被认为项集间存在关联
     * @param array $dataset 二维数组，数据集。格式如[['a','b','c'],['b','c'],['c,d,e']]
     * @author az13js
     */
    public function __construct(float $minSupport, float $minConfidence, array $dataset)
    {
        $this->dataset = new Dataset();
        foreach ($dataset as $itemSet) {
            $this->dataset->addItemSet(new ItemSet($itemSet));
        }
        $this->minSupport = $minSupport;
        $this->minConfidence = $minConfidence;
        /* 第一步，获得频繁项集 */
        $this->frequentItemSet = $this->getFrequentItemSet($minSupport, $this->dataset);
        /* 第二步，获得关联规则 */
        $this->associationRule = $this->calculateAssociationRule($minConfidence, $this->frequentItemSet, $this->dataset);
    }

    /**
     * 计算并以数组形式给定的数据集的频繁项集
     *
     * @param float $minSupport 最小支持度
     * @param Dataset $dataset 数据集对象
     * @return array 由多个项集组成的数据
     */
    public function getFrequentItemSet(float $minSupport, Dataset $dataset): array
    {
        $returnData = [];
        $results = [];
        $items = $dataset->getItems();
        foreach ($items as $key => $item) {
            $itemSet = new ItemSet([$item]);
            if ($dataset->support($itemSet) < $minSupport) {
                unset($items[$key]);
            } else {
                $results[] = $itemSet;
            }
        }
        $items = array_values($items);
        while (true) {
            $itemSets = [];
            foreach ($results as $itemSet) {
                foreach ($items as $item) {
                    if (!$itemSet->haveItem($item)) {
                        $newItemSet = new ItemSet(array_merge($itemSet->getItems(), [$item]));
                        if (!$this->itemInArray($newItemSet, $itemSets) && $dataset->support($newItemSet) >= $minSupport) {
                            $itemSets[] = $newItemSet;
                        }
                    }
                }
            }
            if (!empty($itemSets)) {
                $items = (new Dataset($itemSets))->getItems();
                $results = $itemSets;
                $returnData = array_merge($returnData, $results);
            } else {
                return $results;
            }
        }
    }

    /**
     * 返回关联规则
     *
     * @return AssociationRule
     * @author az13js
     */
    public function getAssociationRule(): AssociationRule
    {
        return $this->associationRule;
    }

    /**
     * 判断给定的项集是不是在项集的集合中存在项完全相同的
     *
     * @param ItemSet $itemSet 一个项集
     * @param array $itemSets 若干项集的数组
     * @return bool 存在返回true，不存在返回false
     * @throws Exception $itemSets元素类型不是ItemSet抛出异常
     * @author az13js
     */
    private function itemInArray(ItemSet $itemSet, array $itemSets): bool
    {
        foreach ($itemSets as $itemset) {
            if (!($itemset instanceof ItemSet)) {
                throw new Exception('The type of variable $itemset is not the instance of ItemSet, type: ' . gettype($itemset));
            }
            if ($itemSet->getItems() === $itemset->getItems()) {
                return true;
            }
        }
        return false;
    }

    /**
     * 生成关联规则
     *
     * @param float $minConfidence 最小置信度
     * @param array $frequentItemSet 频繁项集的数组
     * @param Dataset $dataset 数据集
     * @return AssociationRule 关联规则
     * @author az13js
     */
    private function calculateAssociationRule(float $minConfidence, array $frequentItemSet, Dataset $dataset): AssociationRule
    {
        return new AssociationRule($minConfidence, $frequentItemSet, $dataset);
    }
}
