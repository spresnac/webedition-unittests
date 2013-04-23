<?php

/**
 * webEdition CMS
 *
 * $Rev: 5051 $
 * $Author: mokraemer $
 * $Date: 2012-11-02 21:40:23 +0100 (Fri, 02 Nov 2012) $
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
$this->defaultPreviewCode = '<we:navigation navigationname="default" parentid="@###PARENTID###@" />

<we:navigationEntry type="folder" navigationname="default">
  <li><we:navigationField name="text" />
    <we:ifHasEntries><ul><we:navigationEntries /></ul></we:ifHasEntries>
  </li>
</we:navigationEntry>

<we:navigationEntry type="item" navigationname="default">
  <li><a href="<we:navigationField name="href" />"><we:navigationField name="text" /></a></li>
</we:navigationEntry>

<ul>
<we:navigationWrite navigationname="default" />
</ul>';
