<?php
/**
 * Created by IntelliJ IDEA.
 * User: adi
 * Date: 10/18/13
 * Time: 3:02 PM
 * To change this template use File | Settings | File Templates.
 */

require('/home/adi/jquery_plugins/rating/classes/RatingDB.php');
class RatingDBTest extends PHPUnit_Framework_TestCase {

    protected $ratingDB;

    protected function setUp () {
        $this->ratingDB = new RatingDB();
    }

    /**
     * @dataProvider setProvider
     */
    public function testDuplicate ($item_id, $user_id, $rating, $counter) {
        $this->assertFalse($this->ratingDB->__duplicate($item_id, $user_id));
    }

    /**
     * @depends testDuplicate
     * @dataProvider setProvider
     */
    public function testSet ($item_id, $user_id, $rating, $counter) {
        $this->assertEquals($this->ratingDB->set($item_id, $user_id, $rating), array('rating' => $rating, 'counter' => $counter));
    }

    /**
     * @dataProvider setBadDataProvider
     */
    public function testSetFail ($item_id, $user_id, $rating, $counter) {
        $this->assertFalse($this->ratingDB->set($item_id, $user_id, $rating));
    }

    /**
     * @depends testSet
     * @dataProvider getProvider
     */
    public function testGet ($item_id, $user_id, $rating, $counter) {
        $this->assertEquals($this->ratingDB->get($item_id), array('rating' => $rating, 'counter' => $counter));
        $this->assertFalse($this->ratingDB->get(10));
    }

    public function testNumericValueTest () {
        $this->assertTrue($this->ratingDB->__numeric(5));
    }

    public function testNumericArrayTest () {
        $this->assertTrue($this->ratingDB->__numeric(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0)));
    }

    public function setProvider () {
        return array(
            array(1, 1, 5, 1),
            array(1, 2, 5, 2),
            array(1, 3, 5, 3),
            array(1, 4, 5, 4),
            array(1, 5, 5, 5),
            array(1, 6, 5, 6),
            array(1, 7, 5, 7),
            array(1, 8, 5, 8),
            array(1, 9, 5, 9)
        );
    }

    public function setBadDataProvider () {
        return array(
            array('a', 1, 5, 1),
            array('a', 2, 5, 2),
            array('a', 3, 5, 3),
            array('a', 4, 5, 4),
            array('a', 5, 5, 5),
            array('a', 6, 5, 6),
            array('a', 7, 5, 7),
            array('a', 8, 5, 8),
            array('a', 9, 5, 9)
        );
    }

    public function getProvider () {
        return array(
            array(1, 1, 5, 9),
            array(1, 2, 5, 9),
            array(1, 3, 5, 9),
            array(1, 4, 5, 9),
            array(1, 5, 5, 9),
            array(1, 6, 5, 9),
            array(1, 7, 5, 9),
            array(1, 8, 5, 9),
            array(1, 9, 5, 9)
        );
    }
}
