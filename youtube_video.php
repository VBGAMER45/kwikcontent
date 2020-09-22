<?php
/***************************************************************************
 *
 *   File                 : yahoo_image.php
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


$video = (int) $_REQUEST['video'];
//ensure an image has been passed
if ($video > 0) {

	// Lookup the video information
		$sql = "
			SELECT *
			FROM {$multi_prefix}youtube_video
			WHERE ytv_id = $video
		";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);



	$video_array['title'] = $row['ytv_videotitle'];
	$video_array['description'] = $row['ytv_videodescription'];

	$video_array['videoid'] = $row['ytv_videoid'];


	/**
	 * @see Zend_Loader
	 */
	require_once 'Zend/Loader.php';

	/**
	* @see Zend_Gdata_YouTube
	*/
	Zend_Loader::loadClass('Zend_Gdata_YouTube');

	//find and load the proper key
	$key = new Keyword($row['key_id']);
	$key->loadVars();
	$smarty->assign("keyword", get_object_vars($key));



    $yt = new Zend_Gdata_YouTube();

    $entry = $yt->getVideoEntry($video_array['videoid']);
    $videoTitle = $entry->mediaGroup->title;
    $videoUrl = findFlashUrl($entry);
	$video_array['videourl'] = $videoUrl;

	$smarty->assign("video", $video_array);

} else {
	$redirect = true;
}
if ($redirect) {
	$url = BASE_URL;
	header("Location: $url");
}

//create breadcrumb
$breadcrumb = '<a href="'.BASE_FOLDER.'">Home</a> > <a href="'.$key->getKeywordLocation().'">'.ucwords($key->getTitle('keyword')).'</a> > <a href="'.$key->getYahooImagesLocation().'">'.ucwords($key->getTitle('youtube_videos')).'</a> > '.$video['title'];
$smarty->assign("BREADCRUMB", $breadcrumb);

//establish vars for template
$smarty->assign('WINDOW_TITLE', ($domain->domain_window_titles ? $domain->domain_title.$domain->delimiters['window_title'] : '').ucwords($key->getTitle('youtube_video')).' - '.$video['title']);
$smarty->assign('META_DESCRIPTION', ucwords($key->getTitle('youtube_video')).'.  You are viewing '.$video['title'].'.');
$smarty->assign('META_KEYS', getKeywordString());

//load a header file for any preprocessing
include_once('includes/public_header.php');

//output page
$smarty->display('youtube_video.htm');

//load a footer file for any postprocessing
include_once('includes/public_footer.php');

function findFlashUrl($entry)
{
    foreach ($entry->mediaGroup->content as $content) {
        if ($content->type === 'application/x-shockwave-flash') {
            return $content->url;
        }
    }
    return null;
}
?>