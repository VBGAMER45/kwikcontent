<?php
/***************************************************************************
 *
 *   File                 : admin/inserts.php
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

switch ($action) {
	case "delete":
		$pi = new PageInsert($i);
		$pi->loadVars();
		$pi->destroyObject();
		$alert['text'] = 'That insert has been successfully removed.';
		break;

	default: break;
}

$inserts = getPageInserts();

//page definitions
define("WINDOW_TITLE", "Inserts");
define("PAGE_TITLE", "Inserts");
?>
<?
include("_header.php");
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr>
		<td colspan="3" class="tableHeading">Inserts </td>
		</tr>
	<tr>
		<td colspan="3" class="tableSubHeading">Inserts allow you to place additional HTML on any specific page of your website. </td>
	</tr>
	<tr>
		<td colspan="3" class="tableSubHeading" style="text-align: right"><img src="../site_icons/16/add2.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="insert_edit.php">New Insert </a> </td>
	</tr>
	<tr>
		<td class="tableColumnHeader">Page To Place </td>
		<td align="center" class="tableColumnHeader">Edit</td>
		<td align="center" class="tableColumnHeader">Delete</td>
	</tr>
	<?
	if (count($inserts) > 0) {
		foreach ($inserts as $id => $details) {
			$counter++;
			$css_num = ($counter % 2) + 1;
			$pi = new PageInsert($id);
			$pi->setVars($details);
	?>
	<tr>
		<td class="tableRowRight<? echo $css_num ?>"><a href="<? echo $pi->getUrl(); ?>" target="_blank"><? echo $pi->getUrl(); ?></a>&nbsp;</td>
		<td align="center" class="tableRowRight<? echo $css_num ?>">
		<img src="../site_icons/16/edit.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="insert_edit.php?i=<? echo $pi->pi_id ?>">Edit</a></td>
		<td align="center" class="tableRowRight<? echo $css_num ?>"><img src="../site_icons/16/delete2.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?action=delete&i=<? echo $pi->pi_id ?>" onclick="return confirmIt(':: WARNING ::\n\nYou are about to permanently delete this insert!');">Delete</a></td>
	</tr>
	<? }} else { ?>
	<tr>
		<td colspan="3" class="tableRowNoResults">None Found. <a href="insert_edit.php">Create New</a>.</td>
	</tr>
	<? } ?>
</table>

<?
include("_footer.php");
?>
