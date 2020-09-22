<?php
/***************************************************************************
 *
 *   File                 : sitemaps/sitemap.php
 *   Software             : Kwikcontent
 *   Version              : 1.1
 *   Release Date         : September 6, 2011
 *   Copyright            : (C) 2009-2011 Samson Software
 *   Contact              : http://www.kwikcontent.com/
 *   License              : Limited to one owner.  My not be reproduced or
 *                          redistributed under any circumstance.
 *
 ***************************************************************************/

include_once("../includes/_global.php");

include_once('../includes/public_config.php');
include_once('../includes/yahoo.php');
include_once('../includes/article.php');
include_once('../includes/static_page.php');


ob_clean();
header('Content-Type: text/xml'); 

echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
			    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

$myBaseUrl = BASE_URL;
if (substr(BASE_URL,strlen(BASE_URL) -1,1) == '/')
{
	$myBaseUrl = substr(BASE_URL,0,strlen(BASE_URL) -1);
}

$keys = getKeywords(-1);
if (count($keys) > 0) {
	foreach ($keys as $id => $details) {
		$key = new Keyword($id);
		$key->setVars($details);

		//news info
		$key->news_url = $key->getYahooNewsLocation();
		$key->news_title = $key->getTitle('yahoo_news');
		
		if ($key->news_url != '')
		{
		// Generate the sitemap entry
		echo '<url>
		<loc>' . $myBaseUrl .$key->news_url . '</loc>
		<changefreq>daily</changefreq>
		<priority>0.8</priority>
	</url>';
		}

		//find keyword url info
		$key->keyword_url = $key->getKeywordLocation();
		
		if ($key->keyword_url != '')
		{
		// Generate the sitemap entry
		echo '<url>
		<loc>' . $myBaseUrl .$key->keyword_url . '</loc>
		<changefreq>daily</changefreq>
		<priority>0.8</priority>
	</url>';
		}

		//find images info
		$key->images_url = $key->getYahooImagesLocation();
		$key->images_title = $key->getTitle('yahoo_images');
		$key->total_images = $key->getTotalYahooImages();
		
		if ($key->images_url != '')
		{
		// Generate the sitemap entry
		echo '<url>
		<loc>' . $myBaseUrl .$key->images_url . '</loc>
		<changefreq>daily</changefreq>
		<priority>0.8</priority>
	</url>';
		}

		//find links info
		$key->links_url = $key->getYahooLinksLocation();
		$key->links_title = $key->getTitle('yahoo_links');
		//$key->total_links = $key->getTotalLinks();
		
		if ($key->links_url != '')
		{
		// Generate the sitemap entry
		echo '<url>
		<loc>' . $myBaseUrl .$key->links_url . '</loc>
		<changefreq>daily</changefreq>
		<priority>0.8</priority>
	</url>';
		}

		//find articles info
		$key->articles_url = $key->getArticlesLocation();
		$key->articles_title = $key->getTitle('articles');
		
		if ($key->articles_url != '')
		{
		// Generate the sitemap entry
		echo '<url>
		<loc>' . $myBaseUrl . $key->articles_url . '</loc>
		<changefreq>daily</changefreq>
		<priority>0.8</priority>
	</url>';
		}

		//find answers info
		$key->answers_url = $key->getYahooQAsLocation();
		$key->answers_title = $key->getTitle('yahoo_qas');
		
		if ($key->answers_url != '')
		{
		// Generate the sitemap entry
		echo '<url>
		<loc>' . $myBaseUrl . $key->answers_url . '</loc>
		<changefreq>daily</changefreq>
		<priority>0.8</priority>
	</url>';
		}

		//set into larger array for smarty
		$keywords[$key->top_key_id][$key->key_id] = get_object_vars($key);

		//construct other needed details
		if ($key->top_key_id == 0) $key_string .= $key->key_text.', ';
		$key_string_long .= $key->key_text.', ';
		
		
		
		
	}


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
//$smarty->assign("static_pages_list", $list_html);

// Loop though all the pages


echo '</urlset>';

?>