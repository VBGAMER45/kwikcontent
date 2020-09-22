<?php
/***************************************************************************
 *
 *   File                 : admin/article_edit.php
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
	$article = new Article($a);
	$article->loadVars();
	if (!($article->article_id > 0)) {
		header("Location: articles.php");
		die();
	}

	$form_title = 'Edit Article';
	$form_action = 'edit';

} else {
	$form_title = 'Save New Article';
	$form_action = 'new';
}

//page prompts
switch ($action) {
	case "new":
		if (trim($article_title) != "") {
			$article = new Article();
			$article->createNew(array("key_id" => $key_id));
			$article->arrayToTable($_POST, true);
			$article->loadVars();
			$article->assignRank(0);
			$article->loadVars();
			$alert['text'] = 'Article successfully saved.';

			//set up edit
			$form_title = 'Edit Article';
			$form_action = 'edit';

		} else {
			$alert['text'] = 'Article not saved. Article title is a required field.';
		}
		break;

	case "edit":
		if (trim($article_title) != "") {
			if ($key_id != $article->key_id) {
				$article->assignRank(0);
				$article->arrayToTable($_POST, true);
				$article->loadVars();
				$article->setColumnValue("article_rank", count(getArticles()));
				$article->loadVars();
				$article->assignRank(0);
				$article->loadVars();
			} else {
				$article->arrayToTable($_POST, true);
				$article->loadVars();
			}
			$alert['text'] = 'Article successfully saved.';
		} else {
			$alert['text'] = 'Article not saved. Article title is a required field.';
		}
		break;

	default: break;
}

$keys = condenseArray(getKeywords(0), "key_id", "key_text");
$authors = condenseArray(getAuthors(), "author_id", "author_name");

//page definitions
define("WINDOW_TITLE", "Article Editor");
define("PAGE_TITLE", "Article Editor");
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
	editor_deselector : "articleTeaser",
	theme_advanced_disable : "styleselect,help,code",
	theme_advanced_buttons1_add : "fontselect,fontsizeselect",
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
		<td class="tableRowLeft1">Author</td>
		<td class="tableRowRight2"><? echo getMenuHtml("author_id", $authors, $article->author_id, "No authors found."); ?></td>
	</tr>
	<tr>
		<td class="tableRowLeft1">Top-level Keyword</td>
		<td class="tableRowRight2"><? echo getMenuHtml("key_id", $keys, $article->key_id, "No keywords found."); ?></td>
	</tr>
	<tr>
		<td class="tableRowLeft1">Article Title </td>
		<td class="tableRowRight2"><input name="article_title" type="text" id="article_title" value="<? echo $article->article_title ?>" size="60" /></td>
	</tr>
	<tr>
		<td class="tableRowLeft1">META Description </td>
		<td class="tableRowRight2"><input name="article_meta_desc" type="text" id="article_meta_desc" value="<? echo $article->article_meta_desc ?>" size="60" /><br />Leave blank for system generated meta.</td>
	</tr>
	<tr>
		<td class="tableRowLeft1">META Keywords </td>
		<td class="tableRowRight2"><input name="article_meta_keys" type="text" id="article_meta_keys" value="<? echo $article->article_meta_keys ?>" size="60" /><br />Leave blank for system generated meta.</td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Teaser </td>
		<td class="tableRowRight2"><textarea name="article_teaser" cols="100" rows="10" id="article_teaser" class="articleTeaser"><? echo $article->article_teaser ?></textarea></td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Body</td>
		<td class="tableRowRight2"><? if ($domain->admin_wysiwyg) { ?><div style="margin-bottom: 3px"><a href="javascript:toggleEditor('article_body');">View / Hide Editor</a></div><? } ?><textarea name="article_body" cols="100" rows="20" id="article_body"><? echo $article->article_body ?></textarea></td>
	</tr>
	<tr>
		<td valign="top" class="tableRowLeft1">Acknowledgments</td>
		<td class="tableRowRight2"><? if ($domain->admin_wysiwyg) { ?><div style="margin-bottom: 3px"><a href="javascript:toggleEditor('article_biblio');">View / Hide Editor</a></div><? } ?><textarea name="article_biblio" cols="100" rows="10" id="article_biblio"><? echo $article->article_biblio ?></textarea></td>
	</tr>

	<tr>
		<td colspan="2" class="tableSubmitCell">
			<input name="action" type="hidden" id="action" value="<? echo $form_action ?>" />
			<? if ($article->article_id > 0) { ?>
			<input name="a" type="hidden" id="a" value="<? echo $article->article_id ?>" />
			<? } ?>
			<input type="submit" name="Submit" value="Submit" /></td>
	</tr>
</form>
</table>

<?
include("_footer.php");
?>
