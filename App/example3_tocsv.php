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

$startTime = new Timer\Timer();

$datas = getDatas();

$createDatasTime = new Timer\Timer();
echo 'Create data: ' . ($createDatasTime->sub($startTime)) . ' sec' . PHP_EOL;

$createObjectAndRunBefore = new Timer\Timer();
$data = new Apriori\Apriori(0.006, 0.3, $datas);
$createObjectAndRunAfter = new Timer\Timer();
echo 'Create object and calculate: ' . ($createObjectAndRunAfter->sub($createObjectAndRunBefore)) . ' sec' . PHP_EOL;

$echoDataBefore = new Timer\Timer();
file_put_contents('example3.csv', "FROM,TO,SUPPORT,CONFIDENCE" . PHP_EOL);
foreach ($data->getAssociationRule()->getAssociationPairs() as $pair) {
	$from = implode('&', $pair->getFromItemSet()->getItems());
	$to = implode('&', $pair->getToItemSet()->getItems());
	$support = $pair->getSupport();
	$confidence = $pair->getConfidence();
	file_put_contents('example3.csv', "\"$from\",\"$to\",$support,$confidence" . PHP_EOL, FILE_APPEND);
}
$echoDataAfter = new Timer\Timer();
echo 'Save data: ' . ($echoDataAfter->sub($echoDataBefore)) . ' sec' . PHP_EOL;
showMemoryInfo();