<?php
namespace models;
require_once 'Mail/mimeDecode.php';

class Mail
{
    public static function parseMailData($data){
        // メールデータ取得
        $params['include_bodies'] = true;
        $params['decode_bodies']  = true;
        $params['decode_headers'] = true;
        $params['input'] = $data;
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
        $mailData['to'] = $mail;

        /*
         *「$structure->headers['to']」で送信元のメールアドレスも取得できます。
         */

        // 件名を取得
        $mailData['subject'] = $structure->headers['subject'];

        switch(strtolower($structure->ctype_primary)){
            case "text": // シングルパート(テキストのみ)
                $diary_body =  mb_convert_encoding($structure->body, $charset, $charset_from );
                break;
            case "multipart":  // マルチパート(画像付き)
                foreach($structure->parts as $part){
                    switch(strtolower($part->ctype_primary)){
                        case "text": // テキスト
                            mb_convert_encoding($structure->body, $charset, $charset_from );
                            break;
                    }
                }
                break;
            default:
                $diary_body = "";
        }

        $mailData['body'] = $diary_body;
        return $mailData;
    }
}