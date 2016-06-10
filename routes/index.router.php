<?php
use models\Line;
use models\Mail;
use models\Http;
use lib\Config;

$app->get('/', function(){
    echo "hello";
});

$app->get('/callback', function(){
    $response = dialogue("はろー");
    Line::api_send_line(Config::read('line.send_id'), $response->utt);
});

$app->post('/callback', function(){
    $type = $_POST['type'] ?? 'default';
    switch ($type) {
        case "default":
            $text = defaultTalk();
            break;
        case "send":
            $text = $_POST['message'];
            break;
        default:
            $text = "エラー";

    }

    Line::api_send_line(Config::read('line.send_id'), $text);
});

$app->post('/mailReception', function(){
    $mail = Mail::parseMailData(file_get_contents('php://input'));
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
        "message" => $string
    );
    Http::post($url, $postdata);
});

function defaultTalk (){
    $json_string = file_get_contents('php://input');
    $json_object = json_decode($json_string);
    $content = $json_object->result{0}->content;
    $text = $content->text;
    $text = '"'. $text .'"';
    $from = $content->from;
    $message_id = $content->id;
    $content_type = $content->contentType;

    $redis = new Predis\Client(array(
            "scheme" => "tcp",
            "host" => "127.0.0.1",
            "port" => 6379)
    );
    $context = $redis->get($from);
    $response = dialogue($text, $context);
    $redis->set($from, $response->context);
    return $response->utt;
}

function dialogue($message, $context) {
    $post_data = array(
        'utt' => $message,
        'context' => $context,
        "t" => "20"
    );
    // DOCOMOに送信
    $ch = curl_init("https://api.apigw.smt.docomo.ne.jp/dialogue/v1/dialogue?APIKEY=". Config::read('docomo.api_key'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json; charser=UTF-8"
    ]);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
}