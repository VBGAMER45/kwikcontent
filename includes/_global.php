<?php
/***************************************************************************
 *
 *   File                 : includes/_global.php
 *   Software             : Kwikcontent
 *   Version              : 1.1
 *   Release Date         : August 29, 2011
 *   Copyright            : (C) 2009-2012 Samson Software
 *   Contact              : http://www.kwikcontent.com/
 *   License              : Limited to one owner.  My not be reproduced or
 *                          redistributed under any circumstance.
 *
 ***************************************************************************/


// Enter your MySQL database definitions here
define("DB_HOST", "localhost");
define("DB_NAME", "replacewithdbname");
define("DB_USER", "replacewithdbuser");
define("DB_PASS", "replacewithdbpass");

// Prefix for database tables useful if you want to store more than one domain inside a single database.
$multi_prefix = '';
// Directory definitions
define("BASE_FOLDER", "/");
define("BASE_DIRECTORY", $_SERVER['DOCUMENT_ROOT'].BASE_FOLDER);
define("INCLUDES_DIR", BASE_DIRECTORY."includes/");

// Multi Site check code
$multiCheck =  str_replace("www.","",$_SERVER["HTTP_HOST"]);
$config_file = BASE_DIRECTORY . 'settings/'.$multiCheck.'.php';
$isMultiSite = false;
if (is_file($config_file))
{
	include $config_file;
	$isMultiSite = true;
}
else
{
	// EDIT This
	// Provide the root domain for your site
	define("BASE_DOMAIN", "www.yourdomain.com");

	// EDIT This
	// Provide the full path of your site
	define("BASE_URL", "http://www.yourdomain.com/");

}

//Name of the Kwikcontent license holder
// :: WARNING ::
// If the proper name is not used here, your system will eventually begin rendering errors over time
define("KWIKCONTENT_OWNER", "Hiland Terrace");

//assign the proper template folder
define("SMARTY_TEMPLATE_FOLDER", "yahoo_grids_1");



/* Editing below this line is not recommended. */



define("DEMO_MODE", "0");




if (file_exists(BASE_DIRECTORY."templates/custom/" . $multiCheck))
{
	define("SMARTY_BASE_FOLDER", BASE_FOLDER."templates/custom/". $multiCheck."/");
	define("SMARTY_TEMPLATE_URL", BASE_URL."templates/custom/". $multiCheck."/");
	define("SMARTY_TEMPLATE_DIR", BASE_DIRECTORY."templates/custom/". $multiCheck."/");

	define("SMARTY_CLASS_LOC", BASE_DIRECTORY."Smarty/libs/Smarty.class.php");
	define("SMARTY_COMPILE_DIR", BASE_DIRECTORY."templates_c/");
	define("SMARTY_CACHE_DIR", BASE_DIRECTORY."cache/");
	define("SMARTY_CONFIG_DIR", BASE_DIRECTORY."configs/");
}
else
{
		//more Smarty definitions
	define("SMARTY_BASE_FOLDER", BASE_FOLDER."templates/".SMARTY_TEMPLATE_FOLDER."/");
	define("SMARTY_CLASS_LOC", BASE_DIRECTORY."Smarty/libs/Smarty.class.php");
	define("SMARTY_TEMPLATE_URL", BASE_URL."templates/".SMARTY_TEMPLATE_FOLDER."/");
	define("SMARTY_TEMPLATE_DIR", BASE_DIRECTORY."templates/".SMARTY_TEMPLATE_FOLDER."/");
	define("SMARTY_COMPILE_DIR", BASE_DIRECTORY."templates_c/");
	define("SMARTY_CACHE_DIR", BASE_DIRECTORY."cache/");
	define("SMARTY_CONFIG_DIR", BASE_DIRECTORY."configs/");
}


//connect to db
$db_connection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
mysql_select_db(DB_NAME, $db_connection);

if ($isMultiSite == true)
{
	$multiResult = mysql_query("SELECT removed from sites WHERE domain = '$multiCheck'  LIMIT 1", $db_connection);
	$multiRow = mysql_fetch_assoc($multiResult);

	if ($multiRow['removed'] == 1)
		die("Site is disabled");

	// Update hits
	mysql_query("UPDATE sites SET hits = hits + 1 WHERE domain = '$multiCheck'  LIMIT 1", $db_connection);
}

//ensure all vars are in the global scope
extract($_REQUEST);

//ensure proper php configuration (if controllable)
ini_set("allow_url_fopen", "On");

//pull in inclues that are needed on all pages
include_once(INCLUDES_DIR.'_functions.php');
include_once(INCLUDES_DIR.'_classes.php');
include_once(INCLUDES_DIR.'domain.php');

// Setup Zend for Youtube
//ini_set('include_path',ini_get('include_path').':' . BASE_DIRECTORY . 'includes/Zend/:');
ini_set('include_path',  BASE_DIRECTORY . 'includes/Zend/' );

//Initiate Domain
$domain = new Domain();
$domain->loadVars();

/*
	Hard-coded domain configuration
*/

//SLUGS - if these are changed, the .htaccess file needs to also be altered
$domain->slugs['keyword'] = 'information/';
$domain->slugs['articles'] = 'articles/browse/';
$domain->slugs['article'] = 'articles/view/';
$domain->slugs['yahoo_images'] = 'images/browse/';
$domain->slugs['yahoo_image'] = 'images/view/';
$domain->slugs['yahoo_links'] = 'otherresources/browse/';
$domain->slugs['yahoo_qas'] = 'knowledgebase/browse/';
$domain->slugs['yahoo_qa'] = 'knowledgebase/view/';
$domain->slugs['yahoo_news'] = 'news/';
$domain->slugs['static_page'] = 'page/';

$domain->slugs['youtube_videos'] = 'videos/browse/';
$domain->slugs['youtube_video'] = 'videos/view/';

//these are the descriptive titles of content areas in the system
$domain->titles['keyword'] = '* information';
$domain->titles['articles'] = '* articles';
$domain->titles['yahoo_images'] = '* images';
$domain->titles['yahoo_image'] = '* image';
$domain->titles['yahoo_links'] = 'other * resources';
$domain->titles['yahoo_qas'] = '* knowledge base';
$domain->titles['yahoo_news'] = '* news';

$domain->titles['youtube_videos'] = '* videos';
$domain->titles['youtube_video'] = '* video';

//random domain settings
$domain->delimiters['window_title'] = ' - ';
?>
