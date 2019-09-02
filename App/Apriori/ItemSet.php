<?php
namespace Apriori;

use \Exception;

/**
 * 项集
 *
 * @author az13js
 */
class ItemSet
{
    /** @var array 保存多个项的字符串组成的一维数组 */
    protected $items = [];

    /**
     * 初始化会重新对元素进行排序
     *
     * @param array $items 项。元素不能出现重复。
     * @throws Exception 当元素出现重复或内容非字符串的时候抛出异常
     * @author az13js
     */
    public function __construct(array $items)
    {
        foreach ($items as $item) {
            if (!is_string($item)) {
                throw new Exception('The type of variable $item is not string, type: ' . gettype($item));
            }
            $this->items[] = $item;
        }
        if (count(array_unique($items)) != count($items)) {
            throw new Exception('There are duplicates in the array');
        }
        sort($this->items, SORT_STRING);
    }

    /**
     * 添加项到项集内
     *
     * @param string $item 字符串，项
     * @return ItemSet 返回自身，$this
     * @throws Exception 当项已经存在的时候抛出异常
     * @author az13js
     */
    public function addItem(string $item): ItemSet
    {
        if (in_array($item, $this->items)) {
            throw new Exception('Duplicate item: ' . $item);
        }
        $this->items[] = $item;
        sort($this->items, SORT_STRING);
        return $this;
    }

    /**
     * 以字符串数组方式返回项集的项
     *
     * @return array 返回所有的项
     * @author az13js
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * 判断此项集是不是另一个项集的子集
     *
     * @param ItemSet $parent 可能的父集
     * @return bool 是$parent的子集返回true，否则返回false
     * @author az13js
     */
    public function isSubSetOf(ItemSet $parent): bool
    {
        $parentItems = $parent->getItems();
        foreach ($this->items as $item) {
            if (!in_array($item, $parentItems)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 判断此项集是不是存在给定的项
     *
     * @param string $item
     * @return bool 存在返回true否则返回false
     * @author az13js
     */
    public function haveItem(string $item): bool
    {
        return in_array($item, $this->items);
    }
}
