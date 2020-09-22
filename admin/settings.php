<?php
/***************************************************************************
 *
 *   File                 : admin/settings.php
 *   Software             : Kwikcontent
 *   Version              : 1.2
 *   Release Date         : December 19, 2011
 *   Copyright            : (C) 2011 Samson Software
 *   Contact              : http://www.kwikcontent.com/
 *   License              : Limited to one owner.  My not be reproduced or
 *                          redistributed under any circumstance.
 *
 ***************************************************************************/

include_once("../includes/_global.php");
include_once("../includes/domain.php");

//page prompts
switch ($action) {
	case "save":
		$domain->arrayToTable($_POST, true);
		$domain->loadVars();
		$alert['text'] = 'Settings were successfully saved.';
		break;

	default: break;
}

//page definitions
define("WINDOW_TITLE", "Domain Settings");
define("PAGE_TITLE", "Domain Settings");
?>
<?
include("_header.php");
?>

<style type="text/css">
.tableRowRight2 {
	font-style:italic;
}
</style>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
<form action="<? echo getScriptName(); ?>" method="post">
	<tr>
		<td colspan="2" class="tableHeading">Domain Settings</td>
	</tr>
	<tr>
		<td width="220" valign="top" class="tableRowLeft1">Domain Name </td>
		<td width="559" class="tableRowRight2">

<?php
// get host name from URL
$url = $_SERVER['HTTP_HOST'];
preg_match("/^(http:\/\/)?([^\/]+)/i",
    "$url", $matches);
$host = $matches[2];

// get last two segments of host name
preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
$mydomain = $matches[0];

?>

<?php if ($domain->domain_name) { $mydomain = $domain->domain_name; } echo "<input name=\"domain_name\" type=\"text\" id=\"domain_name\" value=" . $mydomain . " size=\"40\" /><br />Change capitalization if appropriate";
?>
			</td>
	</tr>
	<tr>
		<td width="220" valign="top" class="tableRowLeft1">Domain Title </td>
		<td width="559" class="tableRowRight2"><input name="domain_title" type="text" id="domain_title" value="<? echo $domain->domain_title ?>" size="40" />
			<br />
			The name of your website; should be a short and powerful keyphrase</td>
	</tr>
	<tr>
		<td width="220" valign="top" class="tableRowLeft1">Precede Window Titles<br />
			With Domain Title? </td>
		<td width="559" class="tableRowRight2"><? echo getBoolMenu('domain_window_titles', $domain->domain_window_titles); ?><br />
			Choosing yes will cause all of your public window titles to begin with the domain title listed above. </td></tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Hide PHP Errors/Warnings </td>
		<td class="tableRowRight2"><? echo getBoolMenu('hide_public_errors', $domain->hide_public_errors); ?><br />
			If, for any reason, your server generates PHP errors or warnings, setting this to No will hide those.<br />
			Note: it is suggested you set this to No after you have completed the installation and setup processes. </td>
	</tr>

	<tr>
		<td valign="top" class="tableRowLeft1">Home Page &quot;Splash&quot; Text </td>
		<td class="tableRowRight2"><textarea name="domain_home_text" cols="60" rows="6" id="domain_home_text"><? echo $domain->domain_home_text ?></textarea>
			<br />
			This text will be rendered on the home page of your site.</td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Home Page META Description </td>
		<td class="tableRowRight2"><textarea name="domain_home_meta_desc" cols="60" rows="6" id="domain_home_meta_desc"><? echo $domain->domain_home_meta_desc ?></textarea>
			<br />
			Your home page is the only page of your site whose META description is not automatically created. </td>
	</tr>
	<tr>
		<td colspan="2" valign="top" class="tableHeading">Administration Settings </td>
		</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Use WYSIWYG Editors? </td>
		<td class="tableRowRight2"><? echo getBoolMenu('admin_wysiwyg', $domain->admin_wysiwyg); ?><br />Would you like to use a text editing tool in your custom content sections? (Set this to No if you would like to enter your own HTML code)</td>
	</tr>
	<tr>
		<td colspan="2" valign="top" class="tableHeading">Yahoo! Settings </td>
		</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Yahoo! Application ID </td>
		<td class="tableRowRight2"><input name="yahoo_app_id" type="text" id="yahoo_app_id" value="<? echo $domain->yahoo_app_id ?>" size="40" />
			<br />
			This is required for your domain to function properly. <a href="http://search.yahooapis.com/webservices/register_application" target="_blank">Obtain one here</a>. <em>If this link is not working right now, you should be able to temporarily use the app id 'YahooDemo.'</em> </td>
	</tr>
<tr>
		<td valign="top" class="tableRowLeft1">Yahoo! Region </td>
		<td class="tableRowRight2"><input name="region" type="text" id="region" value="<? if ($domain->region) { echo $domain->region; } else { echo 'us'; } ?>" size="2" /><br />

Filter based on country:
			<ul>

				<li>us: United States</li>
				<li>uk: United Kingdom</li>
				<li>ca: Canada</li>

				<li>au: Australia</li>
				<li>in: India</li>
				<li>es: Spain</li>

				<li>br: Brazil</li>
				<li>ar: Argentina</li>
				<li>mx: Mexico</li>

				<li>e1: en Espanol</li>
				<li>it: Italy</li>
				<li>de: Germany</li>

				<li>fr: France</li>
				<li>sg: Singapore</li>
			</ul>

			You can enter multiple regions.
</td>
	</tr>
	
	<tr>
		<td colspan="2" valign="top" class="tableHeading">Bing Settings </td>
		</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Bing Application ID </td>
		<td class="tableRowRight2"><input name="bing_app_id" type="text" id="bing_app_id" value="<? echo $domain->bing_app_id ?>" size="40" />
			<br />
			Need a Bing Application ID? <a href="http://www.bing.com/developers/createapp.aspx" target="_blank">Obtain one here</a>. <em></em> </td>
	</tr>
	
	<tr>
		<td colspan="2" valign="top" class="tableHeading">Update Settings</td>
		</tr>
	
	
	<tr>
		<td valign="top" class="tableRowLeft1">Automated Updates? <div style="font-weight:normal;font-style:italic">Applies only to: Images, Videos, Links and Y! Answers</div></td>
		<td class="tableRowRight2"><? echo getBoolMenu('yahoo_auto_update', $domain->yahoo_auto_update); ?>&nbsp;Would you like your system to automatically perform content updates?<br />
			If yes, how often would you like your updates to occur? <strong>Update every
				<input name="yahoo_auto_update_frequency" type="text" id="yahoo_auto_update_frequency" value="<? echo $domain->yahoo_auto_update_frequency ?>" size="4" />
				days.</strong>
				<div style="font-style: normal">
				<div><a href="javascript:void(0);" onClick="expandColapse('howdoupdateswork');">How does this work?</a></div>
				<div id="howdoupdateswork" class="hide" style="width: 400px">If you have this set to 'Yes' your system will attempt to update its own content each time a visitor (or search bot) visits the appropriate content sections of your public website.  However, if an update has already been performed within the last number of days you specify above, no updates will be performed at that time.</div>
				</div></td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">News Update Frequency </td>
		<td class="tableRowRight2"><strong>Update every <input name="yahoo_news_update_frequency" type="text" id="yahoo_news_update_frequency" value="<? echo $domain->yahoo_news_update_frequency ?>" size="5" />
			hours.</strong>
			<div style="font-style: normal">
			<div><a href="javascript:void(0);" onClick="expandColapse('howdonewsupdateswork');">How does this work?</a></div>
			<div id="howdonewsupdateswork" class="hide" style="width: 400px">Your system will attempt to update its own news content each time a visitor (or search bot) visits the appropriate keyword landing page on your public website.  However, if an update has already been performed within the last number of hours you specify above, no updates will be performed at that time.</div>
			</div> </td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Total Keyword News </td>
		<td class="tableRowRight2"><input name="preview_y_news" type="text" id="preview_y_news" value="<? echo $domain->preview_y_news ?>" size="10" />
			<br />
			The total Yahoo! news entries that will appear on your keyword landing pages. </td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Total News Stories </td>
		<td class="tableRowRight2"><input name="yahoo_news_display_total" type="text" id="yahoo_news_display_total" value="<? echo $domain->yahoo_news_display_total ?>" size="10" />
			<br />
			The maximum Yahoo! news stories that will appear on your keyword news pages. </td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Total Home/Keyword Images </td>
		<td class="tableRowRight2"><input name="preview_y_images" type="text" id="preview_y_images" value="<? echo $domain->preview_y_images ?>" size="10" />
			<br />
			The total Yahoo! images (for each top-level keyword) that will appear on your home page. </td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Total Home/Keyword Y! Answers </td>
		<td class="tableRowRight2"><input name="preview_y_qas" type="text" id="preview_y_qas" value="<? echo $domain->preview_y_qas ?>" size="10" />
			<br />
			The total Yahoo! Answers (for each top-level keyword) that will appear on your home page.</td>
	</tr>
	<tr>
		<td colspan="2" valign="top" class="tableHeading">Other Keyword Settings </td>
		</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Total Home/Keyword Articles </td>
		<td class="tableRowRight2"><input name="preview_articles" type="text" id="preview_articles" value="<? echo $domain->preview_articles ?>" size="10" />
			<br />
			The total articles (for each top-level keyword) that will appear on your home page.</td>
	</tr>

<tr>


	<tr>
		<td colspan="2" valign="top" class="tableHeading">Google Settings </td>
		</tr>
	<tr>
		<td width="220" valign="top" class="tableRowLeft1">Google Site Search? </td>
		<td width="559" class="tableRowRight2"><? echo getBoolMenu('google_search', $domain->google_search); ?><br />
			Choosing yes will create a link that allows users to search your site using Google. </td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Google Analytics code </td>
		<td class="tableRowRight2"><textarea name="google_analytics" cols="60" rows="6" id="google_analytics"><? echo $domain->google_ad_client  ?></textarea></td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Google AdSense Publisher ID </td>
		<td class="tableRowRight2"><input name="google_ad_client" type="text" id="google_ad_client" value="<? echo $domain->google_ad_client ?>" size="25" /></td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Google AdSense Channel ID </td>
		<td class="tableRowRight2"><input name="google_ad_channel" type="text" id="google_ad_channel" value="<? echo $domain->google_ad_channel ?>" size="25" /></td>
	</tr>

	<tr>
		<td colspan="2" valign="top" class="tableHeading">Amazon Settings </td>
		</tr>

	<tr>
		<td valign="top" class="tableRowLeft1">Amazon Affiliate ID </td>
		<td class="tableRowRight2"><input name="amazon_id" type="text" id="amazon_id" value="<? echo $domain->amazon_id ?>" size="25" /></td>
	</tr>

	<tr>
		<td valign="top" class="tableRowLeft1">Amazon Global Keyphrase</td>
		<td class="tableRowRight2"><input name="amazon_keyword" type="text" id="amazon_keyword" value="<? if ($domain->amazon_keyword) { echo $domain->amazon_keyword; } else { echo $domain->domain_title; } ?>" size="25" /><br />Required to display targeted Amazon ads (this keyphrase is used for <strong>all</strong> Amazon ads throughout the site)</td>
	</tr>

	<tr>
		<td colspan="2" valign="top" class="tableHeading">eBay Settings </td>
		</tr>

	<tr>
		<td valign="top" class="tableRowLeft1">eBay CampaignID </td>
		<td class="tableRowRight2"><input name="ebay_id" type="text" id="ay_id" value="<? echo $domain->ebay_id ?>" size="25" /></td>
	</tr>

	<tr>
		<td valign="top" class="tableRowLeft1">eBay Front Page Keyphrase</td>
		<td class="tableRowRight2"><input name="ebay_keyword" type="text" id="ebay_keyword" value="<? if ($domain->ebay_keyword) { echo $domain->ebay_keyword; } else { echo $domain->domain_title; } ?>" size="25" /><br />Required to display targeted eBay ads on the <strong>front page</strong> (eBay ads on sub pages are automatically targeted according to the primary keyphrase of that page)</td>
	</tr>

	<tr>
		<td colspan="2" valign="top" class="tableHeading">JavaScript Code & Customized Footer</td>
		</tr>


	<tr>
		<td valign="top" class="tableRowLeft1">Will be included right before the &lt;/BODY&gt; tag </td>
		<td class="tableRowRight2"><textarea name="google_analytics" cols="60" rows="6" id="google_analytics"><? echo $domain->google_analytics ?></textarea>
<br />You can paste external code here, including Google Analytics, other statistics software, newsletter subscription code etc.), and anything else you'd like to include before the &lt;/BODY&gt; tag, such as a customized footer or sitewide links.</td>
	</tr>



	<tr>
		<td colspan="2" valign="top" class="tableHeading">Forum</td>
		</tr>


	<tr>
		<td valign="top" class="tableRowLeft1">Forum URL</td>
		<td class="tableRowRight2"><input name="forum_url" type="text" id="forum_url" value="<? if ($domain->forum_url) { echo $domain->forum_url; } ?>" size="25" /><br />If you have a forum for this site and would like to link to it in the left side bar, enter the complete URL here.</td>
	</tr>

	<tr>
		<td valign="top" class="tableRowLeft1">Open Forum in new window?</td>
		<td width="559" class="tableRowRight2"><? echo getBoolMenu('forum_blank', $domain->forum_blank); ?>
</td>
	</tr>

	<tr>
			<td colspan="2" valign="top" class="tableHeading">Other Settings</td>
		</tr>

		<tr>
			<td valign="top" class="tableRowLeft1">linkadge Key </td>
			<td class="tableRowRight2"><input name="linkadge_key" type="text" id="linkadge_key" value="<? echo $domain->linkadge_key ?>" size="25" /></td>
	</tr>


	<tr>
		<td colspan="2" class="tableSubmitCell"><input name="action" type="hidden" id="action" value="save" />
			<input type="submit" name="Submit" value="Save Settings" style="font-size:22px" /></td>
	</tr>



</form>
</table>

<?
include("_footer.php");
?>