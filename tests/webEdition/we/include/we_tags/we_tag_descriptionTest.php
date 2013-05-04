<?php
require_once $_SERVER['DOCUMENT_ROOT'].'webEdition/we/include/we_tags/we_tag_description.inc.php';

class we_tag_descriptionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp(){
    }

    protected function tearDown(){
    }

    public function testWeTagTitleAllOptionsDefault() {

		$attributes = array(

            'htmlspecialchars' => false,
            'max' => '',
            'xml' => '',
            'to' => '',
            'nameto' => ''

		);
		$content = 'Some foo description for the Side';
		$result = we_tag_description($attributes, $content);
		$expect = '<meta name="description" content="Some foo description for the Side" />'."\n";
		$this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagTitleAttributeMax() {

		$attributes = array(

            'htmlspecialchars' => false,
            'max' => '12',
            'xml' => '',
            'to' => '',
            'nameto' => ''

		);
		$content = 'Some foo description for the Side';
		$result = we_tag_description($attributes, $content);
		$expect = '<meta name="description" content="Some foo &hellip;" />'."\n";
		$this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagTitleAttributeXml() {

		$attributes = array(

            'htmlspecialchars' => false,
            'max' => '',
            'xml' => '',
            'to' => '',
            'nameto' => ''

		);
		$content = 'Some foo description for the Side';
		$result = we_tag_description($attributes, $content);
		$expect = '<meta name="description" content="Some foo description for the Side" />'."\n";
		$this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagTitleAttrbiteToGlobalNameToFoo() {

        $attributes = array(

            'htmlspecialchars' => false,
            'max' => '',
            'xml' => '',
            'to' => 'global',
            'nameto' => 'Foo'

        );
        $content = 'Some foo description for the Side';
        $result = we_tag_description($attributes, $content);
        $expect = '<meta name="description" content="Some foo description for the Side" />'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);
        $this->assertTrue(isset($GLOBALS['Foo']), 'Foo is not set to $GLOBALS');
        $this->assertTrue($GLOBALS['Foo'] == $expect, 'expected result is not correct in $GLOBALS! $GLOBALS["Foo"] was -> '.$_GET['Foo']);

    }

    public function testWeTagTitleAttrbiteToGetNameToFoo() {

        $attributes = array(

            'htmlspecialchars' => false,
            'max' => '',
            'xml' => '',
            'to' => 'get',
            'nameto' => 'Foo'

        );
        $content = 'Some foo description for the Side';
        $result = we_tag_description($attributes, $content);
        $expect = '<meta name="description" content="Some foo description for the Side" />'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);
        $this->assertTrue(isset($_GET['Foo']), 'Foo is not set to $_GET');
        $this->assertTrue($_GET['Foo'] == $expect, 'expected result is not correct in $_GET! $_GET["Foo"] was -> '.$_GET['Foo']);

    }

}
