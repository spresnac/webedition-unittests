<?php
require_once $_SERVER['DOCUMENT_ROOT'].'webEdition/we/include/we_global.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'webEdition/we/include/we_tag.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'webEdition/we/include/we_classes/tag/we_tag_tagParser.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'webEdition/we/include/we_tags/we_tag_css.inc.php';

class we_tag_cssTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // Create a dummy CSS file entry in DB
        $_sql = "INSERT INTO tblfile
                (ID, ParentID, Text, Icon, IsFolder, ContentType, CreationDate, ModDate, Path, TemplateID, temp_template_id, Filename, Extension, IsDynamic, IsSearchable, DocType, temp_doc_type, ClassName, Category, temp_category, Deleted, Published, CreatorID, ModifierID, RestrictOwners, Owners, OwnersReadOnly, documentArray, Language, WebUserID, listview, InGlossar)
                VALUES
                (1, 0, 'example.css', 'css.gif', 0, 'text/css', 1366744449, 1366744455, '/example.css', 0, 0, 'example', '.css', 0, 0, 0, 0, 'we_textDocument', '', NULL, 0, 1366744455, 1, 1, 0, '', '', '', 'de_DE', 0, 0, 0)";
        mysql_query($_sql);
    }

    protected function tearDown()
    {
        //mysql_query('TRUNCATE tblFile');
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

        $result = we_tag_css($attributes);
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

        $result = we_tag_css($attributes);
        $expect = '';
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }


}
