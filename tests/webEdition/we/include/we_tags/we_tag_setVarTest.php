<?php

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
		$result = we_tag('setVar', $attributes);
		$this->assertTrue(isset($GLOBALS['myFoo']), '$GLOBALS["myFoo"] was not set');

    }

}
