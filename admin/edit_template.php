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

$templateFile = htmlspecialchars($_REQUEST['t'],ENT_QUOTES);

$templateFile = str_replace("..","",$templateFile);
$templateFile = str_replace(".php","",$templateFile);

//page prompts
switch ($action) {

	case "edit":
		
	$body = $_REQUEST['body'];

	if (!empty($body))
	{	
		file_put_contents(BASE_DIRECTORY."templates/custom/". $multiCheck."/" . $templateFile,$body);
		
		$alert['text'] = 'Templated updated';
	}
	break;
	default: break;
}


//page definitions
define("WINDOW_TITLE", "Edit Template $templateFile");
define("PAGE_TITLE", "Edit Template $templateFile");
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
	theme_advanced_disable : "styleselect,help",
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
		<td valign="top" class="tableRowLeft1">Template</td>
		<td class="tableRowRight2"><? if ($domain->admin_wysiwyg) { ?><div style="margin-bottom: 3px"><a href="javascript:toggleEditor('body');">View / Hide Editor</a></div><? } ?><textarea name="body" cols="100" rows="20" id="body"><? echo htmlspecialchars(DownloadFileContents(BASE_DIRECTORY."templates/custom/". $multiCheck."/" . $templateFile)); ?></textarea></td>
	</tr>


	<tr>
		<td colspan="2" class="tableSubmitCell">
			<input name="action" type="hidden" id="action" value="edit" />
			
			<input name="t" type="hidden" id="t" value="<? echo $templateFile ?>" />
			
			<input type="submit" name="Submit" value="Submit" /></td>
	</tr>
</form>
</table>

<?
include("_footer.php");
?>