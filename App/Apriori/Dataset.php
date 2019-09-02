<?php
namespace Apriori;

use \Exception;

/**
 * 项集组成的集合
 *
 * @author az13js
 */
class Dataset
{
    /** @var array 保存多个项集的一维数组 */
    protected $itemSets = [];

    /**
     * 初始化方法
     *
     * @param array $itemSets 项集数组。里面的元素是ItemSet的实例。
     * @throws Exception 当元素出现类型错误时候抛出异常
     * @author az13js
     */
    public function __construct(array $itemSets = [])
    {
        foreach ($itemSets as $itemSet) {
            if (!is_object($itemSet) || !($itemSet instanceof ItemSet)) {
                throw new Exception('The type of variable $itemSet is not the instance of ItemSet, type: ' . gettype($itemSet));
            }
            $this->itemSets[] = $itemSet;
        }
    }

    /**
     * 计算给定的项集的支持度
     *
     * @param ItemSet $itemSet 一个项集
     * @return float 返回计算出来的支持度的值
     * @throws Exception 当数据集是空的时候，计算支持度将抛出异常
     * @author az13js
     */
    public function support(ItemSet $itemSet): float
    {
        $totalItemSet = count($this->itemSets, COUNT_NORMAL);
        if (0 == $totalItemSet) {
            throw new Exception('Dataset is empty');
        }
        $subSetCounnt = 0;
        foreach ($this->itemSets as $itemSetInDataset) {
            if ($itemSet->isSubSetOf($itemSetInDataset)) {
                $subSetCounnt += 1;
            }
        }
        return $subSetCounnt / $totalItemSet;
    }

    /**
     * 添加一个项集
     *
     * @param ItemSet $itemSet 项集
     * @return Dataset $this，当前的Dataset实例
     * @author az13js
     */
    public function addItemSet(ItemSet $itemSet): Dataset
    {
        $this->itemSets[] = $itemSet;
        return $this;
    }

    /**
     * 返回整个数据集中的所有项
     *
     * @return array 所有存在的项，将按照顺序进行返回
     * @author az13js
     */
    public function getItems(): array
    {
        $items = [];
        foreach ($this->itemSets as $itemSet) {
            $items = array_unique(array_merge($items, $itemSet->getItems()));
        }
        sort($items, SORT_STRING);
        return $items;
    }

    /**
     * 计算置信度
     *
     * @param ItemSet $x 关联规则X项集
     * @param ItemSet $xy 关联规则XY并项集
     * @return float 置信度
     * @throws Exception 当数据集是空或分母为0的时候将抛出异常
     * @author az13js
     */
    public function confidence(ItemSet $x, ItemSet $xy): float
    {
        $totalItemSet = count($this->itemSets, COUNT_NORMAL);
        if (0 == $totalItemSet) {
            throw new Exception('Dataset is empty');
        }
        $subSetCounntX = 0;
        $subSetCounntXY = 0;
        foreach ($this->itemSets as $itemSetInDataset) {
            if ($xy->isSubSetOf($itemSetInDataset)) {
                $subSetCounntXY += 1;
            }
            if ($x->isSubSetOf($itemSetInDataset)) {
                $subSetCounntX += 1;
            }
        }
        if (0 == $subSetCounntX) {
            throw new Exception('$x is 0');
        }
        return $subSetCounntXY / $subSetCounntX;
    }
}
