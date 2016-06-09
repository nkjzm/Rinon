<?php
use models\Line;

$app->get('/', function(){
    echo "hello";
});

$app->get('/callback', function(){
    file_put_contents("/tmp/kouta.txt", "post");
    $json_string = file_get_contents('php://input');
    $json_object = json_decode($json_string);
    $content = $json_object->result{0}->content;
    $text = $content->text;
    $text = '"'. $text .'"';
    $from = $content->from;
    $message_id = $content->id;
    $content_type = $content->contentType;

    // ユーザ情報取得
    $userData = Line::api_get_user_profile_request($from);

    Line::api_send_line(Config::read('line.send_id'), $text);
});

$app->post('/callback', function($text){
    file_put_contents("/tmp/kouta.txt", "post");
    Line::api_send_line(Config::read('line.send_id'), $text);
});