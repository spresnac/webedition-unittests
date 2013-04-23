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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

    $i = 0;

    //  first xhtml from W3C
    $validationService[] = new validationService(
                            $i++,
                            'default',
                            'xhtml',
                            g_l('validation','[service_xhtml_upload]'),
                            'validator.w3.org',
                            '/check',
                            'post',
                            'uploaded_file',
                            'fileupload',
                            'text/html',
                            '',
                            '.html,.htm,.php',
                            1);

    $validationService[] = new validationService(
                            $i++,
                            'default',
                            'xhtml',
                            g_l('validation','[service_xhtml_url]'),
                            'validator.w3.org',
                            '/check',
                            'get',
                            'uri',
                            'url',
                            'text/html',
                            '',
                            '.html,.htm,.php',
                            1);


/*
$service['bobby'] = array(
                        'name'     => g_l('validation','[service_bobby]'),
                        'host'     => 'bobby.watchfire.com',
                        'path'     => '/bobby/bobbyServlet',
                        'method'   => 'get',
                        'varname'  => 'URL',
                        'checkvia' => 'url',
                        'ctype' => 'text/html',
                        'additionalVars' => ''
                    );
*/

