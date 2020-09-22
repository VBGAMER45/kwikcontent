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
include_once("../includes/domain.php");
include_once("lib.sitesetup.php");


switch ($action) {
	case "setupsite":
		
		if (DEMO_MODE == 1)
		{
			$alert['text'] = "This option is disabled in the demo mode";
			break;
		}
		
		$domain_name = htmlspecialchars($_REQUEST['domain_name'],ENT_QUOTES);
		$domain_title = htmlspecialchars($_REQUEST['domain_title'],ENT_QUOTES);
		$database_prefix = htmlspecialchars($_REQUEST['database_prefix'],ENT_QUOTES);
		$yahoo_app_id = htmlspecialchars($_REQUEST['yahoo_app_id'],ENT_QUOTES);
		$bing_app_id = htmlspecialchars($_REQUEST['bing_app_id'], ENT_QUOTES);
		$google_pub_id = htmlspecialchars($_REQUEST['google_pub_id'], ENT_QUOTES);
		$google_analytics_code = htmlspecialchars($_REQUEST['google_analytics_code'], ENT_QUOTES);
		$domain_name = strtolower($domain_name);
		
		if (empty($domain_name))
		{
			$alert['text'] = "Domain name is required.";
			break;
		}
		
		if (empty($domain_title))
		{
			$alert['text'] = "Domain title is required.";
			break;
		}
		
		// Now lets create the site
		SetupSite($database_prefix,$domain_name,$domain_title,$yahoo_app_id,$bing_app_id,$google_pub_id,$google_analytics_code);
		
		
		$alert['text'] = "Site " . $domain_name. " has been created.";
		break;

	default: break;
}



//page definitions
define("WINDOW_TITLE", "Setup New Site");
define("PAGE_TITLE", "Setup New Site");
?>
<?
include("_header.php");
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
<form action="<? echo getScriptName(); ?>" method="post">
	<tr>
		<td colspan="2" class="tableHeading">Setup New Site</td>
		</tr>
	<tr>
		<td class="tableRowLeft1">Domain Name</td>
		<td class="tableRowRight2"><input name="domain_name" type="text" id="domain_name" value="" size="60" /></td>
	</tr>
	<tr>
		<td class="tableRowLeft1">Domain Title </td>
		<td class="tableRowRight2"><input name="domain_title" type="text" id="domain_title" value="" size="60" /></td>
	</tr>
	<tr>
		<td class="tableRowLeft1">Database prefix</td>
		<td class="tableRowRight2"><input name="database_prefix" type="text" id="database_prefix" value="" size="60" /><br />Leave blank for system generated prefix</td>
	</tr>
	<tr>
		<td class="tableRowLeft1">Yahoo Aplication ID </td>
		<td class="tableRowRight2"><input name="yahoo_app_id" type="text" id="yahoo_app_id" value="<? echo $domain->yahoo_app_id ?>" size="60" /></td>
	</tr>
	<tr>
		<td class="tableRowLeft1">Bing Aplication ID </td>
		<td class="tableRowRight2"><input name="bing_app_id" type="text" id="bing_app_id" value="<? echo $domain->bing_app_id ?>" size="60" /></td>
	</tr>
	<tr>
		<td class="tableRowLeft1">Google Publisher ID </td>
		<td class="tableRowRight2"><input name="google_pub_id" type="text" id="google_pub_id" value="<? echo $domain->google_ad_client  ?>" size="60" /></td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Google Analytics Code</td>
		<td class="tableRowRight2"><textarea name="google_analytics_code" cols="100" rows="10" id="google_analytics_code"><? echo $domain->google_ad_client  ?></textarea></td>
	</tr>

	<tr>
		<td colspan="2" class="tableSubmitCell">
			<input name="action" type="hidden" id="action" value="setupsite" />
		
			<input type="submit" name="Submit" value="Create Site" /></td>
	</tr>
</form>
</table>

<?
include("_footer.php");
?>
