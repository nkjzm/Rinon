<?php

require_once "models/Mail.php";
require_once "models/Http.php";

$mail = Mail::parseMailData(file_get_contents("php://stdin"));

$array = explode("\n", $mail['body']); // とりあえず行に分割
$array = array_map('trim', $array); // 各行にtrim()をかける
$array = array_filter($array, 'strlen'); // 文字数が0の行を取り除く
$array = array_values($array); //キーを連番に振りなおし

if(!array_search('りのんオフィシャルブログ「りのんとのんのん」Powered by Ameba の記事が更新されました。', $array)){
    return;
}

$string = "なかじくん！頑張ってブログ更新したよ♡\n";
foreach($array as $key => $value) {
    if(preg_match("/記事タイトル/", $value)) {
        $string .= $value;
    }
}

$key = array_search('▼ブログを見る', $array);
$string .= "\n" . $array[$key + 1];
$string .= "\n是非読んでね♡";

$url = "https://menhera.me/nakaji/callback.php";
$postdata = array(
    "type" => "send",
    "message" => json_encode($string)
);

Http::post($url, $postdata);