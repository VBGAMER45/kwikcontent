<?php
/***************************************************************************
 *
 *   File                 : keyword.php
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
include_once('includes/yahoo.php');
include_once('includes/article.php');

//$smarty->debugging = true;

//ensure a keyword has been passed
if ($k > 0) {
	$key = new Keyword($k);
	$key->loadVars();

	if (!$key->key_id > 0) {
		//this is an old/dead keyword
		$redirect = true;
	}

	//throw the key to smarty
	$smarty->assign("keyword", get_object_vars($key));

	//throw the key to smarty
	$smarty->assign("top_key_id", $key->top_key_id);

} else {
	$redirect = true;
}
if ($redirect) {
	$url = BASE_URL;
	header("Location: $url");
}

//find all of the proper keywords for this page
$keys[$key->key_id] = get_object_vars($key);

//we need to find the rest of the sub keywords here
$sub_keys = getKeywords($key->key_id);
if (count($sub_keys) > 0) {
	foreach ($sub_keys as $id => $details) {
		$keys[$id] = $details;
	}
}

//loop over one or more keywords and find it's needed info
$total_keys = count($keys);
if ($total_keys > 0) {
	foreach ($keys as $id => $details) {
		$key_here = new Keyword($id);
		$key_here->setVars($details);

		//update any news
		$key_here->updateYahooNews();

		//find news
		$news = $key_here->getYahooNews($domain->preview_y_news);
		if (count($news) > 0) {
			foreach ($news as $nid => $ndetails) {
				$n = new YahooNews($nid);
				$n->setVars($ndetails);
				$n->ripData();
				$ynews[$key_here->key_id][$nid] = get_object_vars($n);
			}
		}
		$key_here->news_url = $key_here->getYahooNewsLocation();
		$key_here->news_title = $key_here->getTitle('yahoo_news');

		//find articles info
		$key_here->articles_url = $key_here->getArticlesLocation();
		$key_here->articles_title = $key_here->getTitle('articles');

		//find images info
		$key_here->images_url = $key_here->getYahooImagesLocation();
		$key_here->images_title = $key_here->getTitle('yahoo_images');
		$key_here->total_images = $key_here->getTotalYahooImages();



		//find Videos
		$key_here->videos_url = $key_here->getYouTubelocation();
		$key_here->videos_title = $key_here->getTitle('youtube_videos');
		$key_here->total_videos = $key_here->getTotalYouTube();


		//find links info
		$key_here->links_url = $key_here->getYahooLinksLocation();
		$key_here->links_title = $key_here->getTitle('yahoo_links');
		//$key->total_links = $key->getTotalLinks();

		//find answers info
		$key_here->answers_url = $key_here->getYahooQAsLocation();
		$key_here->answers_title = $key_here->getTitle('yahoo_qas');

		//find data portal url
		$key_here->keyword_url = $key_here->getKeywordLocation();

		//create a better visual if this is the top key or if it is the only key
		if (($key_here->top_key_id == 0) || $total_keys < 2) {

			//find articles url
			$key_here->articles_url = $key_here->getArticlesLocation();

			//find all articles
			$key_here->articles = $key_here->getArticles();
			$key_here->total_articles = count($key_here->articles);
			if (count($key_here->articles) > 0) {
				foreach ($key_here->articles as $ar_id => $ar_details) {
					$ar = new Article($ar_id);
					$ar->loadVars($ar_details);
					$key_here->articles[$ar_id]['article_url'] = $ar->getLocation();
				}
			}

			//find top images for each top key
			$sql = "
				SELECT *
				FROM {$multi_prefix}yahoo_image
				WHERE yim_rank <= '$domain->preview_y_images'
				AND key_id = '$key_here->key_id'
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
				WHERE yqa_rank <= '$domain->preview_y_qas'
				AND key_id = '$key_here->key_id'
				ORDER BY yqa_rank
			";
			$q = mysql_query($sql);
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

		}

		//set into larger array for smarty
		$keywords[$key_here->top_key_id][$key_here->key_id] = get_object_vars($key_here);

		//construct other needed details
		if ($key_here->top_key_id == 0) $key_string .= $key->key_text.', ';
		$key_string_long .= $key_here->key_text.', ';
	}
}

//assign keyword(s) to smarty
$smarty->assign("keywords", $keywords);

//give smarty the news
$smarty->assign("ynews", $ynews);

//create breadcrumb
$breadcrumb = '<a href="'.BASE_FOLDER.'">Home</a> > '.$key->key_text.' information';
$smarty->assign("BREADCRUMB", $breadcrumb);

$key_string = removeLastChars($key_string, 2);
$key_string_long = removeLastChars($key_string_long, 2);

//establish vars for template
$smarty->assign('WINDOW_TITLE', $domain->domain_title.' - '.ucwords($key->getTitle('keyword')));
$smarty->assign('META_DESCRIPTION', ucwords($key->getTitle('keyword')).'.  Use this page as the gateway to all of our '.$key->key_text.' resources.');
$smarty->assign('META_KEYS', $key_string_long);

//load a header file for any preprocessing
include_once('includes/public_header.php');

//output page
$smarty->display('keyword.htm');

//load a footer file for any postprocessing
include_once('includes/public_footer.php');
?>
