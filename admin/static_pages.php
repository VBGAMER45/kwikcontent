<?php
/***************************************************************************
 *
 *   File                 : admin/static_pages.php
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

switch ($action) {
	case "delete":
		$sp = new StaticPage($p);
		$sp->loadVars();
		$sp->destroyObject();
		$alert['text'] = 'That static page has been successfully removed.';
		break;

	case "rank":
		if ($rank > 0) {
			$sp = new StaticPage($p);
			$sp->loadVars();
			$sp->assignRank($rank);
		}
		break;

	default: break;
}

$sp_nav_titles = getStaticPageNavTitleStrings();
$pages = getStaticPages();

//page definitions
define("WINDOW_TITLE", "Static Pages");
define("PAGE_TITLE", "Static Pages");
?>
<?
include("_header.php");
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr>
		<td colspan="4" class="tableHeading">Static Pages </td>
		</tr>
	<tr>
		<td colspan="4" class="tableSubHeading" style="text-align: right"><img src="../site_icons/16/add2.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="static_page_edit.php">New Static Page </a> </td>
	</tr>
	<tr>
		<td class="tableColumnHeader">Static Page </td>
		<td align="center" class="tableColumnHeader">Rank <br /><span style="font-weight:normal">(within parent)</span></td>
		<td align="center" class="tableColumnHeader">Edit</td>
		<td align="center" class="tableColumnHeader">Delete</td>
	</tr>
	<?
	if (count($sp_nav_titles) > 0) {
		foreach ($sp_nav_titles as $sp_id => $sp_details) {
			$counter++;
			$css_num = ($counter % 2) + 1;
			$sp = new StaticPage($sp_id);
			$sp->setVars($pages[$sp_id]);
	?>
	<tr>
		<td class="tableRowRight<? echo $css_num ?>"><a href="<? echo $sp->getLocation(); ?>" target="_blank"><? echo $sp_nav_titles[$sp->sp_id]; ?></a>&nbsp;</td>
		<td align="center" class="tableRowRight<? echo $css_num ?>"><? echo $sp->sp_rank ?><br />
				<a href="<? echo getScriptName(); ?>?p=<? echo $sp->sp_id ?>&action=rank&rank=<? echo ($sp->sp_rank - 1); ?>"><img src="../site_icons/16/arrow_up_blue.gif" width="16" height="16" hspace="2" border="0" align="absmiddle" /></a>
				<a href="<? echo getScriptName(); ?>?p=<? echo $sp->sp_id ?>&action=rank&rank=<? echo ($sp->sp_rank + 1); ?>"><img src="../site_icons/16/arrow_down_blue.gif" width="16" height="16" hspace="2" border="0" align="absmiddle" /></a> </td>
		<td align="center" class="tableRowRight<? echo $css_num ?>">
		<img src="../site_icons/16/edit.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="static_page_edit.php?p=<? echo $sp->sp_id ?>">Edit</a></td>
		<td align="center" class="tableRowRight<? echo $css_num ?>"><img src="../site_icons/16/delete2.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?action=delete&p=<? echo $sp->sp_id ?>" onclick="return confirmIt(':: WARNING ::\n\nYou are about to permanently delete this static page!');">Delete</a></td>
	</tr>
	<? }} else { ?>
	<tr>
		<td colspan="4" class="tableRowNoResults">None Found. <a href="static_page_edit.php">Create New</a>.</td>
	</tr>
	<? } ?>
</table>

<?
include("_footer.php");
?>
