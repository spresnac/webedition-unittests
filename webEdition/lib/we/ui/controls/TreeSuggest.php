<?php

/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * file for generating json output for tree nodes
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
/**
 * Includes autoload function
 */
include_once (dirname(dirname(__FILE__)) . '/../../we/core/autoload.php');

we_core_Permissions::protect();

/**
 * get json output
 */
$type = "application/json";
header("Content-Type: " . $type);
$ZW = array();
foreach($_GET as $gKey => $gValue ){
	$ZW[strip_tags($gKey)]=$gValue;
}
$_GET=$ZW;

if(isset($_GET["id"])){
	$id = urlencode($_GET["id"]);
}
if(isset($_GET["sessionname"])){
	$sessionName = urlencode($_GET["sessionname"]);
}
if(isset($_GET["table"])){
	$table = urlencode($_GET["table"]);
}
if(isset($_GET["close"])){
	$close = urlencode($_GET["close"]);
}
if(isset($_GET["datasource"])){
	$datasource = urlencode($_GET["datasource"]);
}
if(isset($_GET["treeclass"])){
	$treeclass = urlencode($_GET["treeclass"]);
	$tree = new $treeclass();
}

if(isset($sessionName) && $sessionName !== '' && isset($id) && $id !== ''){
	/**
	 * get the session data (open nodes) of the tree
	 */
	$session = new Zend_Session_Namespace($sessionName);

	if(isset($close)){
		//if id exists
		if(FALSE !== ($key = array_search($id, $session->openNodes))){
			//if closing node
			if($close){
				unset($session->openNodes[$key]);
			}
		} else{
			//if opening node
			if(!$close){
				array_push($session->openNodes, $id);
			}
		}

		return;
	}

	if(isset($table) && $table !== '' && isset($datasource) && $datasource == 'table' && is_object($tree)){

		$nodes = $tree->doSelect($table, $id);

		/**
		 * write json output in $response
		 */
		$response = '{"ResultSet":{"Result":[';

		if(!empty($nodes)){
			$nodesCount = count($nodes);

			$m = 0;
			foreach($nodes as $k => $v){
				$m++;
				$response .= '"' . oldHtmlspecialchars($v['Text']) . '"';
				if($m < $nodesCount)
					$response .= ',';
			}

			$response .= '],"Id":[';

			$m = 0;
			foreach($nodes as $k => $v){
				$m++;
				$response .= '' . $v['ID'] . '';
				if($m < $nodesCount)
					$response .= ',';
			}
			/*
			  $response .= '],"ContentType":[';

			  $m = 0;
			  foreach ($nodes as $k => $v) {
			  $m++;
			  $response .= '"'.$v['ContentType'].'"';
			  if($m<$nodesCount) $response .= ',';
			  }
			 */
			$response .= '],"LabelStyle":[';

			$m = 0;
			foreach($nodes as $k => $v){
				$m++;
				$labelType = $tree->getTreeIconClass($v['ContentType']);
				$response .= '"' . $labelType . '"';
				if($m < $nodesCount)
					$response .= ',';
			}

			$response .= '],"Status":[';

			$m = 0;
			foreach($nodes as $k => $v){
				$m++;
				$response .= '"' . $v['Status'] . '"';
				if($m < $nodesCount)
					$response .= ',';
			}

			$response .= '],"Published":[';

			$m = 0;
			foreach($nodes as $k => $v){
				$m++;
				$response .= '"' . $v['Published'] . '"'; //we_util_Strings::p_r($v['Published']);
				if($m < $nodesCount)
					$response .= ',';
			}

			$response .= '],"Classes":[';

			$m = 0;
			foreach($nodes as $k => $v){
				$m++;
				$outClasses = array();
				if($v['Published'] == 0){
					$outClasses[] = 'unpublished';
				}
				if($v['Status'] != ''){
					$outClasses[] = $v['Status'];
				}
				if(!empty($outClasses)){
					$ClassesStr = implode(' ', $outClasses);
					$ClassesStr = trim($ClassesStr, ' ');
				} else{
					$ClassesStr = '';
				}
				$response .= '"' . $ClassesStr . '"'; //we_util_Strings::p_r($v['Published']);
				if($m < $nodesCount)
					$response .= ',';
			}
			$response .= '],"open":[';

			$m = 0;
			foreach($nodes as $k => $v){
				$m++;
				if(in_array($v['ID'], $session->openNodes)){
					$response .= 'true';
				} else{
					$response .= 'false';
				}
				if($m < $nodesCount)
					$response .= ',';
			}
		}

		$response .= ']}}';

		print $response;
	}
	else{
		return;
	}
} else{
	return;
}
?>
