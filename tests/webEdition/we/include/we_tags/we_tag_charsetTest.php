<?php

class we_tag_charsetTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp(){

        if (isset($_GET['Foo'])) {
            unset($_GET['Foo']);
        }
        if (isset($_POST['Foo'])) {
            unset($_POST['Foo']);
        }
        if (isset($GLOBALS['Foo'])) {
            unset($GLOBALS['Foo']);
        }

    }

    protected function tearDown(){
    }

    public function testWeTagCharsetAllOptionsDefault() {

        $attributes = array(
            'defined' => '',
            'xml' => false,
            'to' => 'screen',
            'nameto' => ''
        );
        $expect = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'."\n";
        $result = we_tag('charset', $attributes, 'UTF-8');
        $this->assertTrue($result==$expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagCharsetOnlyOneNonStandartCharset() {

        $attributes = array(
            'defined' => 'ISO-8859-1',
            'xml' => false,
            'to' => 'screen',
            'nameto' => ''
        );
        $expect = '<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">'."\n";
        $result = we_tag('charset', $attributes, 'ISO-8859-1');
        $this->assertTrue($result==$expect, 'expected result is not correct! result was -> '.$result);

    }

    /**
     * Testing that even there is a specific charset not defined in we:charset,
     * calling the tag will result in that desired charset.
     * This is a actual case, when updating a legacy installion, that contains i.e. ISO and
     * after updating we:charset will get a "defined" of "windows", so that the page is not broken!
     */
    public function testWeTagCharsetTestLegacyCharsetFromUpdate() {

        $attributes = array(
            'defined' => 'ISO-8859-1',
            'xml' => false,
            'to' => 'screen',
            'nameto' => ''
        );
        $expect = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'."\n";
        $result = we_tag('charset', $attributes, 'UTF-8');
        $this->assertTrue($result==$expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagCharsetAttributeToGetNameToFoo() {

        $this->assertTrue(!isset($_GET['Foo']), 'Foo is alredy set in $_GET');

        $attributes = array(
            'defined' => '',
            'xml' => false,
            'to' => 'get',
            'nameto' => 'Foo'
        );
        $expect = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'."\n";
        $result = we_tag('charset', $attributes, 'UTF-8');
        $this->assertTrue($result == '', 'result is set, but i should not be when using GET option! result was -> '.$result);
        $this->assertTrue(isset($_GET['Foo']), 'Foo is not set to $_GET');
        $this->assertTrue($_GET['Foo'] == $expect, 'expected result is not correct in $_GET! $_GET["Foo"] was -> '.$_GET['Foo']);

    }

    public function testWeTagCharsetAttributeToGlobalNameToFoo() {

        $this->assertTrue(!isset($GLOBALS['Foo']), 'Foo is alredy set in $GLOBALS');

        $attributes = array(
            'defined' => '',
            'xml' => false,
            'to' => 'global',
            'nameto' => 'Foo'
        );
        $expect = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'."\n";
        $result = we_tag('charset', $attributes, 'UTF-8');
        $this->assertTrue($result == '', 'result is set, but i should not be when using GLOBAL option! result was -> '.$result);
        $this->assertTrue(isset($GLOBALS['Foo']), 'Foo is not set to $GLOBALS');
        $this->assertTrue($GLOBALS['Foo'] == $expect, 'expected result is not correct in $GLOBALS! $GLOBALS["Foo"] was -> '.$GLOBALS['Foo']);

    }

    public function testWeTagCharsetAttributeToPostNameToFoo() {

        $this->assertTrue(!isset($_POST['Foo']), 'Foo is alredy set in $_POST');

        $attributes = array(
            'defined' => '',
            'xml' => false,
            'to' => 'post',
            'nameto' => 'Foo'
        );
        $expect = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'."\n";
        $result = we_tag('charset', $attributes, 'UTF-8');
        $this->assertTrue($result == '', 'result is set, but i should not be when using POST option! result was -> '.$result);
        $this->assertTrue(isset($_POST['Foo']), 'Foo is not set to $_POST');
        $this->assertTrue($_POST['Foo'] == $expect, 'expected result is not correct in $_POST! $_POST["Foo"] was -> '.$_POST['Foo']);

    }

}
