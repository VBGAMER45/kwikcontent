<?php
/***************************************************************************
 *
 *   File                 : article.php
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
include_once('includes/article.php');

//$smarty->debugging = true;

//an article is required here
$article = new Article($a);
$article->loadVars();
if (!($article->article_id > 0)) {
	$url = BASE_URL;
	header("Location: $url");
}

//all articles have one top-level keyword association
$key = new Keyword($article->key_id);
$key->loadVars();

//find all of the sub keys for this top key
$sub_keys = getKeywords($key->key_id);
if (count($sub_keys) > 0) {
	foreach ($sub_keys as $id => $details) {
		$sub_key = new Keyword($id);
		$sub_key->setVars($details);

		$sub_keys[$id]['keyword_url'] = $sub_key->getKeywordLocation();

		$key_tags[$id] = '<a href="'.$sub_key->getKeywordLocation().'">'.$sub_key->key_text.'</a>';
	}

	//construct a key tag string to throw to smarty
	$key_tags_string = getCommaString($key_tags);
	$smarty->assign("key_tags", $key_tags_string);
}

//find the author
$author = new Author($article->author_id);
$author->loadVars();
$author->name = $author->getName();

//ensure all entities are decoded
$article_vars = get_object_vars($article);
if (count($article_vars) > 0) {
	foreach ($article_vars as $key_here => $val) {
		$article_vars[$key_here] = html_entity_decode($val);
	}
}

//give smarty what it needs
$smarty->assign("article", $article_vars);
$smarty->assign("author", get_object_vars($author));

//create breadcrumb
$breadcrumb = '<a href="'.BASE_FOLDER.'">Home</a> > <a href="'.$key->getKeywordLocation().'">'.ucwords($key->getTitle('keyword')).'</a> > <a href="'.$key->getArticlesLocation().'">'.ucwords($key->getTitle('articles')).'</a> > '.$article->article_title;
$smarty->assign("BREADCRUMB", $breadcrumb);

//establish vars for template
$smarty->assign('WINDOW_TITLE', ($domain->domain_window_titles ? $domain->domain_title.$domain->delimiters['window_title'] : '').$article->article_title);
if ($article->article_meta_desc == '') {
	$smarty->assign('META_DESCRIPTION', 'This article explores '.$key->key_text.'.  This page also provides links to other resources including '.$key->key_text.' images, a '.$key->key_text.' knowledgebase and third-party '.$key->key_text.' web sites.');
} else {
	$smarty->assign('META_DESCRIPTION', $article->article_meta_desc);
}
if ($article->article_meta_keys == '') {
	$smarty->assign('META_KEYS', $key->getSubkeyString());
} else {
	$smarty->assign('META_KEYS', $article->article_meta_keys);
}

//load a header file for any preprocessing
include_once('includes/public_header.php');

//output page
$smarty->display('article.htm');

//load a footer file for any postprocessing
include_once('includes/public_footer.php');
?>
