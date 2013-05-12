<?php

class we_tag_cssTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp() {

        // Create a dummy CSS file entry in DB
        $_sql = "INSERT INTO tblfile
                (ID, ParentID, Text, Icon, IsFolder, ContentType, CreationDate, ModDate, Path, TemplateID, temp_template_id, Filename, Extension, IsDynamic, IsSearchable, DocType, temp_doc_type, ClassName, Category, temp_category, Deleted, Published, CreatorID, ModifierID, RestrictOwners, Owners, OwnersReadOnly, documentArray, Language, WebUserID, listview, InGlossar)
                VALUES
                (1, 0, 'example.css', 'css.gif', 0, 'text/css', 1366744449, 1366744455, '/example.css', 0, 0, 'example', '.css', 0, 0, 0, 0, 'we_textDocument', '', NULL, 0, 1366744455, 1, 1, 0, '', '', '', 'de_DE', 0, 0, 0)";
        $GLOBALS['DB_WE']->query($_sql);

    }

    protected function tearDown() {

        // clean the db after all tests
        $GLOBALS['DB_WE']->query('TRUNCATE tblFile');

    }

    public function testWeTagCssAllOptionsDefaultIdNotExists() {

        $attributes = array(

            'id' => 99999,
            'rel' => '',
            'title' => '',
            'media' => '',
            'applyto' => '',
            'xml' => '',
            'to' => '',
            'nameto' => ''

        );

        $result = we_tag('css', $attributes);
        $expect = '';
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagCssAllOptionsDefault() {

        $attributes = array(

            'id' => 1,
            'rel' => '',
            'title' => '',
            'media' => '',
            'applyto' => '',
            'xml' => '',
            'to' => '',
            'nameto' => ''

        );

        $result = we_tag('css', $attributes);
        $expect = '<link title="" media="" applyto="" rel="stylesheet" type="text/css" href="/example.css" />'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagCssOnlyTitle() {

        $attributes = array(

            'id' => 1,
            'rel' => '',
            'title' => 'someFooBarFish',
            'media' => '',
            'applyto' => '',
            'xml' => '',
            'to' => '',
            'nameto' => ''

        );

        $result = we_tag('css', $attributes);
        $expect = '<link title="someFooBarFish" media="" applyto="" rel="stylesheet" type="text/css" href="/example.css" />'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagCssOnlyMedia() {

        $attributes = array(

            'id' => 1,
            'rel' => '',
            'title' => '',
            'media' => 'screen,print',
            'applyto' => '',
            'xml' => '',
            'to' => '',
            'nameto' => ''

        );

        $result = we_tag('css', $attributes);
        $expect = '<link title="" media="screen,print" applyto="" rel="stylesheet" type="text/css" href="/example.css" />'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagCssOnlyRel() {

        $attributes = array(

            'id' => 1,
            'rel' => 'foo,stylesheet,bar',
            'title' => '',
            'media' => '',
            'applyto' => '',
            'xml' => '',
            'to' => '',
            'nameto' => ''

        );

        $result = we_tag('css', $attributes);
        $expect = '<link title="" media="" applyto="" rel="foo,stylesheet,bar" type="text/css" href="/example.css" />'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

}
