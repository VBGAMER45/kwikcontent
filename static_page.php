<?php
/***************************************************************************
 *
 *   File                 : static_page.php
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
include_once('includes/static_page.php');

//$smarty->debugging = true;

$sp = new StaticPage($p);
$sp->loadVars();
if (!($sp->sp_id > 0)) {
	$url = BASE_FOLDER;
	header("Location: $url");
	die();
}

//find all child pages
$sp_children = $sp->getChildren();
if (count($sp_children) > 0) {
	foreach ($sp_children as $id => $details) {
		$spc = new StaticPage($id);
		$spc->setVars($details);
		$sp_children[$id]['sp_url'] = $spc->getLocation();
	}
}
$smarty->assign("sp_children", $sp_children);

//check to see if this is a rss feed page
if (trim($sp->sp_rss_url) != '') {
	define("MAGPIE_DIR", "includes/magpierss/");
	include_once('includes/magpierss/rss_fetch.inc');
	$sp->rss = fetch_rss(html_entity_decode($sp->sp_rss_url));
}

//format the sp object
$sp->sp_html = html_entity_decode($sp->sp_html);
$smarty->assign_by_ref("sp", $sp);

//create breadcrumb
$breadcrumb = '<a href="'.BASE_URL.'">Home</a> > ';
$current_parent = $sp->parent_sp_id;
while ($current_parent != '0') {
	$spp = new StaticPage($current_parent);
	$spp->loadVars();
	$current_parent = $spp->parent_sp_id;
	$breadcrumb_parents[$spp->sp_id]['url'] = $spp->getLocation();
	$breadcrumb_parents[$spp->sp_id]['nav_title'] = $spp->sp_nav_title;
}
if (count($breadcrumb_parents) > 0) {
	$breadcrumb_parents = array_reverse($breadcrumb_parents);
	foreach ($breadcrumb_parents as $id => $spp) {
		$breadcrumb .= '<a href="'.$spp['url'].'">'.$spp['nav_title'].'</a> > ';
	}
}
$breadcrumb .= $sp->sp_nav_title;
$smarty->assign("BREADCRUMB", $breadcrumb);

//establish vars for template
$smarty->assign('WINDOW_TITLE', ($domain->domain_window_titles ? $domain->domain_title.$domain->delimiters['window_title'] : '').$sp->sp_win_title);
$smarty->assign('META_DESCRIPTION', $sp->sp_meta_desc);
$smarty->assign('META_KEYS', $sp->sp_meta_keys);

//load a header file for any preprocessing
include_once('includes/public_header.php');

//output page
$smarty->display('static_page.htm');

//load a footer file for any postprocessing
include_once('includes/public_footer.php');
?>
