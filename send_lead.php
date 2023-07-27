<?php

global $apiSendLeadUrl;
$dir_name = dirname(__FILE__);
require_once($dir_name . '/config.php');
require_once($dir_name . '/app.php');
$dbg_mod = is_debug($_debug);
$data_post = $_POST;
$error = '';
if (!isset($data_post['phone']) || !isset($data_post['name'])) {
    $error_message = 'Получены не все данные. Вернитесь и заполните форму.';
    require($dir_name . '/error.php');
    die;
}

$tech_comments = array_get($_POST, 'tech_comments', '');
if (isset($_POST['fieldJson'])) {
    $fieldJson = $_POST['fieldJson'];
    // формируем дополнительные данные для передачи в tech_comments
    $other = array();
    $data_other = array_get($_POST, 'other', array());
    if ($fieldJson == 'other') {
        foreach ($data_other as $field_name => $field_val) {
            $other['other[' . $field_name . ']'] = $field_val;
        }
    } else {
        $other = $data_other;
    }
    if (!empty($other)) {
        $tech_comments = json_encode($other);
    }

}

// объединяем данные в одно поле из нескольких
$formFields = isset($data_post['formFields']) ? json_decode(urldecode($data_post['formFields']), true) : array();
$fieldsCheck = ['name', 'phone', 'address', 'comments', 'tech_comments'];
$dataField = array(
    'name' => array_get($_POST, 'name', ''),
    'phone' => array_get($_POST, 'phone', ''),
    'address' => array_get($_POST, 'address', ''),
    'email' => array_get($_POST, 'email', ''),
    'comments' => array_get($_POST, 'comments', ''),
    'tech_comments' => $tech_comments,
);
foreach ($fieldsCheck as $field_name) {
    $field = array_get($formFields, $field_name, array());
    $new_data = array();
    foreach ($field as $key => $field_list) {
        $new_data[] = array_get($_POST, $field_list);
    }
    if ($new_data) {
        $dataField[$field_name] = implode(" ", $new_data);
    }
}

$phone = normalizePhoneByCountry(@$data_post['country'], @$dataField['phone']);

//$data = array(
//    'key' => $apiKey,
//    'id' => microtime(true), // тут лучше вставить значение, по которому вы сможете индетифицировать свой лид; можно оставить microtime если у вас нет своей crm
//    'offer_id' => $offer_id,
//    'stream_hid' => $stream_hid,
//    'name' => @$dataField['name'],
//    'phone' => $phone,
//    'email' => @$dataField['email'],
//    'comments' => @$dataField['comments'],
//    'tech_comments' => $dataField['tech_comments'],
//    'country' => @$data_post['country'],    // код страны покупателя (RU, UA и т.п.)
//    'address' => $dataField['address'],
//    'tz' => (isset($data_post['timezone_int'])?$data_post['timezone_int']:3),
//    'web_id' => '', // id вебмастера в вашей системе
//    'ip_address' => (isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$_SERVER['REMOTE_ADDR']),
//    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
//
//    "utm_source" => @$data_post['utm_source'],
//    "utm_medium" => @$data_post['utm_medium'],
//    "utm_campaign" => @$data_post['utm_campaign'],
//    "utm_content" => @$data_post['utm_content'],
//    "utm_term" => @$data_post['utm_term'],
//
//    "sub1" => @$data_post['sub1'],
//    "sub2" => @$data_post['sub2'],
//    "sub3" => @$data_post['sub3'],
//    "sub4" => @$data_post['sub4'],
//    "sub5" => @$data_post['sub5'],
//);

//$result = curl_get_contents($apiSendLeadUrl, $data);
//display_debug_info('result', $result);
//$obj = json_decode($result);
if (sendMessage($dataField['name'], $dataField['phone'])) {
    header('Location: success.php');
}

function curl_get_contents($url, $post = null, $head = 0)
{

    $isCurlEnabled = function () {
        return function_exists('curl_version');
    };
    if (!$isCurlEnabled) {
        echo "pls install curl";
        die;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    if ($head) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    } else {
        curl_setopt($ch, CURLOPT_HEADER, 0);
    }

    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;

}

//send message to telegram bot
function sendMessage($name, $phone)
{
    global $apiSendLeadUrl;
    global $chat_id;

    $text = "<i>📩Yangi buyurtma:</i> \n<b>\n🏷️Ismi:</b> " . $name . "\n<b>📞Telefon raqami: </b>" . "<code>" .$phone . "</code>";
    $text = urlencode($text);

    $url = $apiSendLeadUrl . "?chat_id=" . $chat_id . "&text=" . $text . "&parse_mode=html";
    $result = file_get_contents($url);
    $obj = json_decode($result);

    return $obj->ok;

}