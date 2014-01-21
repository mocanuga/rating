<?php
/**
 * Created by IntelliJ IDEA.
 * User: adi
 * Date: 10/17/13
 * Time: 4:01 PM
 * To change this template use File | Settings | File Templates.
 *
 * Database model
 *
 * CREATE TABLE IF NOT EXISTS `vote` (
 *     `item_id` int(10) NOT NULL,
 *     `counter` int(8) NOT NULL DEFAULT '0',
 *     `rating` int(1) NOT NULL DEFAULT '0',
 *     `timestamp` int(10) NOT NULL,
 *     PRIMARY KEY (`item_id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf32;
 *
 * CREATE TABLE IF NOT EXISTS `vote_voter` (
 *     `user_id` int(11) NOT NULL,
 *     `item_id` int(11) NOT NULL,
 *     `rating` int(1) NOT NULL DEFAULT '0',
 *     `timestamp` int(10) NOT NULL,
 *     UNIQUE KEY `indexed` (`user_id`,`item_id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf32;
 */
include('DBase.php');
final class RatingDB extends DBase {
    public function __construct() {
        /*$trace = debug_backtrace();
        if($trace[1]['class'] == 'RatingDBTest' && $trace[1]['function'] == 'setUp') {
            parent::exec("TRUNCATE TABLE `vote`");
            parent::exec("TRUNCATE TABLE `vote_voter`");
        }*/
    }
    public function get($item_id) {
        if(!$this->__numeric($item_id))
            return false;
        $data = parent::query("SELECT `counter`, `rating` FROM `vote` WHERE `item_id`='{$item_id}'")->fetchAll(PDO::FETCH_ASSOC);
        if(isset($data[0]))
            return $this->getRating($data[0]['rating'], $data[0]['counter']);
        return false;
    }
    public function set($item_id, $user_id = null, $rating = null) {
        if(!$this->__numeric(func_get_args())) // only numeric data allowed
            return false;
        if ($this->__duplicate($item_id, $user_id) || empty($rating))
            return $this->get($item_id);
        $now = time();
        return
            parent::query("INSERT INTO `vote` SET `item_id`='{$item_id}', `counter`='1', `rating`='{$rating}' ON DUPLICATE KEY UPDATE `counter`=`counter`+'1', `rating`=`rating`+'{$rating}'") &&
            parent::query("INSERT INTO `vote_voter` SET `item_id`='{$item_id}', `user_id`='{$user_id}', `rating`='{$rating}', `timestamp`='{$now}'") ?
            $this->get($item_id) :
            false;
    }
    public function __duplicate ($item_id, $user_id = null) {
        $duplicate = parent::query("SELECT COUNT(*) FROM `vote_voter` WHERE `item_id`='{$item_id}' AND `user_id`='{$user_id}'")->fetch(PDO::FETCH_ASSOC);
        return (bool) array_shift($duplicate);
    }
    public function __numeric ($data) {
        $rule = '/^\d+$/';
        if(is_array($data)) {
            $valid = true;
            foreach($data as $field)
                if(!preg_match($rule, $field)) $valid = false;
            return $valid;
        }
        return (bool)preg_match($rule, $data);
    }
    public function getRating($rating, $counter) {
        return array('rating' => intval(@round($rating/$counter,1)), 'counter' => $counter);
    }
}
