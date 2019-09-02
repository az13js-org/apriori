<?php
namespace Apriori;

/**
 * 组合穷举辅助
 *
 * @author az13js
 */
class Combinatorial
{
    /** @var array 需要穷举组合的数组 */
    private $inputs;

    /**
     * 初始化组合的所有可能元素
     *
     * @param array $inputs 组合的所有元素
     *
     * @author az13js
     */
    public function __construct(array $inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * 生成器方法
     *
     * @author az13js
     */
    public function getCombinatorial()
    {
        foreach ($this->recurrence(0, count($this->inputs, COUNT_NORMAL), []) as $data) {
            yield $data;
        }
    }

    /**
     * 递归遍历的生成器
     *
     * @param int 数组下标起始
     * @param int 数组总长度
     * @param array $head 数组已生成遍历左元素集合
     * @author az13js
     */
    private function recurrence(int $start, int $total, array $head)
    {
        for ($i = $start; $i < $total; $i++) {
            $mergeHead = array_merge($head, [$this->inputs[$i]]);
            yield $mergeHead;
            foreach ($this->recurrence($i + 1, $total, $mergeHead) as $data) {
                yield $data;
            }
        }
    }
}
