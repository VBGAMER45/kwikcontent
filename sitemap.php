<?php
/***************************************************************************
 *
 *   File                 : sitemap.php
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
include_once('includes/article.php');
include_once('includes/static_page.php');

//$smarty->debugging = true;

//find main keywords
$keys = getKeywords(-1);
if (count($keys) > 0) {
	foreach ($keys as $id => $details) {
		$key = new Keyword($id);
		$key->setVars($details);

		//news info
		$key->news_url = $key->getYahooNewsLocation();
		$key->news_title = $key->getTitle('yahoo_news');

		//find keyword url info
		$key->keyword_url = $key->getKeywordLocation();

		//find images info
		$key->images_url = $key->getYahooImagesLocation();
		$key->images_title = $key->getTitle('yahoo_images');
		$key->total_images = $key->getTotalYahooImages();

		//find links info
		$key->links_url = $key->getYahooLinksLocation();
		$key->links_title = $key->getTitle('yahoo_links');
		//$key->total_links = $key->getTotalLinks();

		//find articles info
		$key->articles_url = $key->getArticlesLocation();
		$key->articles_title = $key->getTitle('articles');

		//find answers info
		$key->answers_url = $key->getYahooQAsLocation();
		$key->answers_title = $key->getTitle('yahoo_qas');

		//set into larger array for smarty
		$keywords[$key->top_key_id][$key->key_id] = get_object_vars($key);

		//construct other needed details
		if ($key->top_key_id == 0) $key_string .= $key->key_text.', ';
		$key_string_long .= $key->key_text.', ';
	}

	//assign keywords to smarty
	$smarty->assign("keywords", $keywords);

} else {
	die($domain->domain_title." is coming soon!");
}

//construct static pages data
$pages_data = getStaticPages();
if (count($pages_data) > 0) {
	foreach ($pages_data as $id => $details) {
		$sp = new StaticPage($id);
		$sp->setVars($details);
		$pages_data[$id]['sp_location'] = $sp->getLocation();
	}
}

//get list html for pages
$sql = "
	SELECT sp_id, parent_sp_id, sp_nav_title
	FROM {$multi_prefix}static_page
	ORDER BY sp_rank ASC
";
$q = mysql_query($sql);
traverseStaticPageNavTitlesList(0, 0, $q, array(), 'pages_data', 'list_html');
$smarty->assign("static_pages_list", $list_html);

//over keyword data
$key_string = removeLastChars($key_string, 2);
$key_string_long = removeLastChars($key_string_long, 2);

//establish vars for template
$smarty->assign('WINDOW_TITLE', $domain->domain_title.' - Your resource for '.$key_string);
$smarty->assign('META_DESCRIPTION', $domain->domain_home_meta_desc);
$smarty->assign('META_KEYS', $key_string_long);

//load a header file for any preprocessing
include_once('includes/public_header.php');

//output page
$smarty->display('sitemap.htm');

//load a footer file for any postprocessing
include_once('includes/public_footer.php');
?>
