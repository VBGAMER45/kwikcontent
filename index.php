<?php
/***************************************************************************
 *
 *   File                 : index.php
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

//$smarty->debugging = true;

$keys = getKeywords(0);
if (count($keys) > 0) {
	foreach ($keys as $id => $details) {
		$key = new Keyword($id);
		$key->setVars($details);

		//news info
		$key->news_url = $key->getYahooNewsLocation();
		$key->news_title = $key->getTitle('yahoo_news');

		//find articles url
		$key->articles_url = $key->getArticlesLocation();
		$key->articles_title = $key->getTitle('articles');

		//find all articles
		$key->articles = $key->getArticles($domain->preview_articles);
		$key->total_articles = count($key->articles);
		if (count($key->articles) > 0) {
			foreach ($key->articles as $ar_id => $ar_details) {
				$ar = new Article($ar_id);
				$ar->loadVars($ar_details);
				$key->articles[$ar_id] = $ar_details;
				$key->articles[$ar_id]['article_url'] = $ar->getLocation();
				$key->articles[$ar_id]['article_teaser'] = html_entity_decode($ar->article_teaser);
				$key_articles[$ar_id] = $key->articles[$ar_id];
			}
		}

		//find images info
		$key->images_url = $key->getYahooImagesLocation();
		$key->images_title = $key->getTitle('yahoo_images');

		//find links info
		$key->links_url = $key->getYahooLinksLocation();
		$key->links_title = $key->getTitle('yahoo_links');

		//find answers info
		$key->answers_url = $key->getYahooQAsLocation();
		$key->answers_title = $key->getTitle('yahoo_qas');

		//set into larger array for smarty
		$keywords[$key->key_id] = get_object_vars($key);

		//construct other needed details
		if ($key->top_key_id == 0) $key_string .= $key->key_text.', '; 			$key_string_long .= $key->key_text.', ';

		//build key id sql string for later queries
		$key_id_string .= "key_id = '$key->key_id' OR ";
	}

	//assign all articles in one array
	$smarty->assign("key_articles", $key_articles);

	//find all ohe snapshot info
	$key_id_string = removeLastChars($key_id_string, 3);

	//find top images for each top key
	$sql = "
		SELECT *
		FROM {$multi_prefix}yahoo_image
		WHERE yim_rank <= '$domain->preview_y_images'
		AND ($key_id_string)
		ORDER BY yim_rank
	";
	$q = mysql_query($sql);
	while ($r = mysql_fetch_assoc($q)) {
		$key_id = $r['key_id'];
		$yim_id = $r['yim_id'];
		$yim = new YahooImage($yim_id);
		$yim->setVars($r);
		$yim->ripData();
		$yim->view_url = $yim->getLocation();

		$yims[$key_id][$yim_id] = get_object_vars($yim);
	}

	$smarty->assign("yims", $yims);

	//find top yqa's for each top key

	$sql = "
		SELECT *
		FROM {$multi_prefix}yahoo_qa
		WHERE yqa_rank <= '".$domain->preview_y_qas."'
		AND ($key_id_string)
		ORDER BY yqa_rank
	";

	$q = mysql_query($sql) or die("Error, $q");
	while ($r = mysql_fetch_assoc($q)) {

		$key_id = $r['key_id'];
		$yqa_id = $r['yqa_id'];
		$yqa = new YahooQA($yqa_id);
		$yqa->setVars($r);
		$yqa->ripData();
		$yqa->view_url = $yqa->getLocation();
		//these help to strip uneeded entities
		$yqa->y_q_subj = html_entity_decode($yqa->y_q_subj);
		$yqa->y_q_cont = getShortVersion(html_entity_decode($yqa->y_q_cont), 150);

		$yqas[$key_id][$yqa_id] = get_object_vars($yqa);
	}

	$smarty->assign("yqas", $yqas);

	//assign keywords to smarty
	$smarty->assign("keywords", $keywords);

} else {
	die("No keywords have been established.  Visit <a href=/admin>your admin panel</a> to setup your site.");
}

$key_string = removeLastChars($key_string, 2);
$key_string_long = removeLastChars($key_string_long, 2);

//establish vars for template
// $smarty->assign('WINDOW_TITLE', $domain->domain_title.' - Your resource for '.$key_string);

//short title for better SEO
$smarty->assign('WINDOW_TITLE', $domain->domain_title);

$smarty->assign('META_DESCRIPTION', $domain->domain_home_meta_desc);
$smarty->assign('META_KEYS', $key_string_long);

//load a header file for any preprocessing
include_once('includes/public_header.php');

//output page
$smarty->display('index.htm');

//load a footer file for any postprocessing
include_once('includes/public_footer.php');
?>
