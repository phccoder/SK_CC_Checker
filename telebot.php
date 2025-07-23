<?php
/*===[PHP Setup]==============================================*/
error_reporting(0);
ini_set('display_errors', 0);

/*===[Security Setup]=========================================*/
include 'config.php';

/*===[Variable Setup]=========================================*/
$telebot = $_GET['telebot'];

/*===[CC Info Validation]=====================================*/
if($telebot == ""){
    exit();
}


$keyboard = array(
	"inline_keyboard" => array(
		array(
			array("text" => "Visit phccoder Checker", "url" => "https://phccoder.ml/")
		),
		array(
			array("text" => "Contact phccoder", "url" => "https://t.me/PHCC0D3r")
		)
	)
);
$keyboard = json_encode($keyboard, true);
$message = '<b>'.$bot_name.'</b>%0A%0ATest Complete, You may now continue checking cards with our checker.';
phccoderSendMessage($message,$keyboard);

/*===[PHP Functions]==========================================*/
function phccoderSendMessage($message, $keyboard){
    $url = $GLOBALS['token_url']."/sendMessage?chat_id=".$GLOBALS['telebot']."&text=".$message."&parse_mode=HTML&reply_markup=".$keyboard;
    file_get_contents($url); 
}
?>