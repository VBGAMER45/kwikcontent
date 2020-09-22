<?php
/***************************************************************************
 *
 *   File                 : admin/static_page_edit.php
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
include_once("../includes/static_page.php");

//new or edit?
if ($p > 0) {
	$sp = new StaticPage($p);
	$sp->loadVars();
	if (!($sp->sp_id > 0)) {
		header("Location: static_pages.php");
		die();
	}

	$form_title = 'Edit Static Page';
	$form_action = 'edit';

} else {
	$form_title = 'Save New Static Page';
	$form_action = 'new';
}

//page prompts
switch ($action) {
	case "new":
		if (trim($sp_nav_title) != '') {

			$sp = new StaticPage();
			$sp->createNew(array("parent_sp_id" => $parent_id));
			$sp->arrayToTable($_POST, true);
			$sp->setColumnValue('sp_slug', getGoodFilename($sp_slug));
			$sp->loadVars();
			if (!($rank > 0)) $rank = 0;
			$sp->assignRank($rank);
			$sp->loadVars();

			$form_title = 'Edit Static Page';
			$form_action = 'edit';

			$alert['text'] = 'This Static Page has been properly saved.';

		} else {
			$alert['text'] = 'Static Page not saved. Navigation Title is a required field.';
		}
		break;

	case "edit":
		if (trim($sp_nav_title) != '') {

			//save the general data
			$sp->arrayToTable($_POST, true);
			$sp->setColumnValue('sp_slug', getGoodFilename($sp_slug));

			//handle new parent if changed
			if ($parent_id != $sp->parent_sp_id) {

				if ($error = $sp->assignParent($parent_id)) {
					$alert['text'] = $error;
				} else {
					$alert['text'] = 'This Static Page has been properly saved.';
				}

			} else {
				$alert['text'] = 'This Static Page has been properly saved.';
			}

			//reload for proper edit view
			$sp->loadVars();



		} else {
			$alert['text'] = 'Static Page not saved. Navigation Title is a required field.';
		}
		break;

	default: break;
}

$sp_nav_titles = getStaticPageNavTitleStrings();
$pages = getStaticPages();

//page definitions
define("WINDOW_TITLE", "Static Page Editor");
define("PAGE_TITLE", "Static Page Editor");
?>
<?
include("_header.php");
?>

<? if ($domain->admin_wysiwyg) { ?>
<script language="javascript" type="text/javascript" src="../javascripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	editor_deselector : "",
	theme_advanced_disable : "styleselect,help,code",
	theme_advanced_buttons1_add : "fontselect,fontsizeselect",
	theme_advanced_buttons2_add_before : "forecolor",
	theme_advanced_buttons2_add : "separator,hr,removeformat,sub,sup,separator,charmap",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_align : "left",
	theme_advanced_toolbar_location : "top",
	theme_advanced_path_location : "bottom",
	theme_advanced_resizing : true
});

function toggleEditor(id) {
	var elm = document.getElementById(id);

	if (tinyMCE.getInstanceById(id) == null)
		tinyMCE.execCommand('mceAddControl', false, id);
	else
		tinyMCE.execCommand('mceRemoveControl', false, id);
}

</script>

<? } ?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
<form action="<? echo getScriptName(); ?>" method="post">
	<tr>
		<td colspan="2" class="tableHeading"><? echo $form_title ?></td>
		</tr>
	<tr>
		<td class="tableRowLeft1">Navigation Title </td>
		<td class="tableRowRight2"><input name="sp_nav_title" type="text" id="sp_nav_title" value="<? echo $sp->sp_nav_title ?>" size="60" /></td>
	</tr>
	<tr>
		<td class="tableRowLeft1">Parent Page </td>
		<td class="tableRowRight2"><select name="parent_id">
			<option value="0">- None -</option>
			<?
			if (count($sp_nav_titles) > 0) {
				foreach ($sp_nav_titles as $parent_sp_id => $sp_nav_title) {
					if ($parent_sp_id != $sp->sp_id) { //we cannot allow a parent assignment to itself
						$html .= '<option value="'.$parent_sp_id.'"';
						if ($parent_sp_id == $sp->parent_sp_id) $html .= ' SELECTED';
						$html .= '>'.$sp_nav_title.'</option>';
					}
				}
				echo $html;
			}
			?>
		</select>		</td>
	</tr>
	<? if ($form_action == "new") { ?>
	<tr>
		<td class="tableRowLeft1">Rank Within Parent</td>
		<td class="tableRowRight2"><input name="rank" type="text" id="rank" size="10" value="<? echo $sp->sp_rank ?>" /></td>
	</tr>
	<? } ?>
	<tr>
		<td class="tableRowLeft1">Slug</td>
		<td class="tableRowRight2"><input name="sp_slug" type="text" id="sp_slug" value="<? echo $sp->sp_slug ?>" size="60" />
			<br />
			No spaces. Letters and numbers only. Leave blank to auto-generate. </td>
	</tr>
	<tr>
		<td class="tableRowLeft1">Window Title </td>
		<td class="tableRowRight2"><input name="sp_win_title" type="text" id="sp_win_title" value="<? echo $sp->sp_win_title ?>" size="60" /></td>
	</tr>
	<tr>
		<td class="tableRowLeft1">META Description </td>
		<td class="tableRowRight2"><input name="sp_meta_desc" type="text" id="sp_meta_desc" value="<? echo $sp->sp_meta_desc ?>" size="60" /></td>
	</tr>
	<tr>
		<td class="tableRowLeft1">META Keywords </td>
		<td class="tableRowRight2"><input name="sp_meta_keys" type="text" id="sp_meta_keys" value="<? echo $sp->sp_meta_keys ?>" size="60" /></td>
	</tr>

	<tr>
		<td valign="top" class="tableRowLeft1">RSS URL </td>
		<td class="tableRowRight2"><input name="sp_rss_url" type="text" id="sp_rss_url" value="<? echo $sp->sp_rss_url ?>" size="60" />
			<br />
			Leave this blank if you do not want this to be an RSS feed page. <br />
			If you add an RSS URL here the results of that feed will appear on your Static Page below the Body portion of the page.</td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Body</td>
		<td class="tableRowRight2"><? if ($domain->admin_wysiwyg) { ?><div style="margin-bottom: 3px"><a href="javascript:toggleEditor('sp_html');">View / Hide Editor</a></div><? } ?><textarea name="sp_html" cols="100" rows="20" id="sp_html"><? echo $sp->sp_html ?></textarea></td>
	</tr>


	<tr>
		<td colspan="2" class="tableSubmitCell">
			<input name="action" type="hidden" id="action" value="<? echo $form_action ?>" />
			<? if ($sp->sp_id > 0) { ?>
			<input name="p" type="hidden" id="p" value="<? echo $sp->sp_id ?>" />
			<? } ?>
			<input type="submit" name="Submit" value="Submit" /></td>
	</tr>
</form>
</table>

<?
include("_footer.php");
?>
