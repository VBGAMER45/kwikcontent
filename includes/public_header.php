<?php
/***************************************************************************
 *
 *   File                 : includes/public_header.php
 *   Software             : Kwikcontent
 *   Version              : 1.1
 *   Release Date         : August 29, 2009
 *   Copyright            : (C) 2009 Samson Software
 *   Contact              : http://www.kwikcontent.com/
 *   License              : Limited to one owner.  My not be reproduced or
 *                          redistributed under any circumstance.
 *
 ***************************************************************************/


//load the domain info into smarty
$domain->google_analytics = html_entity_decode($domain->google_analytics);
$smarty->assign("domain", get_object_vars($domain));

if (!empty($domain->linkadge_key))
{
	// THE FOLLOWING BLOCK IS USED TO RETRIEVE AND DISPLAY LINK INFORMATION.
// PLACE THIS ENTIRE BLOCK IN THE AREA YOU WANT THE DATA TO BE DISPLAYED.

// MODIFY THE VARIABLES BELOW:
// The following variable defines whether links are opened in a new window
// (1 = Yes, 0 = No)
$OpenInNewWindow = "1";

// # DO NOT MODIFY ANYTHING ELSE BELOW THIS LINE!
// ----------------------------------------------
$BLKey = $domain->linkadge_key;

$QueryString  = "LinkUrl=".urlencode((($_SERVER['HTTPS']=='on')?'https://':'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$QueryString .= "&Key=" .urlencode($BLKey);
$QueryString .= "&OpenInNewWindow=" .urlencode($OpenInNewWindow);


	$ch = curl_init ("http://brokerage.linkadage.com/engine.php?".$QueryString);
    curl_setopt ($ch, CURLOPT_HEADER, 0);
     curl_setopt ($ch,  CURLOPT_RETURNTRANSFER,  1);
   
    $curlResult = curl_exec ($ch);

    if(curl_error($ch))
         $curlResult =  "Error processing request";

    curl_close ($ch);

$smarty->assign("linkadgecode",  "<!--linkadage-->" . $curlResult);    
    
/*

if(intval(get_cfg_var('allow_url_fopen')) && function_exists('readfile')) {
    @readfile("http://brokerage.linkadage.com/engine.php?".$QueryString);
}
elseif(intval(get_cfg_var('allow_url_fopen')) && function_exists('file')) {
    if($content = @file("http://brokerage.linkadage.com/engine.php?".$QueryString))
        print @join('', $content);
}
elseif(function_exists('curl_init')) {
    $ch = curl_init ("http://brokerage.linkadage.com/engine.php?".$QueryString);
    curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_exec ($ch);

    if(curl_error($ch))
        print "Error processing request";

    curl_close ($ch);
}
else {
    print "It appears that your web host has disabled all functions for handling remote pages and as a result the BackLinks software will not function on your web page. Please contact your web host for more information.";
}
*/

}



//create keyword navigation
$top_keys = getKeywords(0);
if (count($top_keys) > 0) {
	foreach ($top_keys as $id => $details) {
		$key = new Keyword($id);
		$key->setVars($details);
		$top_keys[$id]['url_keyword'] = $key->getKeywordLocation();
	}
}
$smarty->assign("top_keys_nav", $top_keys);

//create static page navigation
include_once(INCLUDES_DIR.'static_page.php');
$top_sps_nav = getStaticPages(0);
if (count($top_sps_nav) > 0) {
	foreach ($top_sps_nav as $id => $details) {
		$tsp = new StaticPage($id);
		$tsp->setVars($details);
		$top_sps_nav[$id]['sp_url'] = $tsp->getLocation();
	}
}
$smarty->assign("top_sps_nav", $top_sps_nav);


/*
	Begin auto-update checks
*/
if ($domain->yahoo_auto_update == '1') {

	$auto_update_frequency = $domain->yahoo_auto_update_frequency;

	$now = mktime();
	$last_good_update = $now - ($auto_update_frequency * 60 * 60 * 24);
	$key_array = $smarty->get_template_vars("keyword");
	$key_id = $key_array['key_id'];

	//run updates (if needed)
	switch (getScriptName()) {
		case "yahoo_answers.php":
			//find the most recent update time for this keyword
			$sql = "
				SELECT kup_time
				FROM {$multi_prefix}keyword_update
				WHERE key_id = '$key_id'
				AND kup_table LIKE 'yahoo_qa'
				ORDER BY kup_time DESC
				LIMIT 0,1
			";
			$q = mysql_query($sql);
			if (mysql_num_rows($q) > 0) {
				$r = mysql_fetch_object($q);
				if ($r->kup_time < $last_good_update) $update_content = true;
			} else {
				//this has never been updated
				$update_content = true;
			}

			if ($update_content) {
				//turn opff display errors in case we get a bad request from Yahoo
				ini_set("display_errors", "Off");

				//load the keyword
				$key = new Keyword($key_id);
				$key->setVars($key_array);

				//grab the content
				$key->updateYahooQAs();
			}

			break;


		case "yahoo_links.php":
			//find the most recent update time for this keyword
			$sql = "
				SELECT kup_time
				FROM {$multi_prefix}keyword_update
				WHERE key_id = '$key_id'
				AND kup_table LIKE 'yahoo_link'
				ORDER BY kup_time DESC
				LIMIT 0,1
			";
			$q = mysql_query($sql);
			if (mysql_num_rows($q) > 0) {
				$r = mysql_fetch_object($q);
				if ($r->kup_time < $last_good_update) $update_content = true;
			} else {
				//this has never been updated
				$update_content = true;
			}

			if ($update_content) {
				//turn opff display errors in case we get a bad request from Yahoo
				ini_set("display_errors", "Off");

				//load the keyword
				$key = new Keyword($key_id);
				$key->setVars($key_array);

				//grab the content
				$key->updateYahooLinks();
			}

			break;


		case "yahoo_images.php":
			//find the most recent update time for this keyword
			$sql = "
				SELECT kup_time
				FROM {$multi_prefix}keyword_update
				WHERE key_id = '$key_id'
				AND kup_table LIKE 'yahoo_image'
				ORDER BY kup_time DESC
				LIMIT 0,1
			";
			$q = mysql_query($sql);
			if (mysql_num_rows($q) > 0) {
				$r = mysql_fetch_object($q);
				if ($r->kup_time < $last_good_update) $update_content = true;
			} else {
				//this has never been updated
				$update_content = true;
			}

			if ($update_content) {
				//turn opff display errors in case we get a bad request from Yahoo
				ini_set("display_errors", "Off");

				//load the keyword
				$key = new Keyword($key_id);
				$key->setVars($key_array);

				//grab the content
				$key->updateYahooImages();
			}

			break;


		case "youtube_video.php":
			//find the most recent update time for this keyword
			$sql = "
				SELECT kup_time
				FROM {$multi_prefix}keyword_update
				WHERE key_id = '$key_id'
				AND kup_table LIKE 'youtube_video'
				ORDER BY kup_time DESC
				LIMIT 0,1
			";
			$q = mysql_query($sql);
			if (mysql_num_rows($q) > 0) {
				$r = mysql_fetch_object($q);
				if ($r->kup_time < $last_good_update) $update_content = true;
			} else {
				//this has never been updated
				$update_content = true;
			}

			if ($update_content) {
				//turn opff display errors in case we get a bad request from Yahoo
				ini_set("display_errors", "Off");

				//load the keyword
				$key = new Keyword($key_id);
				$key->setVars($key_array);

				//grab the content
				$key->updateYouTube();
			}

			break;


		default: break;
	}
} //end auto updates check

//check for page inserts
$sql = "
	SELECT *
	FROM {$multi_prefix}page_insert
	WHERE pi_page LIKE '".$_SERVER['REDIRECT_URL']."'
	LIMIT 0,1
";
$q = mysql_query($sql);
if (mysql_num_rows($q) > 0) {
	$r = mysql_fetch_assoc($q);
	if (count($r) > 0) {
		foreach ($r as $key => $value) {
			$pi[$key] = html_entity_decode($value);;
		}
	}
	$smarty->assign("page_insert", $pi);
}
?>
