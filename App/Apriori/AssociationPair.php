<?php
namespace Apriori;

/**
 * 关联对
 *
 * @author az13js
 */
class AssociationPair
{
    /** @var ItemSet X项集 */
    private $from;

    /** @var ItemSet XY项集 */
    private $parent;

    /** @var ItemSet Y项集 */
    private $to;

    /** @var float 置信度 */
    private $confidence;

    /** @var float 支持度 */
    private $support;

    /**
     * 用于保存关联规则
     *
     * @param ItemSet $from
     * @param ItemSet $parent
     * @param float $confidence
     * @param float $support
     * @author az13js
     */
    public function __construct(ItemSet $from, ItemSet $parent, float $confidence, float $support)
    {
        $this->from = $from;
        $this->parent = $parent;
        $this->to = $this->getSubSet($from, $parent);
        $this->confidence = $confidence;
        $this->support = $support;
    }

    /**
     * 获取关联的左侧项集
     *
     * @return ItemSet
     * @author az13js
     */
    public function getFromItemSet(): ItemSet
    {
        return $this->from;
    }

    /**
     * 获取关联的右侧项集
     *
     * @return ItemSet
     * @author az13js
     */
    public function getToItemSet(): ItemSet
    {
        return $this->to;
    }

    /**
     * 并集的支持度
     *
     * @return float
     * @author az13js
     */
    public function getSupport(): float
    {
        return $this->support;
    }

    /**
     * 置信度
     *
     * @return float
     * @author az13js
     */
    public function getConfidence(): float
    {
        return $this->confidence;
    }

    /**
     * 获取 $parent 除了 $sub 之外的子集
     *
     * @param ItemSet $sub
     * @param ItemSet $parent
     * @return ItemSet
     * @author az13js
     */
    private function getSubSet(ItemSet $sub, ItemSet $parent): ItemSet
    {
        $items = [];
        foreach ($parent->getItems() as $item) {
            if (!$sub->haveItem($item)) {
                $items[] = $item;
            }
        }
        return new ItemSet($items);
    }
}
