<?php
require 'autoload.php';

/**
 * 从文件中读取数据并以二维数组的形式返回
 *
 * @return array
 * @author kiki,az13js
 */
function getDatas(): array
{
	$record_str = file_get_contents('records.csv');
	$record_lines = explode("\n", $record_str); unset($record_str);
	$records = [];
	foreach ($record_lines as $i => $line) {
		if (trim($line)) {
			$record = explode(',', $line);
			foreach ($record as &$cloumn) {
				$cloumn = trim($cloumn);
			}
			$record = array_unique($record);
			sort($record);
			$records[] = $record;
		}
	} unset ($record_lines);
	return $records;
}

/**
 * 打印内存占用峰值
 *
 * @return array
 * @author kiki,az13js
 */
function showMemoryInfo()
{
	$size = memory_get_peak_usage(true);
    echo PHP_EOL . '内存开销 ' . round(
	    $size / pow(1024, ($i = floor(log($size,1024)))), 2
	) . ' ' . ['b','kb','mb','gb','tb','pb'][$i];
}


/**
 * 获取事件发生的次数。
 *
 * @param array $datas 采用getDatas()函数返回的数据
 * @param array $eventNames 一个一维的字符串数组，采用与逻辑，进行发生的次数统计
 * @return int 次数
 */
function getEventHappen(array $datas, array $eventNames): int
{
    $sum = 0;
    foreach ($datas as $sample) {
        $isHappen = true;
        foreach ($eventNames as $event) {
            if (!in_array($event, $sample)) {
                $isHappen = false;
                break;
            }
        }
        if ($isHappen) {
            ++$sum;
        }
    }
    return $sum;
}

/**
 * 获取事件不发生的次数。
 *
 * @param array $datas 采用getDatas()函数返回的数据
 * @param array $eventNames 一个一维的字符串数组，采用与逻辑，进行不发生的次数统计
 * @return int 次数
 */
function getEventNotHappen(array $datas, array $eventNames): int
{
    $sum = 0;
    foreach ($datas as $sample) {
        $isHappen = true;
        foreach ($eventNames as $event) {
            if (!in_array($event, $sample)) {
                $isHappen = false;
                break;
            }
        }
        if (false === $isHappen) {
            ++$sum;
        }
    }
    return $sum;
}

/**
 * 获取事件发生不发生的次数。
 *
 * @param array $datas 采用getDatas()函数返回的数据
 * @param array $eventNamesHappen 一个一维的字符串数组，采用与逻辑，进行发生的次数统计
 * @param array $eventNamesNotHappen 一个一维的字符串数组，采用与逻辑，进行不发生的次数统计
 * @return int 次数
 */
function getEventHappenAndNotHappen(array $datas, array $eventNamesHappen, array $eventNamesNotHappen): int
{
    $sum = 0;
    foreach ($datas as $sample) {
        $isHappenEventHappen = true;
        foreach ($eventNamesHappen as $event) {
            if (!in_array($event, $sample)) {
                $isHappenEventHappen = false;
                break;
            }
        }
        $isNotHappenEventHappen = true;
        foreach ($eventNamesNotHappen as $event) {
            if (!in_array($event, $sample)) {
                $isNotHappenEventHappen = false;
                break;
            }
        }
        if ($isHappenEventHappen && false === $isNotHappenEventHappen) {
            ++$sum;
        }
    }
    return $sum;
}

/**
 * 获取可以认为事件关联的最小把握
 *
 * @param float $x2 卡方
 * @return float
 */
function getBelieve(float $x2): float
{
    $map = [
        'p' => [0.5, 0.4, 0.25, 0.15, 0.1, 0.05, 0.025, 0.01, 0.005, 0.001],
        'x' => [0.455, 0.708, 1.323, 2.072, 2.706, 3.841, 5.024, 6.635, 7.879, 10.828],
    ];
    if ($x2 < $map['x'][0]) {
        return 0;
    }
    foreach ($map['x'] as $k => $x) {
        if ($x2 < $x) {
            return 1 - $map['p'][$k - 1];
        }
    }
    return 0.999;
}

/**
 * 获取事件相关性的最小把握
 *
 * @param array $data 数据集
 * @param array $eventA
 * @param array $eventB
 * @return float
 */
function getEventAssocBelieve(array $data, array $eventA, array $eventB): float
{
    /*    A !A */
    /* B  H  I */
    /* !B J  K */
    $pathH = getEventHappen($data, array_merge($eventA, $eventB));
    $pathI = getEventHappenAndNotHappen($data, $eventB, $eventA);
    $pathJ = getEventHappenAndNotHappen($data, $eventA, $eventB);
    $pathK = getEventNotHappen($data, array_merge($eventA, $eventB));
    $result = ($pathH + $pathI + $pathJ + $pathK) * (pow($pathH * $pathK - $pathI * $pathJ, 2) / (($pathH + $pathI) * ($pathJ + $pathK) * ($pathH + $pathJ) * ($pathI + $pathK)));
    return getBelieve($result);
}

$startTime = new Timer\Timer();

$datas = getDatas();

$createDatasTime = new Timer\Timer();
echo 'Create data: ' . ($createDatasTime->sub($startTime)) . ' sec' . PHP_EOL;

$createObjectAndRunBefore = new Timer\Timer();
$data = new Apriori\Apriori(0.006, 0.3, $datas);
$createObjectAndRunAfter = new Timer\Timer();
echo 'Create object and calculate: ' . ($createObjectAndRunAfter->sub($createObjectAndRunBefore)) . ' sec' . PHP_EOL;

$saveDataBefore = new Timer\Timer();
file_put_contents('example4.csv', "FROM,TO,SUPPORT,CONFIDENCE,BELIEVE" . PHP_EOL);
foreach ($data->getAssociationRule()->getAssociationPairs() as $pair) {
    $eventFrom = $pair->getFromItemSet()->getItems();
    $eventTo = $pair->getToItemSet()->getItems();
	$from = implode('&', $eventFrom);
	$to = implode('&', $eventTo);
	$support = $pair->getSupport();
	$confidence = $pair->getConfidence();
    $believe = getEventAssocBelieve($datas, $eventFrom, $eventTo);
	file_put_contents('example4.csv', "\"$from\",\"$to\",$support,$confidence,$believe" . PHP_EOL, FILE_APPEND);
}
$saveDataAfter = new Timer\Timer();
echo 'Save data: ' . ($saveDataAfter->sub($saveDataBefore)) . ' sec' . PHP_EOL;
showMemoryInfo();