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

}
