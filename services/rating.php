<?php
/**
 * Created by IntelliJ IDEA.
 * User: adi
 * Date: 10/17/13
 * Time: 1:54 PM
 * To change this template use File | Settings | File Templates.
 */
header('Content-type: application/json');
if((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') || !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) { // if it's isn't AJAX request
    die(json_encode(array("error" => array("message" => "Invalid request"))));
}
// params whitelist
$whiteList = array('cmd', 'item_id', 'user_id', 'rating');
foreach ($_GET as $key => $var) { // only alowed params
    if(!in_array($key, $whiteList) || !preg_match('/^[a-z_]+$/', $key)) { // if invalid param
        unset($_GET[$key]);
    }
}
extract($_GET);
// actions whitelist
$cmdWhitelist = array('get', 'set');
if(!in_array($cmd, $cmdWhitelist) || !preg_match('/^[a-z_]+$/', $cmd)) // if invalid command
    die(json_encode(array("error" => array("message" => "Invalid command"))));
// include handler class
include('../classes/RatingDB.php');
$db = new RatingDB();

if($cmd == 'get') { // handle get rating action
    if (empty($item_id) || !preg_match('/^\d+$/', $item_id)) // validate parameters
        die(json_encode(array("error" => array("message" => "Invalid article"))));
    // if it has ratings get them, if not construct them
    $init = ($tmp = $db->get($item_id)) ? $tmp : array('rating' => 0, 'counter' => 0);
    die(json_encode(array("success" => array("data" => $init))));
}
if($cmd == 'set') { // handle new rating action
    if (empty($item_id) || empty($user_id) || empty($rating) || !preg_match('/^\d+$/', $item_id) || !preg_match('/^\d+$/', $user_id) || !preg_match('/^(1|2|3|4|5)$/', $rating)) // validate params
        die(json_encode(array("error" => array("message" => "Invalid parameters"))));
    $new = $db->set($item_id, $user_id, $rating); // add the rating; return false on error
    die(json_encode(array("success" => array("data" => array('rating' => !!$new ? $new['rating'] : $rating, "counter" => !!$new ? $new['counter'] : 1))))); // if error return old values
}
