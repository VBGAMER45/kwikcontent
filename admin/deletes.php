<?php
/***************************************************************************
 *
 *   File                 : admin/deletes.php
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
include_once("../includes/yahoo.php");

switch ($action) {
	case "delete_yim":
		$obj = new YahooImage($yim_id);
		$obj->loadVars();
		$obj->destroyObject();
		$alert['text'] = 'Y! Image delete was successful.';
		break;

	case "delete_ylk":
		$obj = new YahooLink($ylk_id);
		$obj->loadVars();
		$obj->destroyObject();
		$alert['text'] = 'Y! Link delete was successful.';
		break;

	case "delete_yqa":
		$obj = new YahooQA($yqa_id);
		$obj->loadVars();
		$obj->destroyObject();
		$alert['text'] = 'Y! Answer delete was successful.';
		break;

	case "remove_old_images":
		//find all images with a rank higher than 50
		$sql = "
			SELECT *
			FROM {$multi_prefix}yahoo_image
			WHERE yim_rank > '50'
		";
		$yims = getArrayFromSql('yim_id', $sql);
		if (count($yims) > 0) {
			foreach ($yims as $id => $details) {
				$yim = new YahooImage($id);
				$yim->setVars($details);
				$yim->ripData();
				if (!(@DownloadFileContents($yim->img_info['Thumbnail']['Url']))) {
					//this is a dead thumbnail
					$yim->destroyObject();
				}
			}
		}
		$alert['text'] = 'You have successfully removed all of the old Y! images from your site.';
		break;

	case "update_y_images":
		//update all of the Yahoo! images that have not been updated within the desired hour range
		$first_good = mktime() - (60 * 60 * $update_hours);

		$updated_keys = array();

		$sql = "
			SELECT *
			FROM {$multi_prefix}keyword_update as ku, keyword as k
			WHERE ku.kup_table LIKE 'yahoo_image'
			AND ku.key_id = k.key_id
			ORDER BY ku.kup_time DESC
		";
		$q = mysql_query($sql);
		while ($r = mysql_fetch_assoc($q)) {

			$key_id = $r['key_id'];
			if (!in_array($key_id, $updated_keys)) {
				//this keyword has not seen an update yet
				if ($r['kup_time'] < $first_good) {
					//we need to update this key's images
					$key = new Keyword($key_id);
					$key->setVars($r);
					$key->updateYahooImages();
				}
			}
			$updated_keys[$key_id] = $key_id;

		}
		$alert['text'] = 'All Y! image updates are complete.';
		break;

	default: break;
}

//page definitions
define("WINDOW_TITLE", "Deletes");
define("PAGE_TITLE", "Deletes");
?>
<?
include("_header.php");
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr>
		<td colspan="3" class="tableHeading">Individual Deletes </td>
	</tr>
	<tr>
		<td colspan="3" class="tableSubHeading">This page allows you to remove specific Yahoo! generated content. </td>
	</tr>
	<form action="<? echo getScriptName(); ?>" method="post">
	<tr>
		<td class="tableRowLeft1">Y! Image ID </td>
		<td class="tableRowRight2"><input name="yim_id" type="text" id="yim_id" /></td>
		<td class="tableRowRight2"><input type="submit" name="Submit" value="Delete" />
			<input name="action" type="hidden" id="action" value="delete_yim" />
			<input name="d" type="hidden" id="d" value="<? echo $domain->domain_id ?>" /></td>
	</tr>
	</form>
	<form action="<? echo getScriptName(); ?>" method="post">
	<tr>
		<td class="tableRowLeft1">Y! Link ID </td>
		<td class="tableRowRight2"><input name="ylk_id" type="text" id="ylk_id" /></td>
		<td class="tableRowRight2"><input type="submit" name="Submit" value="Delete" />
			<input name="action" type="hidden" id="action" value="delete_ylk" />
			<input name="d" type="hidden" id="d" value="<? echo $domain->domain_id ?>" /></td>
	</tr>
	</form>
	<form action="<? echo getScriptName(); ?>" method="post">
	<tr>
		<td class="tableRowLeft1">Y! Answer ID </td>
		<td class="tableRowRight2"><input name="yqa_id" type="text" id="yqa_id" /></td>
		<td class="tableRowRight2"><input type="submit" name="Submit" value="Delete" />
			<input name="action" type="hidden" id="action" value="delete_yqa" />
			<input name="d" type="hidden" id="d" value="<? echo $domain->domain_id ?>" /></td>
	</tr>
	</form>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr>
		<td colspan="2" class="tableHeading">Remove Old Images </td>
		</tr>
	<tr>
		<td colspan="2" class="tableSubHeading">Using this tool will remove all images that meet the following conditions:<br />
		1) Their &quot;rank&quot; is lower than 50 (i.e. 51+). <br />
		2) The thumbnail image is no longer active. </td>
	</tr>
	<form action="<? echo getScriptName(); ?>" method="post">
	<tr>
		<td width="200" class="tableRowLeft1">Update Current Images </td>
		<td class="tableRowRight2">It is recommended that you first update your current images before using this delete tool. This can be time consuming depending on the number of keywords you have stored in your site. If your browser times out, you will need to revisit this page and perform this action until your see the completion message.<br />
			<br />
			Update images that have not been updated within the past
				<input name="update_hours" type="text" id="update_hours" value="6" size="10" />
				hours.
				<input name="action" type="hidden" id="action" value="update_y_images" />
				<input type="submit" name="Submit" value="Begin Updates" /></td>
	</tr>
	</form>
	<tr>
		<td class="tableRowLeft1">Ready to begin?</td>
		<td class="tableRowRight2"><a href="<? echo getScriptName(); ?>?action=remove_old_images">Begin Removal</a> - Note: This can be very time consuming. If your browser times out, you will need to revisit this page and perform this action again until you see the completion message. </td>
	</tr>
</table>
<?
include("_footer.php");
?>
