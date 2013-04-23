<?php
/**
 * webEdition CMS
 *
 * $Rev: 2633 $
 * $Author: mokraemer $
 * $Date: 2011-03-08 01:16:50 +0100 (Tue, 08 Mar 2011) $
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
 * @package    webEdition_toolfactory
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

$translate = we_core_Local::addTranslation('apps.xml');

$dialog = new we_ui_dialog_YesNoCancelDialog();


$dialog->setMessage($translate->_('The document has been changed.') . "\n" . $translate->_('Would you like to save your changes?'));
$dialog->setYesAction("weCmdController.fire(dialog.args.yesCmd);");
$dialog->setNoAction("weCmdController.fire(dialog.args.noCmd);");

echo $dialog->getHTML();
