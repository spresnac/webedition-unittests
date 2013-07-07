<?php
/**
 * Class we_tag_setVarTest
 * @link: http://webedition.org/de/webedition-cms/dokumentation/tag-referenz/setVar
 *
 */

class we_tag_setVarTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp(){
    }

    protected function tearDown(){
    }

    public function testWeTagSetVarBasicOptionsDefault() {

        if (isset($GLOBALS['myFoo'])) {
            unset($GLOBALS['myFoo']);
        }
        $this->assertTrue(!isset($GLOBALS['myFoo']), '$GLOBALS["myFoo"] was set prior to test');

		$attributes = array(

            'to' => 'global',
            'nameto' => 'myFoo'

		);
		we_tag('setVar', $attributes);
		$this->assertTrue(isset($GLOBALS['myFoo']), '$GLOBALS["myFoo"] was not set');
        unset($GLOBALS['myFoo']);

    }

    public function testWeTagSetVarSetGlobalWithValue() {

        if (isset($GLOBALS['myFoo'])) {
            unset($GLOBALS['myFoo']);
        }
        $this->assertTrue(!isset($GLOBALS['myFoo']), '$GLOBALS["myFoo"] was set prior to test');

		$attributes = array(

            'to' => 'global',
            'nameto' => 'myFoo',
            'value' => 'bar'

		);
		we_tag('setVar', $attributes);
		$this->assertTrue(isset($GLOBALS['myFoo']), '$GLOBALS["myFoo"] was not set');
		$this->assertTrue($GLOBALS['myFoo']==='bar', '$GLOBALS["myFoo"] should be "bar", but it is ' . $GLOBALS['myFoo']);
        unset($GLOBALS['myFoo']);

    }

    public function testWeTagSetVarSetPostWithValue() {

        if (isset($_POST['myFoo'])) {
            unset($_POST['myFoo']);
        }
        $this->assertTrue(!isset($_POST['myFoo']), '$_POST["myFoo"] was set prior to test');

		$attributes = array(

            'to' => 'post',
            'nameto' => 'myFoo',
            'value' => 'bar'

		);
		we_tag('setVar', $attributes);
		$this->assertTrue(isset($_POST['myFoo']), '$_POST["myFoo"] was not set');
		$this->assertTrue($_POST['myFoo']==='bar', '$_POST["myFoo"] should be "bar", but it is ' . $_POST['myFoo']);
        unset($_POST['myFoo']);

    }

    public function testWeTagSetVarSetGetWithValue() {

        if (isset($_GET['myFoo'])) {
            unset($_GET['myFoo']);
        }
        $this->assertTrue(!isset($_GET['myFoo']), '$_GET["myFoo"] was set prior to test');

		$attributes = array(

            'to' => 'get',
            'nameto' => 'myFoo',
            'value' => 'bar'

		);
		we_tag('setVar', $attributes);
		$this->assertTrue(isset($_GET['myFoo']), '$_GET["myFoo"] was not set');
		$this->assertTrue($_GET['myFoo']==='bar', '$_GET["myFoo"] should be "bar", but it is ' . $_GET['myFoo']);
        unset($_GET['myFoo']);

    }

    public function testWeTagSetVarSetSessionWithValue() {

        if (isset($_SESSION['myFoo'])) {
            unset($_SESSION['myFoo']);
        }
        $this->assertTrue(!isset($_SESSION['myFoo']), '$_SESSION["myFoo"] was set prior to test');

		$attributes = array(

            'to' => 'session',
            'nameto' => 'myFoo',
            'value' => 'bar'

		);
		we_tag('setVar', $attributes);
		$this->assertTrue(isset($_SESSION['myFoo']), '$_SESSION["myFoo"] was not set');
		$this->assertTrue($_SESSION['myFoo']==='bar', '$_SESSION["myFoo"] should be "bar", but it is ' . $_SESSION['myFoo']);
        unset($_SESSION['myFoo']);

    }


    public function testWeTagSetVarSetGetValueFromPostToGlobal() {

        if (isset($_POST['postname'])) {
            unset($_POST['postname']);
        }
        if (isset($GLOBALS['myFoo'])) {
            unset($GLOBALS['myFoo']);
        }
        $this->assertTrue(!isset($_POST['postname']), '$_POST["postname"] was set prior to test');
        $this->assertTrue(!isset($GLOBALS['myFoo']), '$GLOBALS["myFoo"] was set prior to test');

        $_POST['postname'] = 'myBar';
        $this->assertTrue($_POST['postname']==='myBar', '$_POST["postname"] was not set correct');

        $attributes = array(

            'to' => 'global',
            'nameto' => 'myFoo',
            'from' => 'post',
            'namefrom' => 'postname'

        );
        we_tag('setVar', $attributes);
        $this->assertTrue(isset($GLOBALS['myFoo']), '$GLOBALS["myFoo"] was not set');
        $this->assertTrue($GLOBALS['myFoo']===$_POST['postname'], '$GLOBALS["myFoo"] should now be '.$_POST['postname'].', but it is ' . $GLOBALS['myFoo']);
        unset($GLOBALS['myFoo']);
        unset($_POST['postname']);

    }

    public function testWeTagSetVarSetGetValueFromPostToGlobalWithStriptags() {

        if (isset($_POST['postname'])) {
            unset($_POST['postname']);
        }
        if (isset($GLOBALS['myFoo'])) {
            unset($GLOBALS['myFoo']);
        }
        $this->assertTrue(!isset($_POST['postname']), '$_POST["postname"] was set prior to test');
        $this->assertTrue(!isset($GLOBALS['myFoo']), '$GLOBALS["myFoo"] was set prior to test');

        $_POST['postname'] = 'my\\Bar';
        $this->assertTrue($_POST['postname']==='my\\Bar', '$_POST["postname"] was not set correct');

        $attributes = array(

            'to' => 'global',
            'nameto' => 'myFoo',
            'from' => 'post',
            'namefrom' => 'postname',
            'striptags' => true

        );
        we_tag('setVar', $attributes);
        $this->assertTrue(isset($GLOBALS['myFoo']), '$GLOBALS["myFoo"] was not set');
        $this->assertTrue($GLOBALS['myFoo']==='my\Bar', '$GLOBALS["myFoo"] should now be "my\Bar", but it is ' . $GLOBALS['myFoo']);
        unset($GLOBALS['myFoo']);
        unset($_POST['postname']);

    }

}
