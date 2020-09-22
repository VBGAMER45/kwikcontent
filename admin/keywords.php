<?php
/***************************************************************************
 *
 *   File                 : admin/keywords.php
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
include_once("../includes/yahoo.php");

//load any passed key
if ($k > 0) {
	$key = new Keyword($k);
	$key->loadVars();
}

//page prompts
switch ($action) {
	case "new_subkey":
		$subkey = new Keyword();
		if (
			$subkey->createNew(array(
				"top_key_id" => $key->key_id,
				"key_text" => $key_text
			))) {
				$subkey->loadVars();
				$subkey->assignRank(0);
				$subkey->assignRank($rank);
				$alert['text'] = 'Subkeyword properly saved.';
		} else {
			$alert['text'] = 'Kwikcontent only allows '.MAX_KEYWORDS.' top-level keywords and '.MAX_KEYWORDS.' sub-keywords for each top-level keyword.';
		}
		break;

	case "rank":
		$key->assignRank($rank);
		break;

	case "top_key":
		$key->assignRank(0);
		$key->setColumnValue("top_key_id", "0");
		$key->setStoredVars();
		$key->assignRank(0);
		$alert['text'] = 'That keyword has been moved to a top-level position.';
		break;

	case "delete":
		$key->destroyObject();
		$alert['text'] = 'That keyword has been removed from the system.';
		break;

	case "new":
		if (trim($keyword) != "") {
			//ensure it doesn't already exist
			$sql = "
				SELECT COUNT(key_id)
				FROM {$multi_prefix}keyword
				WHERE key_text LIKE '$keyword'
				AND top_key_id = '0'
			";
			if (!(mysql_result(mysql_query($sql), 0))) {
				$key = new Keyword();
				if (
					$key->createNew(array(
						"key_text" => $keyword,
						"top_key_id" => "0"
					))) {
						$key->loadVars();
						$key->assignRank($rank);
						$alert['text'] = 'Keyword properly saved.';
				} else {
					$alert['text'] = 'Kwikcontent only allows '.MAX_KEYWORDS.' top-level keywords and '.MAX_KEYWORDS.' sub-keywords for each top-level keyword.';
				}
			} else {
				$alert['text'] = 'The phrase you entered already exists.';
			}
		} else {
			$alert['text'] = 'Keyword not saved.  No phrase was entered.';
		}
		break;

	case "edit":
		if (trim($key_text) != "") {
			//ensure it doesn't already exist
			$sql = "
				SELECT COUNT(key_id)
				FROM {$multi_prefix}keyword
				WHERE key_text LIKE '$keyword'
				AND top_key_id = '0'
				AND key_id != '$key->key_id'
			";
			if (!(mysql_result(mysql_query($sql), 0))) {
				$key->arrayToTable($_POST);
				$key->loadVars();
				$key->assignRank($rank);

				$alert['text'] = 'Keyword properly saved.';
			} else {
				$alert['text'] = 'The phrase you entered already exists.';
			}
		} else {
			$alert['text'] = 'Keyword not saved.  No phrase was entered.';
		}
		break;

	case "edit_subkey":
		if (trim($key_text) != "") {
			//ensure it doesn't already exist
			$sql = "
				SELECT COUNT(key_id)
				FROM {$multi_prefix}keyword
				WHERE key_text LIKE '$keyword'
				AND top_key_id = '0'
				AND key_id != '$key->key_id'
			";
			if (!(mysql_result(mysql_query($sql), 0))) {
				$key->arrayToTable($_POST);

				$alert['text'] = 'Keyword properly saved.';
			} else {
				$alert['text'] = 'The phrase you entered already exists.';
			}
		} else {
			$alert['text'] = 'Keyword not saved.  No phrase was entered.';
		}
		break;

	case "mass_update_yahoo_images":
		//find all the top keys
		$top_keys = getKeywords(0);

		if (count($top_keys) > 0) {
			foreach ($top_keys as $id => $details) {
				$top_key = new Keyword($id);
				$top_key->setVars($details);
				$top_key->updateYahooImages();

				//find all of this key's sub keys
				$sub_keys = getKeywords($top_key->key_id);

				if (count($sub_keys) > 0) {
					foreach ($sub_keys as $sub_id => $sub_details) {
						$sub_key = new Keyword($sub_id);
						$sub_key->setVars($sub_details);
						$sub_key->updateYahooImages();
					}
				}
			}
		}

		$alert['text'] = 'All images have been successfully updated.';

		break;

	case "mass_update_yahoo_links":
		//find all the top keys
		$top_keys = getKeywords(0);

		if (count($top_keys) > 0) {
			foreach ($top_keys as $id => $details) {
				$top_key = new Keyword($id);
				$top_key->setVars($details);
				$top_key->updateYahooLinks();

				//find all of this key's sub keys
				$sub_keys = getKeywords($top_key->key_id);

				if (count($sub_keys) > 0) {
					foreach ($sub_keys as $sub_id => $sub_details) {
						$sub_key = new Keyword($sub_id);
						$sub_key->setVars($sub_details);
						$sub_key->updateYahooLinks();
					}
				}
			}
		}

		$alert['text'] = 'All links have been successfully updated.';

		break;

	case "mass_update_yahoo_answers":
		//find all the top keys
		$top_keys = getKeywords(0);

		if (count($top_keys) > 0) {
			foreach ($top_keys as $id => $details) {
				$top_key = new Keyword($id);
				$top_key->setVars($details);
				$top_key->updateYahooQAs();

				//find all of this key's sub keys
				$sub_keys = getKeywords($top_key->key_id);

				if (count($sub_keys) > 0) {
					foreach ($sub_keys as $sub_id => $sub_details) {
						$sub_key = new Keyword($sub_id);
						$sub_key->setVars($sub_details);
						$sub_key->updateYahooQAs();
					}
				}
			}
		}

		$alert['text'] = 'All Yahoo! answers have been successfully updated.';

		break;

	case "mass_update_existing_yahoo_answers":
		//find all the top keys
		$top_keys = getKeywords(0);

		if (count($top_keys) > 0) {
			foreach ($top_keys as $id => $details) {
				$top_key = new Keyword($id);
				$top_key->setVars($details);
				$top_key->updateExistingYahooQAs();

				//find all of this key's sub keys
				$sub_keys = getKeywords($top_key->key_id);

				if (count($sub_keys) > 0) {
					foreach ($sub_keys as $sub_id => $sub_details) {
						$sub_key = new Keyword($sub_id);
						$sub_key->setVars($sub_details);
						$sub_key->updateExistingYahooQAs();
					}
				}
			}
		}

		$alert['text'] = 'All existing Yahoo! answers have been successfully updated.';

		break;

	case "select_update_yahoo_images":
		if (count($keys) > 0) {
			foreach ($keys as $id) {
				$key = new Keyword($id);
				$key->loadVars();
				$key->updateYahooImages();
			}
		}

		$alert['text'] = 'Those images have been successfully updated.';

		break;

	case "select_update_yahoo_links":
		if (count($keys) > 0) {
			foreach ($keys as $id) {
				$key = new Keyword($id);
				$key->loadVars();
				$key->updateYahooLinks();
			}
		}

		$alert['text'] = 'Those links have been successfully updated.';

		break;

	case "select_update_yahoo_answers":
		if (count($keys) > 0) {
			foreach ($keys as $id) {
				$key = new Keyword($id);
				$key->loadVars();
				$key->updateYahooQAs();
			}
		}

		$alert['text'] = 'Those Yahoo! answers have been successfully updated.';

		break;

	case "select_update_youtube":
		if (count($keys) > 0) {
			foreach ($keys as $id) {
				$key = new Keyword($id);
				$key->loadVars();
				$key->updateYouTube();
			}
		}

		$alert['text'] = 'Those YouTube Videos have been successfully updated.';

		break;


	case "update_yahoo_images":
		if ($key->key_id > 0) {
			$key->updateYahooImages();
			$alert['text'] = 'Yahoo! images have been updated for that keyword.';
		} else {
			$alert['text'] = 'Error :: No keyword passed.';
		}
		break;

	case "update_yahoo_links":
		if ($key->key_id > 0) {
			$key->updateYahooLinks();
			$alert['text'] = 'Yahoo! links have been updated for that keyword.';
		} else {
			$alert['text'] = 'Error :: No keyword passed.';
		}
		break;

	case "update_yahoo_answers":
		if ($key->key_id > 0) {
			$key->updateYahooQAs();
			$alert['text'] = 'Yahoo! answers have been updated for that keyword.';
		} else {
			$alert['text'] = 'Error :: No keyword passed.';
		}
		break;

	case "update_youtube":
		if ($key->key_id > 0) {
			$key->updateYouTube();
			$alert['text'] = 'YouTube Vidoes have been updated for that keyword.';
		} else {
			$alert['text'] = 'Error :: No keyword passed.';
		}
		break;

	case "manual_answers_update":
		if ($key->key_id > 0) {
			$key->updateYahooQAs($yahoo_data);
			$alert['text'] = 'Yahoo! answers have been updated for that keyword.';
		} else {
			$alert['text'] = 'Error :: No keyword passed.';
		}
		break;

	case "update_subkeys":
		if ($key->key_id > 0) {

			//find yahoo's suggested keywords
			$rest_url = 'http://search.yahooapis.com/WebSearchService/V1/relatedSuggestion?appid='.$domain->yahoo_app_id.'&query='.urlencode($key->key_text).'&output=php';
			$array = unserialize(DownloadFileContents($rest_url));

			$suggestions = $array['ResultSet']['Result'];
			if (is_array($suggestions) > 0) {
				//first remove any old key suggestions
				// we will no longer destroy anything - $key->destroySubKeys();

				//begin a counter for rank placement
				$rank_counter = 0;

				//find all of the existing/stored sub keys here for rank adjustment
				$subkeys = getKeywords($key->key_id);
				$subkey_ranks = condenseArray($subkeys, "key_text", "key_id");

				//now create new suggested keys
				foreach ($suggestions as $key_text) {


					//only allow keywords that are not alreay associated with a different top_key on this domain
					$sql = "
						SELECT COUNT(key_id)
						FROM {$multi_prefix}keyword
						WHERE key_id NOT LIKE ''
						AND (
							top_key_id = '0'
							OR top_key_id != '$key->key_id'
						)
						AND key_text LIKE '$key_text'
					";
					if (mysql_result(mysql_query($sql), 0) > 0) {
						continue;
					}

					//if we get here, we can consider this is a good keyword for this top-level
					$rank_counter++;

					//if this keyword already exists, simply update its rank, else store it with the proper rank
					//this will force older keywords down as they loose they're relavence
					if (@array_key_exists($key_text, $subkey_ranks)) {
						//just update the rank
						$this_key_id = $subkey_ranks[$key_text];
						$subkey = new Keyword($this_key_id);
						$subkey->setVars($subkeys[$this_key_id]);
						$subkey->assignRank($rank_counter);
					} else {
						$subkey = new Keyword();
						$subkey->createNew(array(
							"key_text" => $key_text,
							"top_key_id" => $key->key_id
						));
						$subkey->loadVars();
						$subkey->assignRank($rank_counter);
					}
				}
			}

			$alert['text'] = 'Subkeywords have been successfully updated.';
		} else {
			$alert['text'] = 'No top-level keyword passed.';
		}
		break;

	default: break;
}

//find all top-level keywords for this domain
$top_keys = getKeywords(0);

//find the array of all the most recent updates for this domain
//ordering these by time forces the last record found to be associated with the array
$sql = "
	SELECT {$multi_prefix}keyword_update.*
	FROM {$multi_prefix}keyword_update, {$multi_prefix}keyword
	WHERE {$multi_prefix}keyword_update.key_id = {$multi_prefix}keyword.key_id
	ORDER BY kup_time
";
$q = mysql_query($sql);
while ($r = mysql_fetch_object($q)) {
	$updates[$r->key_id][$r->kup_table] = date("m/d/Y @ H:i", $r->kup_time);
}

//page definitions
define("WINDOW_TITLE", "Keyword Manager");
define("PAGE_TITLE", "Keyword Manager");
?>
<?
include("_header.php");
?>

<script type="text/javascript">
function setUpdateAction(update_form) {
	update_form.action.value = update_form.to_update.value;
	//alert(update_form.action.value);
}
function selectKeyGroup(form) {
	if (form.key_group.checked == 1) {
		for (i=0; i< form.elements.length; i++){
			if (form.elements[i].name.substr(0, 5) == "keys[") {
				form.elements[i].checked = true;
			}
		}
	} else {
		for (i=0; i< form.elements.length; i++){
			if (form.elements[i].name.substr(0, 5) == "keys[") {
				form.elements[i].checked = false;
			}
		}
	}
}
</script>

<style type="text/css">
.updateTime {
	font-size: 9px;
}
</style>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script type="text/javascript" language="JavaScript" src="../javascripts/overlib.js"></script>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
<form action="<? echo getScriptName(); ?>" method="post">
	<tr>
		<td colspan="2" class="tableHeading">Create New Top-level Keyword</td>
		</tr>
	<tr>
		<td colspan="2" class="tableSubHeading"> The system will find the latest trends in related search terms for each keyword or phrase stored here. </td>
		</tr>
	<tr>
		<td class="tableRowLeft1">Keyword</td>
		<td class="tableRowRight2"><input name="keyword" type="text" id="keyword" size="40" /></td>
	</tr>
	<tr>
		<td class="tableRowLeft1">Rank</td>
		<td class="tableRowRight2"><input name="rank" type="text" id="rank" size="10" /></td>
	</tr>
	<tr>
		<td colspan="2" class="tableSubmitCell"><input name="d" type="hidden" id="d" value="<? echo $domain->domain_id ?>" />
		<input name="action" type="hidden" id="action" value="new" />
			<input type="submit" name="Submit" value="Submit" /></td>
		</tr>
</form>
</table>

<?
if (count($top_keys) > 0) {
	foreach ($top_keys as $id => $details) {
		$key_list[$id][$id] = $details['key_text'];
		$key = new Keyword($id);
		$key->setVars($details);

		//find the sub keywords for this top level keyword
		$sub_keys = getKeywords($key->key_id);
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr>
		<td colspan="2" class="tableHeading">Edit: <? echo $key->key_text ?></td>
	</tr>
	<tr>
		<td colspan="2" class="tableSubHeading">
		<img src="../site_icons/16/gears.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?k=<? echo $key->key_id ?>&action=update_subkeys">Update Sub-level Keywords</a></td>
	</tr>

	<form action="<? echo getScriptName(); ?>" method="post">
	<tr>
		<td class="tableRowLeft1">Edit</td>
		<td class="tableRowRight2">Keyword
			<input name="key_text" type="text" id="key_text" value="<? echo $key->key_text ?>" size="40" />
			Rank
			<input name="rank" type="text" id="rank" value="<? echo $key->key_rank ?>" size="10" />
			<input name="k" type="hidden" id="k" value="<? echo $key->key_id ?>" />
			<input name="action" type="hidden" id="action" value="edit" />
			<input type="submit" name="Submit" value="Submit" /></td>
	</tr>
	</form>

	<form action="<? echo getScriptName(); ?>" method="post">
	<tr>
		<td class="tableRowLeft1">Add Subkey </td>
		<td class="tableRowRight2">Keyword
			<input name="key_text" type="text" id="key_text" size="40" />
			Rank
			<input name="rank" type="text" id="rank" size="10" />
			<input name="k" type="hidden" id="k" value="<? echo $key->key_id ?>" />
			<input name="action" type="hidden" id="action" value="new_subkey" />
			<input type="submit" name="Submit" value="Submit" /></td>
	</tr>
	</form>
	<tr>
		<td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			<form name="form_<? echo $key->key_id ?>" action="<? echo getScriptName(); ?>" method="post">
			<tr>
				<td align="center" class="tableColumnHeader"><input name="key_group" type="checkbox" id="key_group" value="1" onClick="selectKeyGroup(this.form);" /></td>
				<td class="tableColumnHeader">Keyword</td>
				<td align="center" class="tableColumnHeader">Rank</td>
				<td colspan="4" align="center" class="tableColumnHeader">Updates</td>
				<td align="center" class="tableColumnHeader">Actions</td>
				</tr>
			<tr>
				<td align="center" class="tableRowRight1"><input name="keys[]" type="checkbox" id="keys[]" value="<? echo $key->key_id ?>" /></td>
				<td class="tableRowRight1"><a href="<? echo $key->getKeywordLocation(); ?>" target="_blank"><b><? echo $key->key_text ?></b></a></td>
				<td align="center" class="tableRowRight1"><? echo $key->key_rank ?></td>
				<td align="center" class="tableRowRight1">
				<img src="../site_icons/16/photo_portrait.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?k=<? echo $key->key_id ?>&action=update_yahoo_images">Bing Images</a>
					<div class="updateTime">(<? echo $updates[$key->key_id]['yahoo_image']; ?>)</div></td>
				<td align="center" class="tableRowRight1"><img src="../site_icons/16/bookmark.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?k=<? echo $key->key_id ?>&action=update_yahoo_links">Bing Links</a>
					<div class="updateTime">(<? echo $updates[$key->key_id]['yahoo_link']; ?>)</div></td>
				<td align="center" class="tableRowRight1"><img src="../site_icons/16/question_and_answer.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?k=<? echo $key->key_id ?>&action=update_yahoo_answers">Y! Answers</a>
					<div class="updateTime">(<? echo $updates[$key->key_id]['yahoo_qa']; ?>)</div></td>
				<td align="center" class="tableRowRight1"><img src="../site_icons/16/question_and_answer.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?k=<? echo $key->key_id ?>&action=update_youtube">Youtube</a>
					<div class="updateTime">(<? echo $updates[$key->key_id]['youtube_video']; ?>)</div></td>

				<td align="center" class="tableRowRight1"><img src="../site_icons/16/delete2.gif" width="16" height="16" hspace="2" border="0" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?action=delete&k=<? echo $key->key_id ?>" onclick="return confirmIt(':: WARNING ::\n\nYou are about to permanenlty delete this keyword an all of the content associated with it!');">Delete</a></td>
			</tr>
			<?
			if (count($sub_keys) > 0) {
				foreach ($sub_keys as $this_id => $these_details) {
					$key_list[$id][$this_id] = $these_details['key_text'];
					$subkey = new Keyword($this_id);
					$subkey->setVars($these_details);
					$rank_up = $subkey->key_rank - 1;
					$rank_down = $subkey->key_rank + 1;
			?>
			<tr>
				<td align="center" class="tableRowRight2"><input name="keys[]" type="checkbox" id="keys[]" value="<? echo $subkey->key_id ?>" /></td>
				<td class="tableRowRight2"><a href="<? echo $subkey->getKeywordLocation(); ?>" target="_blank"><? echo $subkey->key_text ?></a></td>
				<td align="center" class="tableRowRight2"><? echo $subkey->key_rank ?><br />
				<a href="<? echo getScriptName(); ?>?k=<? echo $subkey->key_id ?>&action=rank&rank=<? echo $rank_up ?>"><img src="../site_icons/16/arrow_up_blue.gif" width="16" height="16" hspace="2" border="0" align="absmiddle" /></a>
				<a href="<? echo getScriptName(); ?>?k=<? echo $subkey->key_id ?>&action=rank&rank=<? echo $rank_down ?>"><img src="../site_icons/16/arrow_down_blue.gif" width="16" height="16" hspace="2" border="0" align="absmiddle" /></a>				</td>
				<td align="center" class="tableRowRight2">
				<img src="../site_icons/16/photo_portrait.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?k=<? echo $subkey->key_id ?>&action=update_yahoo_images">
					Bing Images</a>
					<div class="updateTime">(<? echo $updates[$subkey->key_id]['yahoo_image']; ?>)</div></td>
				<td align="center" class="tableRowRight2"><img src="../site_icons/16/bookmark.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?d=<? echo $domain->domain_id ?>&k=<? echo $subkey->key_id ?>&action=update_yahoo_links">Bing Links</a>
					<div class="updateTime">(<? echo $updates[$subkey->key_id]['yahoo_link']; ?>)</div></td>
				<td align="center" class="tableRowRight2"><img src="../site_icons/16/question_and_answer.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?d=<? echo $domain->domain_id ?>&k=<? echo $subkey->key_id ?>&action=update_yahoo_answers">Y! Answers</a>
					<div class="updateTime">(<? echo $updates[$subkey->key_id]['yahoo_qa']; ?>)</div></td>
				<td align="center" class="tableRowRight2"><img src="../site_icons/16/question_and_answer.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?k=<? echo $key->key_id ?>&action=update_youtube">Youtube</a>
					<div class="updateTime">(<? echo $updates[$key->key_id]['youtube_video']; ?>)</div></td>

				<td align="center" class="tableRowRight2"><img src="../site_icons/16/edit.gif" width="16" height="16" hspace="2" border="0" align="absmiddle" />&nbsp;<a href="javascript:void(0);" onClick="return overlib('<form action=<? echo getScriptName(); ?> method=post style=\'margin: 0px\'><input name=\'key_text\' type=text value=\'<? echo $subkey->key_text ?>\' size=30 ><input name=b value=Save type=submit ><input name=action type=hidden value=edit_subkey ><input name=k type=hidden value=<? echo $subkey->key_id ?> ></form>',STICKY,WIDTH,220,FGCOLOR,'#eeeeee',BGCOLOR,'#000099',CAPTION,'New Keyword Text',CENTER,ABOVE,CLOSETEXT,'[x]',CLOSECOLOR,'#ffffff');">Edit</a>
					<img src="../site_icons/16/delete2.gif" width="16" height="16" hspace="2" border="0" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?action=delete&k=<? echo $subkey->key_id ?>" onclick="return confirmIt(':: WARNING ::\n\nYou are about to permanenlty delete this keyword an all of the content associated with it!');">Delete</a>
					<img src="../site_icons/16/folder_out.gif" width="16" height="16" hspace="2" border="0" align="absmiddle" />&nbsp;<a href="<? echo getScriptName(); ?>?d=<? echo $domain->domain_id ?>&action=top_key&k=<? echo $subkey->key_id ?>">Top Key</a>				</td>
			</tr>
			<? } ?>

			<? } else { ?>
			<tr>
				<td colspan="8" class="tableRowNoResults">No subkeywords found. </td>
			</tr>
			<? } ?>
			<tr>
				<td align="center" class="tableSubmitCell" style="text-align: center"><img src="../site_icons/16/import2.gif" width="16" height="16" /></td>
				<td colspan="7" class="tableSubmitCell" style="text-align: left">With selected, update
					<select name="to_update" id="to_update" onchange="setUpdateAction(this.form);">
						<option value="0" selected>- Choose -</option>
						<option value="select_update_yahoo_images">Yahoo! Images</option>
						<option value="select_update_yahoo_links">Yahoo! Links</option>
						<option value="select_update_yahoo_answers">Yahoo! Answers</option>
						<option value="select_update_youtube">Youtube Videos</option>
					</select>
					<input type="submit" name="Submit2" value="Submit" />
					<input name="action" type="hidden" id="action" value="0" /></td>
			</tr>
			</form>
		</table></td>
	</tr>
</table>

<? } ?>

<!--
<div style="text-align:right; margin-top: 2px; margin-right: 2px"><a href="<? echo getScriptName(); ?>?action=mass_update_existing_yahoo_answers">Update all existing Y! answers</a></div>
-->

<table align="center" width="780" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>

		<h3><? echo $domain->domain_title ?> Keywords</h3>

		<ul>
		<?
		if (count($key_list) > 0) {
			foreach ($key_list as $top_key_id => $sub_keys) {
				echo '<li><b>'.$sub_keys[$top_key_id].'</b></li>';
				if (count($sub_keys) > 0) {
					echo '<ul>';
					foreach ($sub_keys as $key_id => $key_text) {
						echo '<li>'.$key_text.'</li>';
					}
					echo '</ul>';
				}
			}
		}
		?>
		</ul>

		</td>
	</tr>
</table>

<? } ?>

<?
include("_footer.php");
?>
