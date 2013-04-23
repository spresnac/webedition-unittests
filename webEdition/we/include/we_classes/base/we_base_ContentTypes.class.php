<?php

/**
 * webEdition CMS
 *
 * $Rev: 5661 $
 * $Author: mokraemer $
 * $Date: 2013-01-29 22:17:38 +0100 (Tue, 29 Jan 2013) $
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_base_ContentTypes{

	const CLASS_FOLDER_ICON = 'class_folder.gif';
	const FOLDER_ICON = 'folder.gif';
	const IMAGE_ICON = 'image.gif';
	const LINK_ICON = 'link.gif';

	private $ct;

	public function __construct(){
		$charset = defined('WE_BACKENDCHARSET') ? WE_BACKENDCHARSET : 'UTF-8';
		$this->ct = array(
// Content Type for Images
			'image/*' => array(
				'Extension' => array('.gif', '.jpg', '.jpeg', '.png'),
				'Permission' => 'NEW_GRAFIK',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => self::IMAGE_ICON,
			),
			'text/xml' => array(//this entry must stay before text/html, text/we because filetypes are not distinct
				'Extension' => '.xml',
				'Permission' => 'NEW_TEXT',
				'DefaultCode' => '<?xml version="1.0" encoding="' . $charset . '" ?>',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => self::LINK_ICON,
			),
			'text/html' => array(
				'Extension' => array('.html', '.htm', '.shtm', '.shtml', '.stm', '.php', '.jsp', '.asp', '.pl', '.cgi', '.xml', '.xsl'),
				'Permission' => 'NEW_HTML',
				'DefaultCode' => '<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset="' . $charset . '">
	</head>
	<body>
	</body>
</html>',
				'IsWebEditionFile' => true,
				'IsRealFile' => true,
				'Icon' => 'html.gif',
			),
			'text/webedition' => array(
				'Extension' => array('.html', '.htm', '.shtm', '.shtml', '.stm', '.php', '.jsp', '.asp', '.pl', '.cgi', '.xml'),
				'Permission' => 'NEW_WEBEDITIONSITE',
				'DefaultCode' => '',
				'IsWebEditionFile' => true,
				'IsRealFile' => false,
				'Icon' => 'we_dokument.gif',
			),
			'text/weTmpl' => array(
				'Extension' => '.tmpl',
				'Permission' => 'NEW_TEMPLATE',
				'DefaultCode' => '<!DOCTYPE HTML>
<html dir="ltr" lang="<we:pageLanguage type="language" doc="top" />">
<head>
	<we:title></we:title>
	<we:description></we:description>
	<we:keywords></we:keywords>
	<we:charset defined="UTF-8">UTF-8</we:charset>
</head>
<body>
	<article>
		<h1><we:input type="text" name="Headline" size="60"/></h1>
		<p><b><we:input type="date" name="Date" format="d.m.Y"/></b></p>
		<we:ifNotEmpty match="Image">
			<we:img name="Image" showthumbcontrol="true"/>
		</we:ifNotEmpty>
		<we:textarea name="Content" width="400" height="200" autobr="true" wysiwyg="true" removefirstparagraph="false" inlineedit="true"/>
	</article>
</body>
</html>',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => 'we_template.gif',
			),
			'text/js' => array(
				'Extension' => '.js',
				'Permission' => 'NEW_JS',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'javascript.gif',
			),
			'text/css' => array(
				'Extension' => array('.css', '.less', '.scss', '.sass'),
				'Permission' => 'NEW_CSS',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'css.gif',
			),
			'text/htaccess' => array(
				'Extension' => '',
				'Permission' => 'NEW_HTACCESS',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'htaccess.gif'
			),
			'text/plain' => array(
				'Extension' => '.txt',
				'Permission' => 'NEW_TEXT',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => self::LINK_ICON,
			),
			'folder' => array(
				'Extension' => '',
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => self::FOLDER_ICON,
			),
			'class_folder' => array(
				'Extension' => '',
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => self::CLASS_FOLDER_ICON,
			),
			'application/x-shockwave-flash' => array(
				'Extension' => '.swf',
				'Permission' => 'NEW_FLASH',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'flashmovie.gif',
			),
			'video/quicktime' => array(
				'Extension' => array('.mov', '.moov', '.qt'),
				'Permission' => 'NEW_QUICKTIME',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'quicktime.gif',
			),
			'application/*' => array(
				'Extension' => array('.doc', '.xls', '.ppt', '.zip', '.sit', '.bin', '.hqx', '.exe', '.pdf'),
				'Permission' => 'NEW_SONSTIGE',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => self::LINK_ICON,
			),
			'object' => array(
				'Extension' => '',
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => 'object.gif',
			),
			'objectFile' => array(
				'Extension' => '',
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => 'objectFile.gif',
			),
		);
	}

	public static function inst(){
		static $inst = 0;
		$inst = ($inst ? $inst : new self());
		return $inst;
	}

	public function hasContentType($name){
		return isset($this->ct[$name]);
	}

	public function getContentTypes(){
		return array_keys($this->ct);
	}

	public function getIcon($name, $default = '', $extension = ''){
		if($name == 'application/*'){
			switch(strtolower($extension)){
				case '.pdf' :
					return 'pdf.gif';
				case '.zip' :
				case '.sit' :
				case '.hqx' :
				case '.bin' :
					return 'zip.gif';
				case '.odt':
				case '.ott':
				case '.dot' :
				case '.doc' :
					return 'word.gif';
				case '.ods':
				case '.ots':
				case '.xlt' :
				case '.xls' :
					return 'excel.gif';
				case '.odp':
				case '.otp':
				case '.ppt' :
					return 'powerpoint.gif';
				case '.odg':
				case '.otg':
					return 'odg.gif';
				default:
					return 'prog.gif';
			}
		} else{
			return isset($this->ct[$name]) ? $this->ct[$name]['Icon'] : $default;
		}
	}

	public function getExtension($name){
		return isset($this->ct[$name]) ? $this->ct[$name]['Extension'] : '';
	}

	public function isWEFile($name){
		return isset($this->ct[$name]) ? $this->ct[$name]['IsWebEditionFile'] : false;
	}

	public function getWETypes(){
		$ret = array();
		foreach($this->ct as $name => $type){
			if($type['IsWebEditionFile']){
				$ret[] = $name;
			}
		}
		return $ret;
	}

	public function getDefaultCode($name){
		return isset($this->ct[$name]) ? $this->ct[$name]['DefaultCode'] : '';
	}

	public function getPermission($name){
		return isset($this->ct[$name]) ? $this->ct[$name]['Permission'] : '';
	}

	public function getTypeForExtension($extension){
		foreach($this->ct as $type => $val){
			$ext = $val['Extension'];
			if((is_array($ext) && in_array($extension, $ext)) || $ext == $extension){
				return $type;
			}
		}
		return '';
	}

	public function getFiles(){
		$ret = array();
		foreach($this->ct as $type => $val){
			if($val['IsRealFile']){
				$ret[] = $type;
			}
		}
		return $ret;
	}

}