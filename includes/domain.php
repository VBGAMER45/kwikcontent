<?php
/***************************************************************************
 *
 *   File                 : includes/domain.php
 *   Software             : Kwikcontent
 *   Version              : 1.2
 *   Release Date         : December 19, 2011
 *   Copyright            : (C) 2011 Samson Software
 *   Contact              : http://www.kwikcontent.com/
 *   License              : Limited to one owner.  My not be reproduced or
 *                          redistributed under any circumstance.
 *
 ***************************************************************************/


/*
	FUNCTIONS
*/
	function getKeywordString() {
		global $domain, $multi_prefix;
		$sql = "
			SELECT {$multi_prefix}keyword.key_id, {$multi_prefix}keyword.key_text
			FROM {$multi_prefix}keyword
			ORDER BY {$multi_prefix}keyword.key_text
		";
		$q = mysql_query($sql);
		while ($r = mysql_fetch_object($q)) {
			$array[$r->key_id] = $r->key_text;
		}
		return getCommaString($array);
	}

	function getDomainTemplates() {
		global $multi_prefix;
		$sql = "
			SELECT *
			FROM {$multi_prefix}domain_template
			ORDER BY dtpl_name
		";
		return getArrayFromSQL("dtpl_id", $sql);
	}

	function getTotalArticles() {
		global $multi_prefix;
		$sql = "
			SELECT COUNT(article_id)
			FROM {$multi_prefix}article
		";
		return mysql_result(mysql_query($sql), 0);
	}

	function getArticles() {
		global $multi_prefix;
		$sql = "
			SELECT a.*
			FROM {$multi_prefix}article as a, {$multi_prefix}keyword as k
			WHERE k.key_id = a.key_id
			ORDER BY k.key_rank, a.key_id, a.article_rank
		";
		return getArrayFromSql("article_id", $sql);
	}

	function getKeywords($top_key_id = 0, $order_by = 'key_rank') {
		global $multi_prefix;
		if ($top_key_id < 0) {
			//get ALL keywords
			$sql = "
				SELECT *
				FROM {$multi_prefix}keyword
				ORDER BY $order_by
			";
		} else {
			//get keywords for this top_key_id
			$sql = "
				SELECT *
				FROM {$multi_prefix}keyword
				WHERE top_key_id = '$top_key_id'
				ORDER BY $order_by
			";
		}
		return getArrayFromSQL("key_id", $sql);
	}

	function mergeRanks($old = array(), $new = array()) {

		//pull all of the new ones out of the old ranks
		if (count($new) > 0) {
			foreach ($new as $id => $rank) {
				unset($old[$id]);
			}
		}

		$total_new = count($new);
		$total_old = count($old);

		//add all of the old ones after the new ones
		if ($total_old > 0) {
			$new_rank = $total_new;
			foreach ($old as $id => $rank) {
				$new_rank++;
				$new[$id] = $new_rank;
			}
		}

		return $new;
	}

/*
	CLASSES
*/
class Domain extends DbTable
{
	function Domain($id = 1) {
		$db_table = "domain";
		$key_name = "domain_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}
}


class Keyword extends DbTable
{
	function Keyword($id = 0) {
		$db_table = "keyword";
		$key_name = "key_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}

	function getSubKeys($order_by = "key_rank", $order_dir = "ASC") {
		global $multi_prefix;
		$sql = "
			SELECT *
			FROM {$multi_prefix}keyword
			WHERE top_key_id = '$this->key_value'
			ORDER BY $order_by $order_dir
		";
		return getArrayFromSQL("key_id", $sql);
	}

	function getSubkeyString() {
		$keys = condenseArray($this->getSubKeys(), "key_rank", "key_text");
		$keys[0] = $this->key_text;
		ksort($keys);
		return getCommaString($keys);
	}

	function getTitle($title_area) {
		global $domain;
		$title = $domain->titles[$title_area];
		return str_replace("*", $this->key_text, $title);
	}

	function getArticles($total_articles = -1) {
		global $multi_prefix;
		$sql = "
			SELECT *
			FROM {$multi_prefix}article
			WHERE key_id = '$this->key_value'
			ORDER BY article_rank
		";
		if ($total_articles > -1) {
			$sql .= "
				LIMIT 0, $total_articles
			";
		}
		return getArrayFromSql("article_id", $sql);
	}

	function getYahooImages() {
		global $multi_prefix;
		$sql = "
			SELECT *
			FROM {$multi_prefix}yahoo_image
			WHERE key_id = '$this->key_value'
			ORDER BY yim_rank
		";
		return getArrayFromSQL("yim_id", $sql);
	}

	function getYahooLinks() {
		global $multi_prefix;
		$sql = "
			SELECT *
			FROM {$multi_prefix}yahoo_link
			WHERE key_id = '$this->key_value'
			ORDER BY ylk_rank
		";
		return getArrayFromSQL("ylk_id", $sql);
	}

	function getYahooNews($total_entries = -1) {
		global $multi_prefix;
		$sql = "
			SELECT *
			FROM {$multi_prefix}yahoo_news
			WHERE key_id = '$this->key_value'
			ORDER BY ynw_time_p DESC
		";
		if ($total_entries > -1) {
			$sql .= "
				LIMIT 0, $total_entries
			";
		}
		return getArrayFromSQL("ynw_id", $sql);
	}

	function getYahooQAs() {
		global $multi_prefix;
		$sql = "
			SELECT *
			FROM {$multi_prefix}yahoo_qa
			WHERE key_id = '$this->key_value'
			ORDER BY yqa_rank
		";
		return getArrayFromSQL("yqa_id", $sql);
	}

	function getYouTube() {
		global $multi_prefix;
		$sql = "
			SELECT *
			FROM {$multi_prefix}youtube_video
			WHERE key_id = '$this->key_value'
			ORDER BY ytv_rank
		";
		return getArrayFromSQL("ytv_id", $sql);
	}

	/*
		TOTALS METHODS
	*/

	function getTotalYahooImages() {
		global $multi_prefix;
		$sql = "
			SELECT COUNT(yim_id)
			FROM {$multi_prefix}yahoo_image
			WHERE key_id = '$this->key_value'
		";
		return mysql_result(mysql_query($sql), 0);
	}

	function getTotalYahooLinks() {
		global $multi_prefix;
		$sql = "
			SELECT COUNT(ylk_id)
			FROM {$multi_prefix}yahoo_link
			WHERE key_id = '$this->key_value'
		";
		return mysql_result(mysql_query($sql), 0);
	}

	function getTotalYouTube() {
		global $multi_prefix;
		$sql = "
			SELECT COUNT(ytv_id)
			FROM {$multi_prefix}youtube_video
			WHERE key_id = '$this->key_value'
		";
		return mysql_result(mysql_query($sql), 0);
	}

	
	function getBingNews($total_entries = -1) {
		global $multi_prefix;
		$sql = "
			SELECT *
			FROM {$multi_prefix}bing_news
			WHERE key_id = '$this->key_value'
			ORDER BY bn_time_p DESC
		";
		if ($total_entries > -1) {
			$sql .= "
				LIMIT 0, $total_entries
			";
		}
		return getArrayFromSQL("bn_id", $sql);
	}

	/*
		LOCATION METHODS
	*/

	function getKeywordLocation() {
		global $domain;
		return BASE_FOLDER.$domain->slugs['keyword'].$this->key_value.'/'.getFakeFilename($this->key_text).'.html';
	}

	function getYahooNewsLocation() {
		global $domain;
		return BASE_FOLDER.$domain->slugs['yahoo_news'].$this->key_value.'/'.getFakeFilename($this->key_text).'.html';
	}

	function getArticlesLocation() {
		global $domain;
		return BASE_FOLDER.$domain->slugs['articles'].$this->key_value.'/'.getFakeFilename($this->key_text).'.html';
	}

	function getYahooImagesLocation() {
		global $domain;
		return BASE_FOLDER.$domain->slugs['yahoo_images'].$this->key_value.'/'.getFakeFilename($this->key_text).'.html';
	}

	function getYahooLinksLocation() {
		global $domain;
		return BASE_FOLDER.$domain->slugs['yahoo_links'].$this->key_value.'/'.getFakeFilename($this->key_text).'.html';
	}

	function getYahooQAsLocation() {
		global $domain;
		return BASE_FOLDER.$domain->slugs['yahoo_qas'].$this->key_value.'/'.getFakeFilename($this->key_text).'.html';
	}

	function getYouTubelocation() {
		global $domain;
		return BASE_FOLDER.$domain->slugs['youtube_videos'].$this->key_value.'/'.getFakeFilename($this->key_text).'.html';
	}

	/*
		OTHER METHODS
	*/

	function assignRank($new_rank = 0) {
		global 	$multi_prefix;
		//let's first get all of the current standings at this top_key_level
		$array_current = condenseArray(getKeywords($this->top_key_id), "key_id", "key_rank");

		//check t see if $new_rank value needs to be altered
		if ($new_rank == 0 || count($array_current) < 1 || count($array_current) < $new_rank) {
			//give it the lowest rank(e.g. highest number)
			$new_rank = sizeof($array_current);
			if ($new_rank == 0) {
				$new_rank = 1;
			}
		}

		//if this had an existing rank, decrement all above it by one
		if ($this->key_rank > 0) {
			$sql = "
				UPDATE {$multi_prefix}$this->db_table
				SET key_rank = key_rank - 1
				WHERE key_rank > '".$this->key_rank."'
				AND top_key_id = '$this->top_key_id'
			";
			$update = mysql_query($sql) or die(mysql_error());
		}

		//now increment all ranks that hold the new rank or higher
		$sql = "
			UPDATE {$multi_prefix}$this->db_table
			SET key_rank = key_rank + 1
			WHERE key_rank >= '$new_rank'
			AND top_key_id = '$this->top_key_id'
		";
		$update = mysql_query($sql) or die(mysql_error());

		//now assign this one its new rank
		$this->setColumnValue("key_rank", $new_rank);
	}



	/*
		Content grabbing functions
	*/

	function updateYahooImages() {
		//this can be very time consuming
		ini_set("max_execution_time", "600");

		global $domain, $multi_prefix;

		//find bing's image results
		$rest_url = 'http://api.bing.net/json.aspx?Appid=' . $domain->bing_app_id. '&sources=image&query=' . urlencode($this->key_text);

		$array = DownloadFileContents($rest_url);

$jsonobj = json_decode($array);
//print_r($jsonobj);

	$imgs = array();
	foreach($jsonobj->SearchResponse->Image->Results as $value)
	{

		
/*		
Title 	The title of the image file.
Summary 	Summary text associated with the image file.
Url 	The URL for the image file.
ClickUrl 	The URL for linking to the image file. See URL linking for more information.
RefererUrl 	The URL of the web page hosting the content.
FileSize 	The size of the file in bytes.
FileFormat 	

One of bmp, gif, jpg, or png.
Height 	The height of the image in pixels.
Width 	The width of the image in pixels.
Thumbnail 	The URL of the thumbnail file and its height and width in pixels.
Publisher 	The creator of the image file.
Restrictions 	

Provides any restrictions for this media object. Restrictions include noframe and noinline.

    * Noframe means that you should not display it with a framed page on your site.
    * Noinline means that you should not inline the object in the frame up top (it won't work because the site has some protection based on the "referrer" field).

Copyright 	The copyright owner. 
	*/	
		
		
		/*
		    [Title] => Site Profile: Zynga
    [MediaUrl] => http://www.webdevtwopointzero.com/wp-content/uploads/2008/11/zynga.jpg
    [Url] => http://www.webdevtwopointzero.com/sites/zynga/
    [DisplayUrl] => http://www.webdevtwopointzero.com/sites/zynga/
    [Width] => 852
    [Height] => 480
    [FileSize] => 111004
    [ContentType] => image/jpeg
    [Thumbnail] => stdClass Object
        (
            [Url] => http://ts2.mm.bing.net/images/thumbnail.aspx?q=1427015082091&id=1abaf648d8e4fda4dd01c34b3eba8ca9
            [ContentType] => image/jpeg
            [Width] => 160
            [Height] => 90
            [FileSize] => 4091
        )

	*/
        
		$myimage = array();
		$bingresult = serialize($value);
		
		$myimage['Title']  = $value->Title;
		$myimage['Summary'] = '';
		$myimage['Url'] = $value->MediaUrl;
		$myimage['ClickUrl'] = $value->DisplayUrl;
		$myimage['RefererUrl'] = $value->DisplayUrl;
		$myimage['FileSize'] = $value->FileSize;
		$myimage['FileFormat'] = str_replace("image/","",$value->ContentType);
		$myimage['Height'] = $value->Height;
		$myimage['Width'] = $value->Width;
		
		$thumbarray = array();
		$thumbarray['Url']  = $value->Thumbnail->Url;
		$thumbarray['Height'] = $value->Thumbnail->Height;
		$thumbarray['Width'] =  $value->Thumbnail->Width;
		$myimage['Thumbnail'] = $thumbarray;
		
		
		$imgs[] = $myimage;
		
		//print_r($myimage);
		
	}
	
	//die("done");
	
		
		if (count($imgs) > 0) {

			//find all of the current ranks
			$sql = "
				SELECT yim_id
				FROM {$multi_prefix}yahoo_image
				WHERE key_id = '$this->key_value'
				ORDER BY yim_rank
			";
			$q = mysql_query($sql);
			$old_rank = 0;
			while ($r = mysql_fetch_object($q)) {
				$old_rank++;
				$old_ranks[$r->yim_id] = $old_rank;
			}

			//find all of the existing images in order of their rank
			$images = $this->getYahooImages();
			$image_ranks = condenseArray($images, "yim_url", "yim_id");

			//store all new images in order of rank
			$rank = 0;
			foreach ($imgs as $data) {//testshowarray($data);

				$rank++;

				//do we need an image update or is this a new image to the system?
				$img_url = $data['Url'];
				if (@array_key_exists(htmlentities($img_url), $image_ranks)) {
					//update the image
					$this_key_id = $image_ranks[$img_url];
					$image = new YahooImage($this_key_id);
					$image->setVars($images[$this_key_id]);
					$image->arrayToTable(array(
						"yim_url" => $img_url,
						"yim_data" => serialize($data),
						"yim_rank" => $rank
					));

				} else {
					$image = new YahooImage();
					$image->createNew(array(
						"key_id" => $this->key_id,
						"yim_url" => $img_url,
						"yim_data" => serialize($data),
						"yim_rank" => $rank
					));
				}

				//add to the new ranks
				$new_ranks[$image->key_value] = $rank;
			}

			//merge ranks and get the new ordering
			$yim_ranks = mergeRanks($old_ranks, $new_ranks);

			//store the new rank for each yqa
			if (count($yim_ranks) > 0) {
				foreach ($yim_ranks as $yim_id => $new_rank) {
					$sql = "
						UPDATE {$multi_prefix}yahoo_image
						SET yim_rank = '$new_rank'
						WHERE yim_id = '$yim_id'
					";
					$update = mysql_query($sql);
				}
			}
		}

		//ensure proper update
		user_post("yahoo_image", $this->key_text);

		//saved the time updated for this keyword
		$update = new KeywordUpdate();
		$update->createNew(array(
			"key_id" => $this->key_value,
			"kup_table" => "yahoo_image",
			"kup_time" => mktime()
		));
	}

	
	function updateYahooImagesOLD() {
		//this can be very time consuming
		ini_set("max_execution_time", "600");

		global $domain, $multi_prefix;

		//find yahoo's image results
		$rest_url = 'http://search.yahooapis.com/ImageSearchService/V1/imageSearch?appid='.$domain->yahoo_app_id.'&query='.urlencode($this->key_text).'&output=php&results=50';
		$array = unserialize(DownloadFileContents($rest_url));

		$imgs = $array['ResultSet']['Result'];
		if (count($imgs) > 0) {

			//find all of the current ranks
			$sql = "
				SELECT yim_id
				FROM {$multi_prefix}yahoo_image
				WHERE key_id = '$this->key_value'
				ORDER BY yim_rank
			";
			$q = mysql_query($sql);
			$old_rank = 0;
			while ($r = mysql_fetch_object($q)) {
				$old_rank++;
				$old_ranks[$r->yim_id] = $old_rank;
			}

			//find all of the existing images in order of their rank
			$images = $this->getYahooImages();
			$image_ranks = condenseArray($images, "yim_url", "yim_id");

			//store all new images in order of rank
			$rank = 0;
			foreach ($imgs as $data) {//testshowarray($data);

				$rank++;

				//do we need an image update or is this a new image to the system?
				$img_url = $data['Url'];
				if (@array_key_exists(htmlentities($img_url), $image_ranks)) {
					//update the image
					$this_key_id = $image_ranks[$img_url];
					$image = new YahooImage($this_key_id);
					$image->setVars($images[$this_key_id]);
					$image->arrayToTable(array(
						"yim_url" => $img_url,
						"yim_data" => serialize($data),
						"yim_rank" => $rank
					));

				} else {
					$image = new YahooImage();
					$image->createNew(array(
						"key_id" => $this->key_id,
						"yim_url" => $img_url,
						"yim_data" => serialize($data),
						"yim_rank" => $rank
					));
				}

				//add to the new ranks
				$new_ranks[$image->key_value] = $rank;
			}

			//merge ranks and get the new ordering
			$yim_ranks = mergeRanks($old_ranks, $new_ranks);

			//store the new rank for each yqa
			if (count($yim_ranks) > 0) {
				foreach ($yim_ranks as $yim_id => $new_rank) {
					$sql = "
						UPDATE {$multi_prefix}yahoo_image
						SET yim_rank = '$new_rank'
						WHERE yim_id = '$yim_id'
					";
					$update = mysql_query($sql);
				}
			}
		}

		//ensure proper update
		user_post("yahoo_image", $this->key_text);

		//saved the time updated for this keyword
		$update = new KeywordUpdate();
		$update->createNew(array(
			"key_id" => $this->key_value,
			"kup_table" => "yahoo_image",
			"kup_time" => mktime()
		));
	}




	function updateYahooNews() {
		//this can be very time consuming
		ini_set("max_execution_time", "600");

		global $domain, $multi_prefix;

		//only create this update if one had not been done within the number of hours assigned via admin panel
		$last_good = mktime() - ($domain->yahoo_news_update_frequency * 60 * 60);
		$sql = "
			SELECT kup_id
			FROM {$multi_prefix}keyword_update
			WHERE kup_table = 'yahoo_news'
			AND key_id = '$this->key_value'
			AND kup_time > '$last_good'
			LIMIT 0,1
		";
		$q = mysql_query($sql);
		$total = mysql_num_rows($q);
		
		if ($total < 1) {
			//an update is required

			//turn opff display errors in case we get a bad request from Yahoo
			ini_set("display_errors", "Off");

			//find yahoo's news results
			
			$rest_url = 'http://api.bing.net/json.aspx?Appid=' . $domain->bing_app_id. '&sources=news&query=' . urlencode($this->key_text);

			$array = DownloadFileContents($rest_url);

			$jsonobj = json_decode($array);
			//print_r($jsonobj);
		
			$news = array();
			foreach($jsonobj->SearchResponse->News->Results as $value)
			{
					/*
					[Title] => Zynga Post-IPO: Get Back to Work!
                                    [Url] => http://www.businessinsider.com/zynga-post-ipo-get-back-to-work-2011-12
                                    [Source] => The Business Insider
                                    [Snippet] => Going public has an effect on company culture, a fact that founders and CEOs worry about heading up to the big day. Mark Zuckerberg, for example, is reported to have put off the Facebook IPO in order to keep his employees ‘hungry’. Now that Zynga is ...
                                    [Date] => 2011-12-19T12:52:00Z
                                    [BreakingNews] => 0
                                )
                    */
					
					/*
					Result 	Contains each individual response.
Title 	The title of the article.
Summary 	Summary text associated with the article.
Url 	The URL for the article.
ClickUrl 	The URL for linking to the article. See URL linking for more information.
NewsSource 	The company that distributed the news article, such as API or BBC.
NewsSourceUrl 	The URL for the news source.
Language 	

The language the article is written in.
PublishDate 	The date the article was first published, in unix timestamp format.
ModificationDate 	The date the article was last modified, in unix timestamp format.
Thumbnail 	The URL of a thumbnail file associated with the article, if present, and its height and width in pixels. 
*/
					
					
				$myimage = array();
				$bingresult = serialize($value);
					
				$myimage['Title']  = $value->Title;
				$myimage['Summary'] = $value->Snippet;
				$myimage['Url'] = $value->Url;
				$myimage['ClickUrl'] = $value->Url;
				$myimage['NewsSource'] = $value->Source;
				$myimage['NewsSourceUrl'] = $value->Url;
				
				$myimage['PublishDate'] = strtotime($value->Date);
				$myimage['ModificationDate'] = strtotime($value->Date);
					
				$news[] = $myimage;
					
                                
			}
			
		
			if (count($news) > 0) {
				//find all other urls (from the past 10 days) so that there is no overlapping content
				$ten_days_ago = mktime() - (10 * 24 * 60 * 60);
				$sql = "
					SELECT ynw_title
					FROM {$multi_prefix}yahoo_news
					WHERE ynw_time_p > '$ten_days_ago'
				";
				$news_titles = getArrayFromSql('ynw_title', $sql);

				foreach ($news as $data) {
					if (@array_key_exists(htmlentities($data['Title']), $news_titles)) {
						//we already have this story
					} else {
						$n = new YahooNews();
						$n->createNew(array(
							"key_id" => $this->key_id,
							"ynw_title" => $data['Title'],
							"ynw_data" => serialize($data),
							"ynw_time_p" => $data['PublishDate']
						));
					}
				}
			}

			//saved the time updated for this keyword
			$update = new KeywordUpdate();
			$update->createNew(array(
				"key_id" => $this->key_value,
				"kup_table" => "yahoo_news",
				"kup_time" => mktime()
			));
		}
	}


	
		function updateYahooNewsOld() {
		//this can be very time consuming
		ini_set("max_execution_time", "600");

		global $domain, $multi_prefix;

		//only create this update if one had not been done within the number of hours assigned via admin panel
		$last_good = mktime() - ($domain->yahoo_news_update_frequency * 60 * 60);
		$sql = "
			SELECT kup_id
			FROM {$multi_prefix}keyword_update
			WHERE kup_table = 'yahoo_news'
			AND key_id = '$this->key_value'
			AND kup_time > '$last_good'
			LIMIT 0,1
		";
		$q = mysql_query($sql);
		$total = mysql_num_rows($q);

		if ($total < 1) {
			//an update is required

			//turn opff display errors in case we get a bad request from Yahoo
			ini_set("display_errors", "Off");

			//find yahoo's news results
			$rest_url = 'http://search.yahooapis.com/NewsSearchService/V1/newsSearch?appid='.$domain->yahoo_app_id.
						'&query='.urlencode($this->key_text).'&output=php&results=10&sort=date';
			$array = unserialize(DownloadFileContents($rest_url));

			$news = $array['ResultSet']['Result'];
			if (count($news) > 0) {
				//find all other urls (from the past 10 days) so that there is no overlapping content
				$ten_days_ago = mktime() - (10 * 24 * 60 * 60);
				$sql = "
					SELECT ynw_title
					FROM {$multi_prefix}yahoo_news
					WHERE ynw_time_p > '$ten_days_ago'
				";
				$news_titles = getArrayFromSql('ynw_title', $sql);

				foreach ($news as $data) {
					if (@array_key_exists(htmlentities($data['Title']), $news_titles)) {
						//we already have this story
					} else {
						$n = new YahooNews();
						$n->createNew(array(
							"key_id" => $this->key_id,
							"ynw_title" => $data['Title'],
							"ynw_data" => serialize($data),
							"ynw_time_p" => $data['PublishDate']
						));
					}
				}
			}

			//saved the time updated for this keyword
			$update = new KeywordUpdate();
			$update->createNew(array(
				"key_id" => $this->key_value,
				"kup_table" => "yahoo_news",
				"kup_time" => mktime()
			));
		}
	}
	
	

	function updateYahooLinks() {
		//this can be very time consuming
		ini_set("max_execution_time", "600");

		global $domain, $multi_prefix ;

		$rest_url = 'http://api.bing.net/json.aspx?Appid=' . $domain->bing_app_id. '&sources=web&query=' . urlencode($this->key_text);
		
		
		$links = array();
		$array = DownloadFileContents($rest_url);
		
		
		$jsonobj = json_decode($array);
		
		//print_R($jsonobj);
		
		foreach($jsonobj->SearchResponse->Web->Results as $value)
		{
			/*
			  [Title] => Zynga | Connecting the World Through Games
                                    [Description] => As part of our efforts to improve our service, we have updated our Terms of Service and Privacy Policy. We hope you'll take some time to review them.
                                    [Url] => http://www.zynga.com/
                                    [CacheUrl] => http://cc.bingj.com/cache.aspx?q=zynga&d=4760559313355280&w=d1b6284f,8a2fb22a
                                    [DisplayUrl] => www.zynga.com
                                    [DateTime] => 2011-12-14T08:22:00Z

         		*/
			$myimage = array();
			$bingresult = serialize($value);
			
			$myimage['Title']  = $value->Title;
			$myimage['Summary'] = $value->Description;
			$myimage['Url'] = $value->Url;
			$myimage['ClickUrl'] = $value->Url;
	
			
			$links[] = $myimage;
			
		}

		
	

		
		if (count($links) > 0) {

			//find all of the current ranks
			$sql = "
				SELECT ylk_id
				FROM {$multi_prefix}yahoo_link
				WHERE key_id = '$this->key_value'
				ORDER BY ylk_rank
			";
			$q = mysql_query($sql);
			$old_rank = 0;
			while ($r = mysql_fetch_object($q)) {
				$old_rank++;
				$old_ranks[$r->ylk_id] = $old_rank;
			}

			//store all new images in order of rank
			$rank = 0;
			foreach ($links as $data) {

				$rank++;

				//do we need an image rank update or is this a new image to the system?
				$info = $data['Url'];
				if (@array_key_exists(htmlentities($info), $link_ranks)) {
					//just update the rank
					$this_key_id = $link_ranks[$info];
					$link = new YahooLink($this_key_id);
					$link->setVars($old_links[$this_key_id]);

				} else {
					$link = new YahooLink();
					$link->createNew(array(
						"key_id" => $this->key_id,
						"ylk_url" => $info,
						"ylk_data" => serialize($data),
						"ylk_rank" => $rank
					));
				}

				//add to the new ranks
				$new_ranks[$link->key_value] = $rank;
			}

			//merge ranks and get the new ordering
			$ylk_ranks = mergeRanks($old_ranks, $new_ranks);

			//store the new rank for each yqa
			if (count($ylk_ranks) > 0) {
				foreach ($ylk_ranks as $ylk_id => $new_rank) {
					$sql = "
						UPDATE {$multi_prefix}yahoo_link
						SET ylk_rank = '$new_rank'
						WHERE ylk_id = '$ylk_id'
					";
					$update = mysql_query($sql);
				}
			}
		}

		//ensure proper update
		user_post("yahoo_link", $this->key_text);

		//saved the time updated for this keyword
		$update = new KeywordUpdate();
		$update->createNew(array(
			"key_id" => $this->key_value,
			"kup_table" => "yahoo_link",
			"kup_time" => mktime()
		));
	}


function updateYahooLinksOLD() {
		//this can be very time consuming
		ini_set("max_execution_time", "600");

		global $domain, $multi_prefix ;

		//find yahoo's image results
		$rest_url = 'http://search.yahooapis.com/WebSearchService/V1/webSearch?appid='.$domain->yahoo_app_id.
					'&query='.urlencode($this->key_text).'&output=php&results=50&format=html';
		$array = unserialize(DownloadFileContents($rest_url));

		$links = $array['ResultSet']['Result'];
		if (count($links) > 0) {

			//find all of the current ranks
			$sql = "
				SELECT ylk_id
				FROM {$multi_prefix}yahoo_link
				WHERE key_id = '$this->key_value'
				ORDER BY ylk_rank
			";
			$q = mysql_query($sql);
			$old_rank = 0;
			while ($r = mysql_fetch_object($q)) {
				$old_rank++;
				$old_ranks[$r->ylk_id] = $old_rank;
			}

			//store all new images in order of rank
			$rank = 0;
			foreach ($links as $data) {

				$rank++;

				//do we need an image rank update or is this a new image to the system?
				$info = $data['Url'];
				if (@array_key_exists(htmlentities($info), $link_ranks)) {
					//just update the rank
					$this_key_id = $link_ranks[$info];
					$link = new YahooLink($this_key_id);
					$link->setVars($old_links[$this_key_id]);

				} else {
					$link = new YahooLink();
					$link->createNew(array(
						"key_id" => $this->key_id,
						"ylk_url" => $info,
						"ylk_data" => serialize($data),
						"ylk_rank" => $rank
					));
				}

				//add to the new ranks
				$new_ranks[$link->key_value] = $rank;
			}

			//merge ranks and get the new ordering
			$ylk_ranks = mergeRanks($old_ranks, $new_ranks);

			//store the new rank for each yqa
			if (count($ylk_ranks) > 0) {
				foreach ($ylk_ranks as $ylk_id => $new_rank) {
					$sql = "
						UPDATE {$multi_prefix}yahoo_link
						SET ylk_rank = '$new_rank'
						WHERE ylk_id = '$ylk_id'
					";
					$update = mysql_query($sql);
				}
			}
		}

		//ensure proper update
		user_post("yahoo_link", $this->key_text);

		//saved the time updated for this keyword
		$update = new KeywordUpdate();
		$update->createNew(array(
			"key_id" => $this->key_value,
			"kup_table" => "yahoo_link",
			"kup_time" => mktime()
		));
	}
	

	function updateYahooQAs($yahoo_data = '') {

		global $domain, $multi_prefix;
		
		ini_set('max_execution_time',0);

		if (trim($yahoo_data) == '') {
			//find yahoo's questions
			$rest_url = 'http://answers.yahooapis.com/AnswersService/V1/questionSearch?appid='.$domain->yahoo_app_id.
						'&query='.urlencode($this->key_text).'&output=php&results=35&region='.$domain->region;
			
			
			$data = DownloadFileContents($rest_url);	
			//echo $data;
			
			$array = unserialize($data);
		} else {
			//load directly from data passed
			$array = unserialize($yahoo_data);
		}
		$results = $array['Questions'];
		
	

		//testShowArray($results);die();

		//find the full q/a thread for this question
		$rank = 0;
		if (count($results) > 0) {

			//find all of the current ranks
			$sql = "
				SELECT yqa_id
				FROM {$multi_prefix}yahoo_qa
				WHERE key_id = '$this->key_value'
				ORDER BY yqa_rank
			";
			$q = mysql_query($sql);
			$old_rank = 0;
			while ($r = mysql_fetch_object($q)) {
				$old_rank++;
				$old_ranks[$r->yqa_id] = $old_rank;
			}

			foreach ($results as $qa) {
				//load found details
				$y_q_id = $qa['id'];
				$y_q_subj = $qa['Subject'];
				$y_q_cont = $qa['Content'];

				//find the full thread if there are one or more answers
				if ($qa['NumAnswers'] > 0) {

					//increment rank
					$rank++;

					global $domain;

					//find all the answers
					$rest_url = 'http://answers.yahooapis.com/AnswersService/V1/getQuestion?appid='.$domain->yahoo_app_id.
								'&output=php&question_id='.$y_q_id;
					$array = array();
					$array = unserialize(DownloadFileContents($rest_url));
					$results_answers = $array['Questions']['0'];

					//is this an update or a new entry?
					$sql = "
						SELECT yqa_id
						FROM {$multi_prefix}yahoo_qa
						WHERE y_q_id = '$y_q_id'
						AND key_id = '$this->key_value'
					";
					$q = mysql_query($sql);
					if (mysql_num_rows($q) < 1) {
						//this is new
						$yqa = new YahooQA();
						$yqa->createNew(array(
							"key_id" => $this->key_id,
							"y_q_id" => $y_q_id,
							"y_q_subj" => $y_q_subj,
							"y_q_cont" => $y_q_cont,
							"yqa_data" => serialize($results_answers)
						));

					} else {
						//this is just an update
						$r = mysql_fetch_object($q);
						$yqa = new YahooQA($r->yqa_id);
						$details = array(
							"y_q_subj" => $y_q_subj,
							"y_q_cont" => $y_q_cont,
							"yqa_data" => serialize($results_answers)
						);
						$yqa->arrayToTable($details);
					}

					//add to the new ranks
					$new_ranks[$yqa->key_value] = $rank;

				} else {
					//there are no answers yet for this question.
					//we'll wait to store anything
				}

				//merge ranks and get the new ordering
				$yqa_ranks = mergeRanks($old_ranks, $new_ranks);

				//store the new rank for each yqa
				if (count($yqa_ranks) > 0) {
					foreach ($yqa_ranks as $yqa_id => $new_rank) {
						$sql = "
							UPDATE {$multi_prefix}yahoo_qa
							SET yqa_rank = '$new_rank'
							WHERE yqa_id = '$yqa_id'
						";
						$update = mysql_query($sql);
					}
				}
			}
		}

		//ensure proper update
		user_post("yahoo_qa", $this->key_text);

		//saved the time updated for this keyword
		$update = new KeywordUpdate();
		$update->createNew(array(
			"key_id" => $this->key_value,
			"kup_table" => "yahoo_qa",
			"kup_time" => mktime()
		));
	}



	function updateExistingYahooQAs($yahoo_data = '') {

		global $domain;

		//find current yqas
		$qas = $this->getYahooQAs();
		if (count($qas) > 0) {
			foreach ($qas as $id => $details) {

				$yqa = new YahooQA($id);
				$yqa->setVars($details);

				//find all the answers
				$rest_url = 'http://answers.yahooapis.com/AnswersService/V1/getQuestion?appid='.$domain->yahoo_app_id.
							'&output=php&question_id='.$yqa->y_q_id;
				$array = array();
				$array = unserialize(DownloadFileContents($rest_url));
				$results = $array['Questions']['0'];

				$yqa->arrayToTable(array("yqa_data" => serialize($results)));
			}
		}
	}






	function updateYouTube() {
		//this can be very time consuming
		ini_set("max_execution_time", "600");

		global $domain, $multi_prefix;

		//only create this update if one had not been done within the number of hours assigned via admin panel
		$last_good = mktime() - ($domain->yahoo_news_update_frequency * 60 * 60);
		$sql = "
			SELECT kup_id
			FROM {$multi_prefix}keyword_update
			WHERE kup_table = 'youtube_video'
			AND key_id = '$this->key_value'
			AND kup_time > '$last_good'
			LIMIT 0,1
		";
		$q = mysql_query($sql);
		$total = mysql_num_rows($q);

		if ($total < 1) {
			//an update is required

			/**
			 * @see Zend_Loader
			 */
			require_once 'Zend/Loader.php';

			/**
			 * @see Zend_Gdata_YouTube
			 */
			Zend_Loader::loadClass('Zend_Gdata_YouTube');


		    $yt = new Zend_Gdata_YouTube();
		    $query = $yt->newVideoQuery();
		    $query->setQuery($this->key_text);

		    $query->setMaxResults(10);

       		 $feed = $yt->getVideoFeed($query);

			$sql = "
					SELECT ytv_videoid
					FROM {$multi_prefix}youtube_video

				";
				$video_ids = getArrayFromSql('ytv_videoid', $sql);


			if (count($feed ) > 0) {


			 foreach ($feed as $entry) {
				$videoId = $entry->getVideoId();
	       		 $thumbnailUrl = $entry->mediaGroup->thumbnail[0]->url;

	       		 $videoTitle = $entry->mediaGroup->title;
	       		 $videoDescription = $entry->mediaGroup->description;
				if (@array_key_exists(htmlentities($videoId), $video_ids)) {
						//we already have this story
					} else {
						$n = new YouTubeVideo();
						$n->createNew(array(
							"key_id" => $this->key_id,
							"ytv_videoid" => $videoId,
							"ytv_videotitle" => $videoTitle,
							"ytv_videodescription" => $videoDescription ,
							"ytv_videothumburl" => $thumbnailUrl
						));
					}


			 }


			}

			//saved the time updated for this keyword
			$update = new KeywordUpdate();
			$update->createNew(array(
				"key_id" => $this->key_value,
				"kup_table" => "youtube_video",
				"kup_time" => mktime()
			));
		}
	}

function updateBingNews() {
		//this can be very time consuming
		ini_set("max_execution_time", "600");

		global $domain, $multi_prefix;

		//only create this update if one had not been done within the number of hours assigned via admin panel
		$last_good = mktime() - ($domain->yahoo_news_update_frequency * 60 * 60);
		$sql = "
			SELECT kup_id
			FROM {$multi_prefix}keyword_update
			WHERE kup_table = 'bing_news'
			AND key_id = '$this->key_value'
			AND kup_time > '$last_good'
			LIMIT 0,1
		";
		$q = mysql_query($sql);
		$total = mysql_num_rows($q);

		if ($total < 1) {
			//an update is required

			//turn off display errors in case we get a bad request from Bing
			ini_set("display_errors", "Off");

			//find Bing's news results
			$rest_url = 'http://api.search.live.net/xml.aspx?Appid='.$domain->bing_app_id.
						'&sources=news&query='.urlencode($this->key_text);
			$array = unserialize(DownloadFileContents($rest_url));

			$news = $array['ResultSet']['Result'];
			if (count($news) > 0) {
				//find all other urls (from the past 10 days) so that there is no overlapping content
				$ten_days_ago = mktime() - (10 * 24 * 60 * 60);
				$sql = "
					SELECT bn_title
					FROM {$multi_prefix}bing_news
					WHERE bn_time_p > '$ten_days_ago'
				";
				$news_titles = getArrayFromSql('bn_title', $sql);

				foreach ($news as $data) {
					if (@array_key_exists(htmlentities($data['Title']), $news_titles)) {
						//we already have this story
					} else {
						$n = new BingNews();
						$n->createNew(array(
							"key_id" => $this->key_id,
							"bn_title" => $data['Title'],
							"bn_snippet" => serialize($data),
							"bn_time_p" => $data['PublishDate']
						));
					}
				}
			}

			//saved the time updated for this keyword
			$update = new KeywordUpdate();
			$update->createNew(array(
				"key_id" => $this->key_value,
				"kup_table" => "bing_news",
				"kup_time" => mktime()
			));
		}
	}



	function createNew($array) {
		global $multi_prefix;
		//only allows a maximum of MAX_KEYWORDS keys for any top_key_id
		//check fr key count
		$top_key = $array['top_key_id'];
		$sql = "
			SELECT COUNT(key_id)
			FROM {$multi_prefix}keyword
			WHERE top_key_id = '$top_key'
		";
		$total = mysql_result(mysql_query($sql), 0);
		if ($total >= MAX_KEYWORDS) {
			return false;
		} else {
			parent::createNew($array);
			return true;
		}
	}

	function destroyObject() {
		global $multi_prefix;
		//this should be used with care.

		if ($this->top_key_id == '0') {
			//delete all subkeys
			$keys = getKeywords($this->key_id);
			if (count($keys) > 0) {
				foreach ($keys as $id => $details) {
					$key = new Keyword($id);
					$key->setVars($details);
					$key->destroyObject();
				}
			}
		}

		$delete = mysql_query("DELETE FROM {$multi_prefix}yahoo_image WHERE key_id = '$this->key_value'");
		$delete = mysql_query("DELETE FROM {$multi_prefix}yahoo_link WHERE key_id = '$this->key_value'");
		$delete = mysql_query("DELETE FROM {$multi_prefix}yahoo_qa WHERE key_id = '$this->key_value'");
		$delete = mysql_query("DELETE FROM {$multi_prefix}keyword_update WHERE key_id = '$this->key_value'");
		$delete = mysql_query("DELETE FROM {$multi_prefix}youtube_video WHERE key_id = '$this->key_value'");
		$delete = mysql_query("DELETE FROM {$multi_prefix}bing_news WHERE key_id = '$this->key_value'");
		$this->assignRank(0);

		parent::destroyObject();
	}
}


class KeywordUpdate extends DbTable
{
	function KeywordUpdate($id = 0) {
		$db_table = "keyword_update";
		$key_name = "kup_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}

	/*
		NOTES
		- The kup_table should correespond to the db_table that was updated.
	*/
}
?>