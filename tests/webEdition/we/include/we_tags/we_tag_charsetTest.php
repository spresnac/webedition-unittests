<?php

class we_tag_charsetTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp(){
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

    public function testWeTagCharsetOnlyOneNonStandartCharsetGivingWrongSelectedCharset() {

        $attributes = array(
            'defined' => 'ISO-8859-1',
            'xml' => false,
            'to' => 'screen',
            'nameto' => ''
        );
        $expect = '<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">'."\n";
        $result = we_tag('charset', $attributes, 'UTF-8');
        $this->assertTrue($result==$expect, 'expected result is not correct! result was -> '.$result);

    }
}
