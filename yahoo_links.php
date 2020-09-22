<?php
/***************************************************************************
 *
 *   File                 : yahoo_links.php
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
include_once('includes/yahoo.php');

//$smarty->debugging = true;

//ensure a keyword has been passed
if ($k > 0) {
	$key = new Keyword($k);
	$key->loadVars();

	if (!$key->key_id > 0) {
		//this is an old/dead keyword
		$redirect = true;
	}

	$key->links_title = $key->getTitle('yahoo_links');

	//throw the key to smarty
	$smarty->assign("keyword", get_object_vars($key));

} else {
	$redirect = true;
}
if ($redirect) {
	$url = BASE_URL;
	header("Location: $url");
}

//find links for this keyword
$links = $key->getYahooLinks();
if (count($links) > 0) {
	foreach ($links as $id => $details) {
		$link = new YahooLink($id);
		$link->setVars($details);
		$link->ripData();
		$s_links[$link->ylk_id] = get_object_vars($link);
	}
	$smarty->assign("links", $s_links);
}

//create breadcrumb
$breadcrumb = '<a href="'.BASE_FOLDER.'">Home</a> > <a href="'.$key->getKeywordLocation().'">'.ucwords($key->getTitle('keyword')).'</a> > '.ucwords($key->getTitle('yahoo_links'));
$smarty->assign("BREADCRUMB", $breadcrumb);

//establish vars for template
$smarty->assign('WINDOW_TITLE', ($domain->domain_window_titles ? $domain->domain_title.$domain->delimiters['window_title'] : '').ucwords($key->getTitle('yahoo_links')));
$smarty->assign('META_DESCRIPTION', 'This page is full of links and resource information for '.$key->key_text.' on the Internet.');
$smarty->assign('META_KEYS', getKeywordString());

//load a header file for any preprocessing
include_once('includes/public_header.php');

//output page
$smarty->display('yahoo_links.htm');

//load a footer file for any postprocessing
include_once('includes/public_footer.php');
?>
