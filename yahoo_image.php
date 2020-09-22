<?php
/***************************************************************************
 *
 *   File                 : yahoo_image.php
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

//$smarty->debugging = true;

//ensure an image has been passed
if ($yim > 0) {
	$img = new YahooImage($yim);
	$img->loadVars();

	if (!$img->yim_id > 0) {
		//this is an old/dead keyword
		$redirect = true;
	}

	//load the needed vars
	$img->ripData();

	//throw the key to smarty
	$smarty->assign("image", get_object_vars($img));

	//find and load the proper key
	$key = new Keyword($img->key_id);
	$key->loadVars();
	$smarty->assign("keyword", get_object_vars($key));

} else {
	$redirect = true;
}
if ($redirect) {
	$url = BASE_URL;
	header("Location: $url");
}

//create breadcrumb
$breadcrumb = '<a href="'.BASE_FOLDER.'">Home</a> > <a href="'.$key->getKeywordLocation().'">'.ucwords($key->getTitle('keyword')).'</a> > <a href="'.$key->getYahooImagesLocation().'">'.ucwords($key->getTitle('yahoo_images')).'</a> > '.$img->img_info['Title'];
$smarty->assign("BREADCRUMB", $breadcrumb);

//establish vars for template
$smarty->assign('WINDOW_TITLE', ($domain->domain_window_titles ? $domain->domain_title.$domain->delimiters['window_title'] : '').ucwords($key->getTitle('yahoo_image')).' - '.$img->img_info['Title']);
$smarty->assign('META_DESCRIPTION', ucwords($key->getTitle('yahoo_image')).'.  You are viewing '.$img->img_info['Title'].'.');
$smarty->assign('META_KEYS', getKeywordString());

//load a header file for any preprocessing
include_once('includes/public_header.php');

//output page
$smarty->display('yahoo_image.htm');

//load a footer file for any postprocessing
include_once('includes/public_footer.php');
?>
