<?php
/***************************************************************************
 *
 *   File                 : admin/authors.php
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

switch ($action) {
	case "delete":
		$author = new Author($a);
		$author->destroyObject();
		$alert['text'] = "You have successfully deleted that author.";
		break;

	default: break;
}

$authors = getAuthors();

//page definitions
define("WINDOW_TITLE", "Authors");
define("PAGE_TITLE", "Authors");
?>
<?
include("_header.php");
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr>
		<td colspan="3" class="tableHeading">Authors </td>
		</tr>
	<tr>
		<td colspan="3" class="tableSubHeading" style="text-align: right"><img src="../site_icons/16/add2.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="author_edit.php">New Author </a> </td>
	</tr>
	<tr>
		<td class="tableColumnHeader">Domain</td>
		<td align="center" class="tableColumnHeader">Edit</td>
		<td align="center" class="tableColumnHeader">Delete</td>
	</tr>
	<?
	if (count($authors) > 0) {
		foreach ($authors as $id => $details) {
			$counter++;
			$css_num = ($counter % 2) + 1;
			$author = new Author($id);
			$author->setVars($details);
	?>
	<tr>
		<td class="tableRowRight<? echo $css_num ?>"><? echo $author->getName(); ?>&nbsp;</td>
		<td align="center" class="tableRowRight<? echo $css_num ?>"><img src="../site_icons/16/edit.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="author_edit.php?a=<? echo $author->author_id ?>">Edit</a></td>
		<td align="center" class="tableRowRight<? echo $css_num ?>"><img src="../site_icons/16/delete2.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="authors.php?a=<? echo $author->author_id ?>&action=delete" onClick="return confirmIt(':: WARNING ::\n\nYou are about to permanently delete this author!');">Delete</a></td>
	</tr>
	<? }} else { ?>
	<tr>
		<td colspan="3" class="tableRowNoResults">None Found. </td>
	</tr>
	<? } ?>
</table>

<?
include("_footer.php");
?>
