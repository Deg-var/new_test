<?php
$params = require __DIR__ . '/params.php';

$urls = array();

$traffic = 0;

$crawlers = array();

$codes = array();

$crawlersResult = array();

$codeResult = array();

$pattern = $params['pattern'];

$systems = $params['bots'];

// Подключаем файл
$log = 'access.log';

// Делим на строки
$lines = file($log);

foreach ($lines as $line) {

    $elem =  preg_match($pattern, $line, $matches);

    // Если есть еще не встречающиеся url пишем их в массив
    $url = $matches[6];
    if (!in_array($url, $urls)) {
        array_push($urls, $url);
    }

    // Суммируем трафик
    $traffic += $matches[9];

    // Ищем поисковики
    $userAgent = $matches[11];
    foreach ($systems as $bots => $botsKeys) {
        foreach ($botsKeys as $bot) {
            if (str_contains($userAgent, $bot)) {
                array_push($crawlers, $bots);
            }
        }
    }

    // Ищем коды, если найдены, считаем сколько каждого
    $code = $matches[8];
    if ($code) {
        array_push($codes, $code);
    }
}


// Считаем сколько запросов на просмотр
$views = count($lines);

// сколько уникальных url
$urlsnum = count($urls);

$codes = array_count_values($codes);

$crawlers = array_count_values($crawlers);

$result = [
    'views' => $views,
    'urls' => $urlsnum,
    'traffic' => $traffic,
    'crawlers' => $crawlers,
    'statusCodes' => $codes
];

$file = json_encode($result, JSON_PRETTY_PRINT);

$fileName = date("Y-m-d H-i-s") . '.json';

file_put_contents($fileName, $file);
