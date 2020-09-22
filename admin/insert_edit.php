<?php
/***************************************************************************
 *
 *   File                 : admin/insert_edit.php
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
include_once("../includes/insert.php");

//new or edit?
if ($i > 0) {
	$pi = new PageInsert($i);
	$pi->loadVars();
	if (!($pi->pi_id > 0)) {
		header("Location: inserts.php");
		die();
	}

	$form_title = 'Edit Insert';
	$form_action = 'edit';

} else {
	$form_title = 'Save New Insert';
	$form_action = 'new';
}

//page prompts
switch ($action) {
	case "new":
		if (trim($pi_page) != '') {

			$pi = new PageInsert();
			$pi->createNew(array("pi_page" => $pi_page));
			$pi->arrayToTable($_POST, true);
			$pi->loadVars();

			$form_title = 'Edit Insert';
			$form_action = 'edit';

			$alert['text'] = 'This Insert has been properly saved.';

		} else {
			$alert['text'] = 'Insert not saved. Page To Place is a required field.';
		}
		break;

	case "edit":
		if (trim($pi_page) != '') {

			$pi->arrayToTable($_POST, true);

			$alert['text'] = 'This Insert has been properly saved.';

		} else {
			$alert['text'] = 'Insert not saved. Page To Place is a required field.';
		}
		break;

	default: break;
}

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
		<td valign="top" class="tableRowLeft1">Page To Place </td>
		<td class="tableRowRight2"><? echo removeLastChars(BASE_URL, 1); ?><input name="pi_page" type="text" id="pi_page" value="<? echo $pi->pi_page?>" size="60" />
			<br />
			<br />
			<strong>Important:</strong> Be sure to include the first slash (/) of the page to place this on, but do not include your domain name.<br />
			<strong>Example:</strong> /images/view/123/someimage.html</td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">HTML To Insert </td>
		<td class="tableRowRight2"><? if ($domain->admin_wysiwyg) { ?><div style="margin-bottom: 3px"><a href="javascript:toggleEditor('pi_html');">View / Hide Editor</a></div><? } ?><textarea name="pi_html" cols="100" rows="20" id="pi_html"><? echo $pi->pi_html ?></textarea></td>
	</tr>
	<tr>
		<td colspan="2" class="tableSubmitCell">
			<input name="action" type="hidden" id="action" value="<? echo $form_action ?>" />
			<? if ($pi->pi_id > 0) { ?>
			<input name="i" type="hidden" id="i" value="<? echo $pi->pi_id ?>" />
			<? } ?>
			<input type="submit" name="Submit" value="Submit" /></td>
	</tr>
</form>
</table>

<?
include("_footer.php");
?>
