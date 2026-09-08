<?php
//Script Author: phccoder https://t.me/PHCC0D3r

/*===[PHP Setup]==============================================*/
error_reporting(0);
ini_set('display_errors', 0);

/*===[Security Setup]=========================================*/
include 'init.php';
include 'http.php';
if ($forceAuth && !isset($_SESSION["Auth"])) {
    EchoMessage('DEAD', 'Session Timeout: Please reload the website');
    exit();
}

/*===[Variable Setup]=========================================*/
// Parameters arrive via the AJAX query string (GET).
$cc_info = isset($_GET['cc_info']) ? $_GET['cc_info'] : '';
$sk = isset($_GET['sk']) ? $_GET['sk'] : '';
$telebot = isset($_GET['telebot']) ? $_GET['telebot'] : '';
$tele_msg = isset($_GET['tele_msg']) ? $_GET['tele_msg'] : '';

/*===[CC Info Validation]=====================================*/
if (!$testMode) {
    if ($cc_info == "" || $sk == "") {
        exit();
    }
}

/*===[BugBin Exclusion]=======================================*/
if (!$testMode) {
    $j = 0;
    while ($j < count($bug_bin)) {
        if (substr($cc_info, 0, strlen($bug_bin[$j])) === $bug_bin[$j]) {
            phc_log('preset', 'BIN excluded: ' . $cc_info . ' SK=' . phc_mask_sk($sk));
            EchoMessage('DEAD', $cc_info . ' >> BUG BIN Not Accepted for checking...');
            exit();
        }
        $j++;
    }
}

/*===[Variable Setup]=========================================*/
$i = explode("|", $cc_info);
if (count($i) < 4) {
    EchoMessage('DEAD', $cc_info . ' >> Invalid card format. Use: NUMBER|MM|YYYY|CVV');
    exit();
}
$cc = trim($i[0]);
$mm = trim($i[1]);
$yyyy = trim($i[2]);
$yy = substr($yyyy, 2, 4);
$cvv = trim($i[3]);
$bin = substr($cc, 0, 8);
$last4 = substr($cc, 12, 16);
$email = urlencode(emailGenerate());
$m = ltrim($mm, "0");

if (!preg_match('/^\d{6,19}$/', $cc) || !preg_match('/^\d{2}$/', $mm) || !preg_match('/^\d{4}$/', $yyyy) || !preg_match('/^\d{3,4}$/', $cvv)) {
    phc_log('preset', 'Invalid card format: ' . $cc_info . ' SK=' . phc_mask_sk($sk));
    EchoMessage('DEAD', $cc_info . ' >> Invalid card format. Use: NUMBER|MM|YYYY|CVV');
    exit();
}

phc_log('preset', 'Checking ' . $cc_info . ' SK=' . phc_mask_sk($sk) . ' file=' . basename($_SERVER['PHP_SELF']));

/*===[Identity Setup]=========================================*/
// Try the public randomuser API but fall back to local data so checks
// never break when the API is unreachable.
$location_street = '123 Main St';
$location_city = 'Lexington';
$location_state = 'KY';
$location_postcode = '40502';
$name_full = 'John Doe';

$get = @file_get_contents('https://randomuser.me/api/1.2/?nat=us', false, stream_context_create(['http' => ['timeout' => 4]]));
$infos = json_decode($get, true);
if (is_array($infos) && isset($infos['results'][0]['name']['first'])) {
    $name_first = $infos['results'][0]['name']['first'];
    $name_last = $infos['results'][0]['name']['last'];
    $name_full = $name_first . ' ' . $name_last;
    $location_street = $infos['results'][0]['location']['street'];
    $location_city = $infos['results'][0]['location']['city'];
    $location_postcode = $infos['results'][0]['location']['postcode'];
    $state_map = [
        "alabama" => "AL", "alaska" => "AK", "arizona" => "AZ",
        "arkansas" => "AR", "california" => "CA", "colorado" => "CO",
        "connecticut" => "CT", "delaware" => "DE", "florida" => "FL",
        "georgia" => "GA", "hawaii" => "HI", "idaho" => "ID",
        "illinois" => "IL", "indiana" => "IN", "iowa" => "IA",
        "kansas" => "KS", "kentucky" => "KY", "louisiana" => "LA",
        "maine" => "ME", "maryland" => "MD", "massachusetts" => "MA",
        "michigan" => "MI", "minnesota" => "MN", "mississippi" => "MS",
        "missouri" => "MO", "montana" => "MT", "nebraska" => "NE",
        "nevada" => "NV", "new hampshire" => "NH", "new jersey" => "NJ",
        "new mexico" => "NM", "new york" => "NY", "north carolina" => "NC",
        "north dakota" => "ND", "ohio" => "OH", "oklahoma" => "OK",
        "oregon" => "OR", "pennsylvania" => "PA", "rhode island" => "RI",
        "south carolina" => "SC", "south dakota" => "SD", "tennessee" => "TN",
        "texas" => "TX", "utah" => "UT", "vermont" => "VT",
        "virginia" => "VA", "washington" => "WA", "west virginia" => "WV",
        "wisconsin" => "WI", "wyoming" => "WY"
    ];
    $location_state_lower = strtolower($infos['results'][0]['location']['state']);
    $location_state = $state_map[$location_state_lower] ?? 'KY';
}


/*===[PHP Functions]==========================================*/
function BotForwarder($message,$chat_ID){
    $url = $GLOBALS['token_url']."/sendMessage?chat_id=".$chat_ID."&text=".$message."&parse_mode=HTML";
    file_get_contents($url); 
}
function emailGenerate($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString.'@gmail.com';
}
function EchoMessage($CardStatus, $CardMessage) {
    phc_log('echo', $CardStatus . ' | ' . $CardMessage);
    $MessageStructure = '';
    switch ($CardStatus) {
        case 'CVV LIVE':
            echo '<div class="live_cvv" style="display:none;"><span class="badge">CVV LIVE</span><span> '.$CardMessage.'</span></div>';
            break;
        case 'CCN LIVE':
            echo '<div class="live_ccn" style="display:none;"><span class="badge">CCN LIVE</span><span> '.$CardMessage.'</span></div>';
            break;
        case 'DEAD':
            echo '<div class="dead" style="display:none;"><span class="badge">DEAD</span><span> '.$CardMessage.'</span></div>';
            break;
    }
}

?>