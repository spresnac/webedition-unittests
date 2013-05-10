<?php
require_once $_SERVER['DOCUMENT_ROOT'].'webEdition/we/include/we_tags/we_tag_title.inc.php';

class we_tag_titleTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp(){
    }

    protected function tearDown(){
    }

    public function testWeTagTitleAllOptionsDefault() {

		$attributes = array(

				'htmlspecialchars' => false,
				'prefix' => '',
				'suffix' => '',
				'delimiter' => ''

		);
		$content = 'myTitle';
		$result = we_tag('title', $attributes, $content);
		$expect = '<title>myTitle</title>'."\n";
		$this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagTitlePrefixOnly() {

        $attributes = array(

            'htmlspecialchars' => false,
            'prefix' => 'somePrefix',
            'suffix' => '',
            'delimiter' => ''

        );
        $content = 'myTitle';
        $result = we_tag('title', $attributes, $content);
        $expect = '<title>somePrefixmyTitle</title>'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagTitlePrefixAndDelimiter() {

        $attributes = array(

            'htmlspecialchars' => false,
            'prefix' => 'somePrefix',
            'suffix' => '',
            'delimiter' => ' - '

        );
        $content = 'myTitle';
        $result = we_tag('title', $attributes, $content);
        $expect = '<title>somePrefix - myTitle</title>'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagTitleSuffixOnly() {

        $attributes = array(

            'htmlspecialchars' => false,
            'prefix' => '',
            'suffix' => 'someSuffix',
            'delimiter' => ''

        );
        $content = 'myTitle';
        $result = we_tag('title', $attributes, $content);
        $expect = '<title>myTitlesomeSuffix</title>'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagTitleSuffixAndDelimiter() {

        $attributes = array(

            'htmlspecialchars' => false,
            'prefix' => '',
            'suffix' => 'someSuffix',
            'delimiter' => ' | '

        );
        $content = 'myTitle';
        $result = we_tag('title', $attributes, $content);
        $expect = '<title>myTitle | someSuffix</title>'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagTitleDelimiterOnly() {

        $attributes = array(

            'htmlspecialchars' => false,
            'prefix' => '',
            'suffix' => '',
            'delimiter' => 'someDelimiter'

        );
        $content = 'myTitle';
        $result = we_tag('title', $attributes, $content);
        $expect = '<title>myTitle</title>'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagTitlePrefixSuffixAndDelimiter() {

        $attributes = array(

            'htmlspecialchars' => false,
            'prefix' => 'isPrefix',
            'suffix' => 'someSuffix',
            'delimiter' => ' -*- '

        );
        $content = 'myTitle';
        $result = we_tag('title', $attributes, $content);
        $expect = '<title>isPrefix -*- myTitle -*- someSuffix</title>'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagTitleHtmlSpecialcharsRewrite() {

        $attributes = array(

            'htmlspecialchars' => true,
            'prefix' => '',
            'suffix' => '',
            'delimiter' => ''

        );
        $content = 'Sänd&burg';
        $result = we_tag('title', $attributes, $content);
        $expect = '<title>Sänd&amp;burg</title>'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagTitleHtmlSpecialcharsNoRewrite() {

        $attributes = array(

            'htmlspecialchars' => false,
            'prefix' => '',
            'suffix' => '',
            'delimiter' => ''

        );
        $content = 'Sänd&burg';
        $result = we_tag('title', $attributes, $content);
        $expect = '<title>Sänd&burg</title>'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeParseTagTitle() {

        $attributes = array(

            'htmlspecialchars' => false,
            'prefix' => '',
            'suffix' => '',
            'delimiter' => ''

        );

        $content = 'FooBarFishForelle';
        $result = we_parse_tag_title($attributes, $content);
        $expect = "<?php printElement(we_tag('title',array('htmlspecialchars'=>'','prefix'=>'','suffix'=>'','delimiter'=>'',),\"FooBarFishForelle\"));?>";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

}
