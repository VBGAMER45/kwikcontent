<?php
/***************************************************************************
 *
 *   File                 : admin/manage_sites.php
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


switch ($action) {
	case "disable":
		
		$id = (int) $_REQUEST['id'];
		$result = mysql_query("
		SELECT 
			id, domain, date, hits, removed FROM sites Where id = " . $id);
		$siteRow = mysql_fetch_assoc($result);
		
		mysql_query("UPDATE sites SET removed = 1 WHERE id = " . $id);
	 
		$alert['text'] = "You have disabled " . $siteRow['domain'];
		
		
		break;
		
	case "enable":
		
		$id = (int) $_REQUEST['id'];
		$result = mysql_query("
		SELECT 
			id, domain, date, hits, removed FROM sites Where id = " . $id);
		$siteRow = mysql_fetch_assoc($result);
		
		mysql_query("UPDATE sites SET removed = 0 WHERE id = " . $id);
	 
		$alert['text'] = "You have enabled " . $siteRow['domain'];
		
		
		break;
		
	case 'ping':
		$id = (int) $_REQUEST['id'];
		$result = mysql_query("
		SELECT 
			id, domain, date, hits, removed FROM sites Where id = " . $id);
		$siteRow = mysql_fetch_assoc($result);
		
		$sitemapUrl = 'http://www.' . $siteRow['domain'] . '/sitemaps/sitemap.php';
			
		// Ping All
		
		// Ping Google
		//	http://www.google.com/webmasters/sitemaps/ping?sitemap='
		$googleResult = @DownloadFileContents('http://www.google.com/webmasters/sitemaps/ping?sitemap=' . $sitemapUrl);
		$alert['text'] = "Google Pinged<br />";
		// Ping Yahoo
		
		// http://search.yahooapis.com/SiteExplorerService/V1/ping?sitemap=
		$yahooResult = @DownloadFileContents('http://search.yahooapis.com/SiteExplorerService/V1/ping?sitemap=' . $sitemapUrl);
		$alert['text'] .= "Yahoo Pinged<br />";
		// Ping MSN/Live/Bing
		// 
		//http://webmaster.live.com/ping.aspx?siteMap=
		$bingResult = @DownloadFileContents('http://webmaster.live.com/ping.aspx?siteMap=' . $sitemapUrl);
		$alert['text'] .= "Bing Pinged<br />";
		
		// Ping Ask
		/// http://submissions.ask.com/ping?sitemap=
		$askResult = @DownloadFileContents('http://submissions.ask.com/ping?sitemap=' . $sitemapUrl);
		$alert['text'] .= "Ask.com Pinged<br />";
		
		break;

	default: break;
}



//page definitions
define("WINDOW_TITLE", "Manage Sites");
define("PAGE_TITLE", "Manage Sites");
?>
<?
include("_header.php");
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr>
		<td colspan="3" class="tableHeading">Sites </td>
		</tr>
	<tr>
		<td colspan="3" class="tableSubHeading" style="text-align: right"><img src="../site_icons/16/add2.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="setup_new_site.php">Seup New Site</a> </td>
	</tr>
	<tr>
		<td align="center" class="tableColumnHeader">Domain</td>
		<td align="center" class="tableColumnHeader">Site Maps</td>
		<td align="center" class="tableColumnHeader">Options</td>
	</tr>
	<?php
	
	$result = mysql_query("SELECT id, domain, date, hits, removed FROM sites");

	while ($row = mysql_fetch_assoc($result))
	{
	echo '
	<tr>
		<td class="tableRowRightd" align="center"><a href="http://' . $row['domain'] . '">' . $row['domain'] . '</a>&nbsp;</td>
		
		<td align="center" class="tableRowRight"><img src="../site_icons/16/gears.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="manage_sites.php?id=' . $row['id'] . '&action=ping">Ping All</a></td>
		
		<td align="center" class="tableRowRight">';
	
		if ($row['removed'] == 0)
			echo '<img src="../site_icons/16/delete2.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="manage_sites.php?id=' . $row['id'] . '&action=disable" onClick="return confirmIt(\':: WARNING ::\n\nYou are about to disable this site!\');">Disable Site</a>';
		else 
			echo '<img src="../site_icons/16/edit.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="manage_sites.php?id=' . $row['id'] . '&action=enable" onClick="return confirmIt(\':: WARNING ::\n\nYou are about to enable this site!\');">Enable Site</a>';
	
		echo '
	</td>
	</tr>';
	}
	
?>

</table>

<?
include("_footer.php");
?>
