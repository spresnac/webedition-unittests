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
 * @package    webEdition_javamenu
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


$we_menu_navigation['000100']['text'] =g_l('navigation','[navigation]');
$we_menu_navigation['000100']['parent'] = '000000';
$we_menu_navigation['000100']['perm'] = '';
$we_menu_navigation['000100']['enabled'] = '1';

$we_menu_navigation['000200']['text'] = g_l('navigation','[menu_new]');
$we_menu_navigation['000200']['parent'] = '000100';
$we_menu_navigation['000200']['perm'] = '';
$we_menu_navigation['000200']['enabled'] = '1';

$we_menu_navigation['000300']['text'] = g_l('navigation','[entry]');
$we_menu_navigation['000300']['parent'] = '000200';
$we_menu_navigation['000300']['cmd'] = 'new_navigation';
$we_menu_navigation['000300']['perm'] = 'EDIT_NAVIGATION || ADMINISTRATOR';
$we_menu_navigation['000300']['enabled'] = '1';

$we_menu_navigation['000400']['text'] = g_l('navigation','[group]');
$we_menu_navigation['000400']['parent'] = '000200';
$we_menu_navigation['000400']['cmd'] = 'new_navigation_group';
$we_menu_navigation['000400']['perm'] = 'EDIT_NAVIGATION || ADMINISTRATOR';
$we_menu_navigation['000400']['enabled'] = '1';

$we_menu_navigation['000500']['text'] = g_l('navigation','[menu_save]');
$we_menu_navigation['000500']['parent'] = '000100';
$we_menu_navigation['000500']['cmd'] = 'save_navigation';
$we_menu_navigation['000500']['perm'] = 'EDIT_NAVIGATION || ADMINISTRATOR';
$we_menu_navigation['000500']['enabled'] = '1';

$we_menu_navigation['000600']['text'] = g_l('navigation','[menu_delete]');
$we_menu_navigation['000600']['parent'] = '000100';
$we_menu_navigation['000600']['cmd'] = 'delete_navigation';
$we_menu_navigation['000600']['perm'] = 'EDIT_NAVIGATION || ADMINISTRATOR';
$we_menu_navigation['000600']['enabled'] = '1';

$we_menu_navigation['000950']['parent'] = '000100'; // separator

$we_menu_navigation['001000']['text'] = g_l('navigation','[menu_exit]');
$we_menu_navigation['001000']['parent'] = '000100';
$we_menu_navigation['001000']['cmd'] = 'exit_navigation';
$we_menu_navigation['001000']['perm'] = '';
$we_menu_navigation['001000']['enabled'] = '1';
/*
$we_menu_navigation['001500']['text'] = g_l('navigation','[menu_options]');
$we_menu_navigation['001500']['parent'] = '000000';
$we_menu_navigation['001500']['perm'] = '';
$we_menu_navigation['001500']['enabled'] = '1';

$we_menu_navigation['001600']['text'] = g_l('navigation','[menu_generate]').'...';
$we_menu_navigation['001600']['parent'] = '001500';
$we_menu_navigation['001600']['cmd'] = 'generate_navigation';
$we_menu_navigation['001600']['perm'] = '';
$we_menu_navigation['001600']['enabled'] = '1';

$we_menu_navigation['001700']['text'] = g_l('navigation','[menu_settings]');
$we_menu_navigation['001700']['parent'] = '001500';
$we_menu_navigation['001700']['cmd'] = 'settings_navigation';
$we_menu_navigation['001700']['perm'] = '';
$we_menu_navigation['001700']['enabled'] = '1';*/

$we_menu_navigation['002000']['text'] = g_l('navigation','[menu_options]');
$we_menu_navigation['002000']['parent'] = '000000';
$we_menu_navigation['002000']['perm'] = '';
$we_menu_navigation['002000']['enabled'] = '1';

$we_menu_navigation['002100']['text'] = g_l('navigation','[menu_highlight_rules]');
$we_menu_navigation['002100']['parent'] = '002000';
$we_menu_navigation['002100']['perm'] = '';
$we_menu_navigation['002100']['cmd'] = 'navigation_rules';
$we_menu_navigation['002100']['enabled'] = '1';

$we_menu_navigation['003000']['text'] = g_l('navigation','[menu_help]');
$we_menu_navigation['003000']['parent'] = '000000';
$we_menu_navigation['003000']['perm'] = '';
$we_menu_navigation['003000']['enabled'] = '1';

$we_menu_navigation['003100']['text'] = g_l('navigation','[menu_help]').'...';
$we_menu_navigation['003100']['parent'] = '003000';
$we_menu_navigation['003100']['cmd'] = 'help_navigation';
$we_menu_navigation['003100']['perm'] = '';
$we_menu_navigation['003100']['enabled'] = '1';

$we_menu_navigation['003200']['text'] = g_l('navigation','[menu_info]').'...';
$we_menu_navigation['003200']['parent'] = '003000';
$we_menu_navigation['003200']['cmd'] = 'info';
$we_menu_navigation['003200']['perm'] = '';
$we_menu_navigation['003200']['enabled'] = '1';