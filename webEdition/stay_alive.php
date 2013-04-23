<?php
/**
 * webEdition CMS
 *
 * $Rev: 4648 $
 * $Author: mokraemer $
 * $Date: 2012-07-02 22:24:31 +0200 (Mon, 02 Jul 2012) $
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
//session_name(SESSION_NAME);
session_start();
?>
<html>
	<head>
	</head>
	<body>
		<!-- ping -->
		<script type="text/javascript">
			<!--
			setTimeout("self.location='stay_alive.php?r=<?php print rand(); ?>'", (5 *60000) );
			//-->
		</script>
	</body>
</html>