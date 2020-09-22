<?php
/***************************************************************************
 *
 *   File                 : search_google.php
 *   Software             : Kwikcontent
 *   Version              : 1.1
 *   Release Date         : August 29, 2009
 *   Copyright            : (C) 2009 Samson Software
 *   Contact              : http://www.kwikcontent.com/
 *   License              : Limited to one owner.  My not be reproduced or
 *                          redistributed under any circumstance.
 *
 ***************************************************************************/

//Request needed includes
include_once('includes/_global.php');
include_once('includes/public_config.php');

//$smarty->debugging = true;

//ensure admin wants this page to be shown
if (!$domain->google_search) {
	$url = BASE_FOLDER;
	header("Location: $url");
}

//establish vars for template
$smarty->assign('WINDOW_TITLE', ($domain->domain_window_titles ? $domain->domain_title.$domain->delimiters['window_title'] : '').'Site Search');
$smarty->assign('META_DESCRIPTION', 'This page allows you to search our site.');
$smarty->assign('META_KEYS', getKeywordString());

//load a header file for any preprocessing
include_once('includes/public_header.php');

//output page
$smarty->display('search_google.htm');

//load a footer file for any postprocessing
include_once('includes/public_footer.php');
?>
