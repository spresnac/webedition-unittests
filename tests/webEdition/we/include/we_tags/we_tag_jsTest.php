<?php

class we_tag_jsTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp() {

        // Create a dummy CSS file entry in DB
        $_sql = "INSERT INTO tblfile
                (ID, ParentID, Text, Icon, IsFolder, ContentType, CreationDate, ModDate, Path, TemplateID, temp_template_id, Filename, Extension, IsDynamic, IsSearchable, DocType, temp_doc_type, ClassName, Category, temp_category, Deleted, Published, CreatorID, ModifierID, RestrictOwners, Owners, OwnersReadOnly, documentArray, Language, WebUserID, listview, InGlossar)
                VALUES
                (2, 0, 'example.js', 'javascript.gif', 0, 'text/js', 1366485735, 1366485781, '/example.js', 0, 0, 'example', '.js', 0, 0, 0, 0, 'we_textDocument', '', NULL, 0, 1366485781, 1, 1, 0, '', '', '', 'de_DE', 0, 0, 0);";
        $GLOBALS['DB_WE']->query($_sql);

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

    protected function tearDown() {

        // clean the db after all tests
        $GLOBALS['DB_WE']->query('TRUNCATE tblFile');

    }

    public function testWeTagJsAllOptionsDefaultIdNotExists() {

        $attributes = array(

            'id' => 99999,
            'to' => '',
            'nameto' => ''

        );

        $result = we_tag('js', $attributes);
        $expect = '';
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagJsAllOptionsDefault() {

        $attributes = array(

            'id' => 2,
            'to' => '',
            'nameto' => ''

        );

        $result = we_tag('js', $attributes);
        $expect = '<script type="text/javascript" src="/example.js"></script>'."\n";
        $this->assertTrue($result == $expect, 'expected result is not correct! result was -> '.$result);

    }

    public function testWeTagJsToGet() {

        $attributes = array(

            'id' => 2,
            'to' => 'get',
            'nameto' => 'jsFooGet'

        );

        $result = we_tag('js', $attributes);
        $expect = '<script type="text/javascript" src="/example.js"></script>'."\n";
        $this->assertTrue($result == '', 'result is set, but i should not be when using GET option! result was -> '.$result);
        $this->assertTrue(isset($_GET['jsFooGet']), 'jsFooGet is not set to $_GET');
        $this->assertTrue($_GET['jsFooGet'] == $expect, 'expected result is not correct in $_GET! $_GET["jsFooGet"] was -> '.$_GET['jsFooGet']);

    }

    public function testWeTagJsToGlobal() {

        $attributes = array(

            'id' => 2,
            'to' => 'global',
            'nameto' => 'jsFooGlobal'

        );

        $result = we_tag('js', $attributes);
        $expect = '<script type="text/javascript" src="/example.js"></script>'."\n";
        $this->assertTrue($result == '', 'result is set, but i should not be when using GLOBAL option! result was -> '.$result);
        $this->assertTrue(isset($GLOBALS['jsFooGlobal']), 'jsFoo is not set to $GLOBALS');
        $this->assertTrue($GLOBALS['jsFooGlobal'] == $expect, 'expected result is not correct in $GLOBALS! $GLOBALS["jsFooGlobal"] was -> '.$GLOBALS['jsFooGlobal']);

    }


    public function testWeTagJsToPost() {

        $attributes = array(

            'id' => 2,
            'to' => 'post',
            'nameto' => 'jsFooPost'

        );

        $result = we_tag('js', $attributes);
        $expect = '<script type="text/javascript" src="/example.js"></script>'."\n";
        $this->assertTrue($result == '', 'result is set, but i should not be when using POST option! result was -> '.$result);
        $this->assertTrue(isset($_POST['jsFooPost']), 'jsFooGet is not set to $_POST');
        $this->assertTrue($_POST['jsFooPost'] == $expect, 'expected result is not correct in $_GET! $_POST["jsFooPost"] was -> '.$_POST['jsFooPost']);

    }

}
