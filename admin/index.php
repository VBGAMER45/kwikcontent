<?php
/***************************************************************************
 *
 *   File                 : admin/index.php
 *   Software             : Kwikcontent
 *   Version              : 1.1
 *   Release Date         : August 29, 2009
 *   Copyright            : (C) 2009 Samson Software
 *   Contact              : http://www.kwikcontent.com/
 *   License              : Limited to one owner.  My not be reproduced or
 *                          redistributed under any circumstance.
 *
 ***************************************************************************/

include_once("../includes/_global.php");

//page definitions
define("WINDOW_TITLE", "Home");
define("PAGE_TITLE", "Home");
?>
<?
include("_header.php");
?>
<style type="text/css">
h3 {
	margin-bottom: 5px;
}
p {
	margin: 0px;
	font-size: 13px;
}
li {
	font-size: 13px;
}
</style>

<h2>Getting Started</h2>
<p>Setting up your domain is a fairly simple process.  If you run into any problems, or have any questions, please visit the <a href="http://www.kwikcontent.com/supportforum" target="_blank">kwikcontent support forum</a>.</p>

<ol>
	<li>You will first need to update your domain settings. This can be done by clicking the Settings link from your navigation menu above. Fill in all of the proper information on that page. </li>
	<li>You will now want to configure the top-level keywords for your site. Click the Keywords link to begin. For each top-level keyword you create, you can allow kwikcontent to generate a bank of sub-level keywords by clicking its appropriate Update Sub-level Keywords link.</li>
	<li>You should now adjust the rank of both your top-level and sub-level keywords. It is important to rank your keywords before updating your content. Kwikcontent attempts to not duplicate content from one keyword to another. Therefore, if two keywords potentially share the same content, it will assign that content to the keyword with the highest rank first. </li>
	<li>You are now ready to begin generating content. This can only be done for one top-level keyword at a time. Check the boxes next to each keyword you have stored and choose to update the Yahoo! Images. Do this for each of your top-level keywords. <span style="font-weight: bold">Note:</span> This process can be time consuming and, depending on your server speed and the current Yahoo! response time, may take several minutes. </li>
	<li>Repeat step 4 above for each of your top-level keywords. </li>
	<li>Now you will need to repeat steps 4 and 5 above to update your Yahoo! Answers and Yahoo! Links content. </li>
	<li>Technically, that is all you need to do. However, you can also store your own unique content using the Authors, Articles and Static Pages sections available in your navigation menu. </li>
	</ol>


<h2>Kwikcontent Navigation</h2>

<h3><a href="index.php">Home</a></h3>
<p>This is the page you are reading now. The home page provides you with a brief description of each section of your administration section. It also gives you a brief guide to setting up your domain. </p>

<h3><a href="settings.php">Settings</a></h3>
<p>The settings area allows you to update the overall configuration of your domain.</p>

<h3><a href="keywords.php">Keywords</a></h3>
<p>This section allows you to configure each of your domain's keywords. It is also the area where you will tell the system to generate or update the content for your domain. </p>

<h3><a href="articles.php">Articles</a></h3>
<p>Kwikcontent allows you to store your own unique articles. This section allows you to manage those articles. </p>

<h3><a href="authors.php">Authors</a></h3>
<p>Each of your stored articles can be assigned one author. Use this section to manage the authors for your domain. </p>

<h3><a href="static_pages.php">Static Pages</a></h3>
<p>Kwikcontent allows you to add web pages into your site navigation.  These static pages can also hold RSS feeds. This section allows you to manage those pages.</p>

<h3><a href="inserts.php">Inserts</a></h3>
<p>Your site should begin to attract organic search engine traffic.  You will find that some pages are more popular landing pages than others.  You can use Inserts to automatically insert text/HTML onto any page in your web site.</p>

<h3><a href="deletes.php">Deletes</a></h3>
<p>The deletes area allows you to remove any individual pieces of Yahoo! content that you do not want on your site. </p>

<h3><a href="http://www.kwikcontent.com/help.php" target="_blank">Help</a></h3>
<p>This link will take you to the kwikcontent.com help section.</p>

<?
include("_footer.php");
?>
