<?php
use models\Line;
use lib\Config;

$app->get('/', function(){
    echo "hello";
});

$app->get('/callback', function(){
    $response = dialogue("はろー");
    file_put_contents("/tmp/kouta.txt", $response->utt);
    Line::api_send_line(Config::read('line.send_id'), json_encode($response->utt));
});

$app->post('/callback', function(){
    $json_string = file_get_contents('php://input');
    $json_object = json_decode($json_string);
    $content = $json_object->result{0}->content;
    $text = $content->text;
    $text = '"'. $text .'"';
    $from = $content->from;
    $message_id = $content->id;
    $content_type = $content->contentType;

    $response = dialogue($text);

    Line::api_send_line(Config::read('line.send_id'), $response->utt);
});

function dialogue($message) {
    $post_data = array('utt' => $message);
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