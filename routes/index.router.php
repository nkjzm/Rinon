<?php
use models\Line;
use lib\Config;

$app->get('/', function(){
    echo "hello";
});

$app->get('/callback', function(){
    file_put_contents("/tmp/kouta.txt", Config::read('line.send_id'));
    Line::api_send_line(Config::read('line.send_id'), "aaaa");
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

    Line::api_send_line(Config::read('line.send_id'), "aaaa");
});