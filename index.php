<?php
namespace Google\Cloud\Samples\Dialogflow;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
require __DIR__ . '/vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// All API's
$telegram_api = "TELEGRAM API";
$woal = "WOLAL";
$chat_id = "CHAT ID";
$gcloud_session = "DIALOGFLOW SESSION";
$projectId = 'PROJECT ID';

$data = json_decode(file_get_contents("php://input"));
//bot name
$botname = "Rose";
$myPhoneNumber = "My Number";

if (!empty($data->query) && !empty($data->query->sender) && !empty($data->query->message)) {
    $sender = $data->query->sender;
    $text1 = str_replace($botname, "", $data->query->message);
    $text = str_replace(strtolower($botname), "", $text1);
    http_response_code(200);
    $newcf = fopen("newcontact.txt", "a");
    $newcontacts = file_get_contents("newcontact.txt");
    $blockedf = fopen("blocked.txt", "a");
    $blocked = file_get_contents("blocked.txt");
    $help = fopen("help.txt", "a");
    $helpf = file_get_contents("help.txt");
    $welcome = fopen("welcome.txt", "a");
    $welcomef = file_get_contents("welcome.txt");
    //$sessionsClient
    $sessionsClient = $gcloud_session;
    $languageCode = 'en-US';
    // new session
    $test = array('credentials' => 'secretjsonfile.json');
    $sessionsClient = new SessionsClient($test);
    $session = $sessionsClient->sessionName($projectId, $gcloud_session ? : uniqid());
    // create text input
    $textInput = new TextInput();
    $textInput->setText($text);
    $textInput->setLanguageCode($languageCode);
    // create query input
    $queryInput = new QueryInput();
    $queryInput->setText($textInput);
    // get response and relevant info
    $response = $sessionsClient->detectIntent($session, $queryInput);
    $queryResult = $response->getQueryResult();
    $queryText = $queryResult->getQueryText();
    $intent = $queryResult->getIntent();
    $displayName = $intent->getDisplayName();
    $confidence = $queryResult->getIntentDetectionConfidence();
    $fulfilmentText = $queryResult->getFulfillmentText();
    $key = 0;
    $params = [];
    if ($response->getQueryResult()->getParameters()->getFields()->count()) {
        foreach ($response->getQueryResult()->getParameters()->getFields() as $key => $value) {
            $params[$key] = $value->serializeToJsonString();
        }
    }
    if ($key == "person") {
        $data = $params['person'];
        $dejson = json_decode($data, true);
        if ($dejson != "") {
            $name = $dejson['name'];
        } else {
            $name = "user";
        }
    } else {
        $name = "user";
    }
    $fulfilmentText = str_replace(":+1:", ".", $fulfilmentText);
    $fulfilmentText = str_replace("rose", $botname, $fulfilmentText);
    $fulfilmentText = str_replace("%nick%", " user ", $fulfilmentText);
    $fulfilmentText = str_replace("\u0027", "'", $fulfilmentText);
    if (!str_contains($sender, "+")) {
        $fulfilmentText = str_replace("user", $sender, $fulfilmentText);
    }
    $sender = str_replace("My Number", "Boss", $sender);
    if ($displayName == "I need help") {
        echo json_encode(array("replies" => array(array("message" => "*$botname* \r\nHi $sender  ,This is a $botname created by sumith"), array("message" => "*Help*\r\n1. *get-location* (```To get Sumith last location ```).\r\n2. *call-sumith* (```Let sumith know you want to chat with him.```) \r\n3. *get-battery-status* (```To get the sumith phone battery percentage and phone temperature```)."))));
    } elseif (!str_contains($welcomef, $sender)) {
        if (!str_contains($sender, "+")) {
            echo json_encode(array("replies" => array(array("message" => "*$botname* \r\nWelcome *$sender*  ,This is a $botname created by sumith")
            /*array("message" => "*Help*\r\n1. *get-location* (```To get Sumith last location ```).\r\n2. *call-sumith* (```Let sumith know you want to chat with him.```) \r\n3. *get-battery-status* (```To get the sumith phone battery percentage and phone temperature```).")*/
            )));
            $myfilew = fopen("welcome.txt", "a");
            fwrite($myfilew, $sender);
            fwrite($myfilew, "\r\n");
            fclose($myfilew);
        } else {
            echo json_encode(array("replies" => array(array("message" => "*$botname* \r\nWelcome *user*  ,This is a $botname created by sumith"),
            /*array("message" => "*Help*\r\n1. *get-location* (```To get Sumith last location ```).\r\n2. *call-sumith* (```Let sumith know you want to chat with him.```) \r\n3. *get-battery-status* (```To get the sumith phone battery percentage and phone temperature```).")*/
            )));
            $myfilew = fopen("welcome.txt", "a");
            fwrite($myfilew, $sender);
            fwrite($myfilew, "\r\n");
            fclose($myfilew);
        }
    } elseif ($displayName == "call.sumith") {
        echo json_encode(array("replies" => array(array("message" => "*$botname* \r\n _Calling sumith...._"), array("message" => "*$botname* \r\n_wait a minute...._"), array("message" => "*$botname* \r\n_If there is no response within 1 minute sumith may be busy_"))));
        $url = "https://api.telegram.org/bot$telegram_api/sendMessage?chat_id=$chat_id&text=Call Request from $sender";
        file_get_contents($url);
    } elseif ($displayName == "mylocation") {
        if (str_contains($newcontacts, $sender)) {
            $ipdata = file_get_contents("http://ip-api.com/line/?fields=524793");
            echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n _Hi there! 🙌🙌🙌,_\r\n _you are not in Sumith contact list,only people from his contact list can access his location_"), array("message" => "*$botname*\r\n _Even though you sent a request to save your number it wasn't saved in  in sumith's phone_\r\n_requested phone numbers will be saved in server and a notification will be sent to my boss_"), array("message" => "*$botname*\r\n _you can only see his approximate location_\r\n\r\n$ipdata"))));
        } elseif (str_contains($sender, "+")) {
            echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n _Hi there! 🙌🙌🙌,_\r\n _you are not in Sumith contact list,only people from his contact list can access his location_"))));
        } else {
            $locationdata = file_get_contents('/data/data/com.termux/files/home/rosebot/location/location.txt');
            $time = file_get_contents("/data/data/com.termux/files/home/rosebot/location/time.txt");
            echo json_encode(array("replies" => array(array("message" => "*$botname*\r\nSumith Last Location at\r\n$time"), array("message" => "*$botname*\r\n$locationdata"))));
        }
    } elseif ($displayName == "battery.status") {
        exec('termux-battery-status 2>&1', $output);
        $data = $output['2'] . "\r\n" . $output['5'];
        $data = str_replace(" ", "", $data);
        $data = str_replace(":", "= ", $data);
        $data = str_replace(",", "", $data);
        $data = str_replace('"', ' ', $data);
        $data = str_replace('%0A%20', ',', $data);
        $data = str_replace('te', 'Phone Te', $data);
        echo json_encode(array("replies" => array(array("message" => "*$botname* \r\n Battery  $data °C"))));
    } elseif (str_contains($sender, "+") && !str_contains($newcontacts, $sender)) {
        $num = $sender;
        if (!file_exists("numbers/" . $num . ".txt")) {
            $newf = fopen("numbers/" . $num . ".txt", "w");
            fwrite($newf, "0");
            fclose($newf);
        }
        $id = file_get_contents("numbers/" . $num . ".txt");
        switch ($id) {
            case 0:
                echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n_I haven't seen your contact in sumith's contact list_"), array("message" => "*$botname*\r\n_who are you ?_"))));
                $myfile = fopen("numbers/" . $num . ".txt", "w");
                fwrite($myfile, "1");
                fclose($myfile);
                break;
            case 1:
                if ($key == "person") {
                    if ($name == "sumith") {
                        echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n _Okay ,Hey *sumith* is my boss name. Your name is *sumith* is that right if not send a message as *no*._\r\n\r\n_1. Yes_\r\n_2 .no_"))));
                        $myfile = fopen("numbers/" . $num . ".txt", "w");
                        fwrite($myfile, "2");
                        fclose($myfile);
                        $uname = fopen("numbers/" . $num . ".name.txt", "w");
                        fwrite($uname, $name);
                        fclose($uname);
                        break;
                    } else {
                        echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n _Okay , Your name is *$name* is that right._\r\n\r\n_1. Yes_\r\n_2 .no_"))));
                        $myfile = fopen("numbers/" . $num . ".txt", "w");
                        fwrite($myfile, "2");
                        fclose($myfile);
                        $uname = fopen("numbers/" . $num . ".name.txt", "w");
                        fwrite($uname, $name);
                        fclose($uname);
                        break;
                    }
                    /* echo json_encode(array("replies" => array(
                    array("message" => "*$botname*\r\n _Okay , Your name is *$name* is that right._\r\n\r\n_1. Yes_\r\n_2 .no_")
                    )));
                    $myfile = fopen("numbers/".$num.".txt","w");
                    fwrite($myfile,"2");
                    fclose($myfile);
                    $uname = fopen("numbers/".$num.".name.txt","w");
                    fwrite($uname,$name);
                    fclose($uname);
                    break;*/
                } else {
                    echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n _$fulfilmentText ._"), array("message" => "*$botname*\r\n _What is your name ?_"))));
                    break;
                }
            case 2:
                if ($displayName == "name.conformation.yes" || $displayName == "smalltalk.common.YES/OK" || $text == "1") {
                    $name = file_get_contents("numbers/" . $num . ".name.txt");
                    echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n _Wait a minute..._"), array("message" => "*$botname*\r\n _let me save you contact,it may take less than 1 minute_"), array("message" => "*$botname*\r\n _✅ Your contact saved_"))));
                    $url = "https://api.telegram.org/bot$telegram_api/sendMessage?chat_id=$chat_id&text=New%20Contact%0A%0AName%20%20%20%20%20%3A%20$name%0ANumber%20%3A$sender";
                    file_get_contents($url);
                    $myfile = fopen("newcontact.txt", "a");
                    fwrite($myfile, $num);
                    fwrite($myfile, "\r\n");
                    fclose($myfile);
                    break;
                } elseif ($displayName == "name.conformation.no" || $displayName == "smalltalk.common.NO" || $text == "2") {
                    echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n  _Ok cancelled_"))));
                    $myfile = fopen("numbers/" . $num . ".txt", "w");
                    fwrite($myfile, "0");
                    fclose($myfile);
                    break;
                } else {
                    echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n  _You should give a reply as yes or no_\r\n_Ex:- (Yes ,no,1 or 2)_"))));
                    break;
                }
        }
    } elseif ($displayName == "social.accounts") {
        $social = $params[$key];
        $social = str_replace('"', '', $social);
        $sociall = strtolower($social);
        echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n*Sumith $social Details*\r\nhttps://$sociall.com/sumithemmadi\r\nUsername:  @sumithemmadi"))));
    } elseif ($displayName == "mynumber") {
        $temp = $params[$key];
        $temp = str_replace('"', '', $temp);
        echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n_Here is sumith $temp number: $myPhoneNumber_"))));
    } elseif ($displayName == "Default Fallback Intent" || $displayName == "user.name") {
        $textmsg = urlencode($text);
        $botjson = file_get_contents("http://api.wolframalpha.com/v2/query?appid=$woal&input=$textmsg&output=json");
        $data = json_decode($botjson);
        if ($data->queryresult->success == true) {
            $botmsg = $data->queryresult->pods[1]->subpods[0]->plaintext;
            $botmsg = str_replace("Wolfram|Alpha", "Sumith", $botmsg);
            $botmsg = str_replace("Stephen", "Sumith", $botmsg);
            $botmsg = str_replace("Wolfram", "Emmadi", $botmsg);
            echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n$botmsg"))));
        } else {
            echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n_$fulfilmentText ._"))));
        }
    } else {
        echo json_encode(array("replies" => array(array("message" => "*$botname*\r\n_$fulfilmentText ._"))));
    }
} else {
    http_response_code(400);
    // send
    echo json_encode(array("replies" => array(array("message" => "Error ❌"), array("message" => "JSON Error"))));
}
?>
