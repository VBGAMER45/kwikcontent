<?php
/***************************************************************************
 *
 *   File                 : yahoo_answer.php
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

//ensure an answer has been passed
if ($yqa > 0) {
	$yqa = new YahooQA($yqa);
	$yqa->loadVars();

	if (!$yqa->yqa_id > 0) {
		//this is an old/dead keyword
		$redirect = true;
	}

	//load the needed vars
	$yqa->ripData();

	//throw the key to smarty
	$smarty->assign("answer", get_object_vars($yqa));

	//find and load the proper key
	$key = new Keyword($yqa->key_id);
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
$question = html_entity_decode($yqa->y_q_subj);
$breadcrumb = '<a href="'.BASE_URL.'">Home</a> > <a href="'.$key->getKeywordLocation().'">'.ucwords($key->getTitle('keyword')).'</a> > <a href="'.$key->getYahooQAsLocation().'">'.ucwords($key->getTitle('yahoo_qas')).'</a> > '.$question;
$smarty->assign("BREADCRUMB", $breadcrumb);

//establish vars for template
$smarty->assign('WINDOW_TITLE', ($domain->domain_window_titles ? $domain->domain_title.$domain->delimiters['window_title'] : '').$question);
$smarty->assign('META_DESCRIPTION', 'Answering the question - '.$question);
$smarty->assign('META_KEYS', getKeywordString());

//load a header file for any preprocessing
include_once('includes/public_header.php');

//output page
$smarty->display('yahoo_answer.htm');

//load a footer file for any postprocessing
include_once('includes/public_footer.php');
?>
