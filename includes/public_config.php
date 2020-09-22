<?php
/***************************************************************************
 *
 *   File                 : includes/public_config.php
 *   Software             : Kwikcontent
 *   Version              : 1.1
 *   Release Date         : August 29, 2009
 *   Copyright            : (C) 2009 Samson Software
 *   Contact              : http://www.kwikcontent.com/
 *   License              : Limited to one owner.  My not be reproduced or
 *                          redistributed under any circumstance.
 *
 ***************************************************************************/


//load smarty engine and vars
include(SMARTY_CLASS_LOC);
$smarty = new Smarty();
$smarty->force_compile = true;	//this is best when we are bouncing between templates.  Can be set to false (or removed) if no "template bouncing" is needed.
$smarty->template_dir = SMARTY_TEMPLATE_DIR;
$smarty->compile_dir = SMARTY_COMPILE_DIR;
$smarty->cache_dir = SMARTY_CACHE_DIR;
$smarty->config_dir = SMARTY_CONFIG_DIR;

//load generally needed vars within templates
$smarty->assign("BASE_DOMAIN", BASE_DOMAIN);
$smarty->assign("BASE_URL", BASE_URL);
$smarty->assign("BASE_FOLDER", BASE_FOLDER);
$smarty->assign("SMARTY_BASE_FOLDER", SMARTY_BASE_FOLDER);
$smarty->assign("SMARTY_TEMPLATE_URL", SMARTY_TEMPLATE_URL);

//should we hide php errors?
if ($domain->hide_public_errors == '1') {
	ini_set("display_errors", "Off");
}
?>
