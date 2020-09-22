<?php
/***************************************************************************
 *
 *   File                 : articles.php
 *   Software             : Kwikcontent
 *   Version              : 1.1
 *   Release Date         : August 29, 2009
 *   Copyright            : (C) 2009-2011 Samson Software
 *   Contact              : http://www.kwikcontent.com/
 *   License              : Limited to one owner.  My not be reproduced or
 *                          redistributed under any circumstance.
 *
 ***************************************************************************/

//Request needed includes
include_once('includes/_global.php');
include_once('includes/public_config.php');
include_once('includes/article.php');

//$smarty->debugging = true;

//a keyword is required here
$key = new Keyword($k);
$key->loadVars();
if (!($key->key_id > 0)) {
	$url = BASE_URL;
	header("Location: $url");
}
$key->articles_title = $key->getTitle('articles');
$smarty->assign("key", get_object_vars($key));

$articles = $key->getArticles();
if (count($articles) > 0) {
	foreach ($articles as $id => $details) {
		$art = new Article($id);
		$art->setVars($details);
		$articles[$art->article_id]['article_teaser'] = html_entity_decode($art->article_teaser);
		$articles[$art->article_id]['article_url'] = $art->getLocation();
	}
}

$smarty->assign("articles", $articles);

//create breadcrumb
$breadcrumb = '<a href="'.BASE_FOLDER.'">Home</a> > <a href="'.$key->getKeywordLocation().'">'.ucwords($key->getTitle('keyword')).'</a> > '.ucwords($key->getTitle('articles'));
$smarty->assign("BREADCRUMB", $breadcrumb);

//establish vars for template
$smarty->assign('WINDOW_TITLE', ($domain->domain_window_titles ? $domain->domain_title.$domain->delimiters['window_title'] : '').ucwords($key->getTitle('articles')));
$smarty->assign('META_DESCRIPTION', 'This is a directory of all the articles we have saved relating to '.$key->key_text.'.');
$smarty->assign('META_KEYS', getKeywordString());

//load a header file for any preprocessing
include_once('includes/public_header.php');

//output page
$smarty->display('articles.htm');

//load a footer file for any postprocessing
include_once('includes/public_footer.php');
?>
