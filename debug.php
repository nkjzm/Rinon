<?php

require_once 'Mail/mimeDecode.php';
// メールデータ取得
$params['include_bodies'] = true;
$params['decode_bodies']  = true;
$params['decode_headers'] = true;
$params['input'] = file_get_contents("php://stdin");
$params['crlf'] = "\r\n";
$structure = Mail_mimeDecode::decode($params);
$charset = $structure->ctype_parameters['charset'];

if ( $charset ) {
    $charset_from = $charset;
} else {
    $charset_from = 'auto';
}

//送信者のメールアドレスを抽出
$mail = $structure->headers['from'];
$mail = addslashes($mail);
$mail = str_replace('"','',$mail);

//署名付きの場合の処理を追加
preg_match("/<.*>/",$mail,$str);
if($str[0]!=""){
    $str=substr($str[0],1,strlen($str[0])-2);
    $mail = $str;
}
/*
 *「$structure->headers['to']」で送信元のメールアドレスも取得できます。
 */

// 件名を取得
$diary_subject = $structure->headers['subject'];

switch(strtolower($structure->ctype_primary)){
    case "text": // シングルパート(テキストのみ)
        $diary_body =  mb_convert_encoding($structure->body, $CHARSET_TO, $charset_from );
        break;
    case "multipart":  // マルチパート(画像付き)
        foreach($structure->parts as $part){
            switch(strtolower($part->ctype_primary)){
                case "text": // テキスト
                    mb_convert_encoding($structure->body, $CHARSET_TO, $charset_from );
                    break;
            }
        }
        break;
    default:
        $diary_body = "";
}
/*
 * 取得したメールアドレス、タイトル、本文、画像を使用してデータベースなどに取り込む
 */

file_put_contents("/tmp/test.txt", $diary_body);

$url = "https://menhera.me/nakaji/callback.php";
$postdata = array(
    "type" => "send",
    "message" => json_encode($diary_body)
);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
$result = curl_exec($ch);
curl_close($ch);