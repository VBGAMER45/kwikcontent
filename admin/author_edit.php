<?php
/***************************************************************************
 *
 *   File                 : admin/author_edit.php
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
include_once("../includes/article.php");

//new or edit?
if ($a > 0) {
	$author = new Author($a);
	$author->loadVars();
	if (!($author->author_id > 0)) {
		header("Location: domains.php");
		die();
	}

	$form_title = 'Edit Author';
	$form_action = 'edit';

} else {
	$form_title = 'Save New Author';
	$form_action = 'new';
}

//page prompts
switch ($action) {
	case "new":
		if (trim($author_name) != "") {
			$author = new Author();
			$author->createNew(array("author_name" => $author_name));
			$author->arrayToTable($_POST);
			$author->loadVars();
			$alert['text'] = 'Author details successfully saved.';

			//set up edit
			$form_title = 'Edit Author';
			$form_action = 'edit';

		} else {
			$alert['text'] = 'Author details not saved. Author name is a required field.';
		}
		break;

	case "edit":
		if (trim($author_name) != "") {
			$author->arrayToTable($_POST);
			$author->loadVars();
			$alert['text'] = 'Author details successfully saved.';
		} else {
			$alert['text'] = 'Author details not saved. Author name is a required field.';
		}
		break;

	default: break;
}

//page definitions
define("WINDOW_TITLE", "Author Editor");
define("PAGE_TITLE", "Author Editor");
?>
<?
include("_header.php");
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
<form action="<? echo getScriptName(); ?>" method="post">
	<tr>
		<td colspan="2" class="tableHeading"><? echo $form_title ?></td>
		</tr>
	<tr>
		<td class="tableRowLeft1">Author's Name </td>
		<td class="tableRowRight2"><input name="author_name" type="text" id="author_name" value="<? echo $author->author_name ?>" size="60" /></td>
	</tr>
	<tr>
		<td class="tableRowLeft1">Author's URL </td>
		<td class="tableRowRight2"><input name="author_url" type="text" id="author_url" value="<? echo $author->author_url ?>" size="60" /></td>
	</tr>
	<tr>
		<td colspan="2" class="tableSubmitCell"><input name="action" type="hidden" id="action" value="<? echo $form_action ?>" />
			<? if ($author->author_id > 0) { ?>
			<input name="a" type="hidden" id="a" value="<? echo $author->author_id ?>" />
			<? } ?>
<input type="submit" name="Submit" value="Submit" /></td>
	</tr>
</form>
</table>


<?
include("_footer.php");
?>
