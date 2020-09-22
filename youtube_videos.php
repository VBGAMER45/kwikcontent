<?php
/***************************************************************************
 *
 *   File                 : yahoo_images.php
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

//$smarty->debugging = true;

//ensure a keyword has been passed
if ($k > 0) {
	$key = new Keyword($k);
	$key->loadVars();

	if (!$key->key_id > 0) {
		//this is an old/dead keyword
		$redirect = true;
	}
	$key->images_title = $key->getTitle('youtube_videos');

	//throw the key to smarty
	$smarty->assign("keyword", get_object_vars($key));

} else {
	$redirect = true;
}
if ($redirect) {
	$url = BASE_URL;
	header("Location: $url");
}

//find images for this keyword
$videos = $key->getYouTube();
if (count($videos) > 0) {
	foreach ($videos as $id => $details) {
		//$img = new YouTubeVideo($id);
		//$img->setVars($details);

		//$img->ripData();
		//$img->yim_url = $img->getLocation();


		$videos[$id] = array_merge($details,array('videourl' => BASE_FOLDER.$domain->slugs['youtube_video'].$id.'/'.getFakeFilename($details['ytv_videotitle']).'.html'));


		//$images[$img->yim_id] = get_object_vars($img);
	}


	$smarty->assign("videos", $videos);
}


//create breadcrumb
$breadcrumb = '<a href="'.BASE_FOLDER.'">Home</a> > <a href="'.$key->getKeywordLocation().'">'.ucwords($key->getTitle('keyword')).'</a> > '.ucwords($key->getTitle('youtube_videos'));
$smarty->assign("BREADCRUMB", $breadcrumb);

//establish vars for template
$smarty->assign('WINDOW_TITLE', ($domain->domain_window_titles ? $domain->domain_title.$domain->delimiters['window_title'] : '').ucwords($key->getTitle('youtube_videos')));
$smarty->assign('META_DESCRIPTION', ucwords($key->getTitle('youtube_videos')).'. Browse the images we have stored that pertain to '.$key->key_text.'.');
$smarty->assign('META_KEYS', getKeywordString());

//load a header file for any preprocessing
include_once('includes/public_header.php');

//output page
$smarty->display('youtube_videos.htm');

//load a footer file for any postprocessing
include_once('includes/public_footer.php');
?>