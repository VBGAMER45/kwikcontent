<?php
/***************************************************************************
 *
 *   File                 : admin/articles.php
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
		$article = new Article($a);
		$article->loadVars();
		$article->destroyObject();
		$alert['text'] = 'That article has been successfully removed.';
		break;

	case "rank":
		$article = new Article($a);
		$article->loadVars();
		$article->assignRank($rank);
		break;

	default: break;
}

$keys = condenseArray(getKeywords(0), "key_id", "key_text");
$articles = getArticles();
$authors = condenseArray(getAuthors(), "author_id", "author_name");

//page definitions
define("WINDOW_TITLE", "Articles");
define("PAGE_TITLE", "Articles");
?>
<?
include("_header.php");
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr>
		<td colspan="6" class="tableHeading">Articles </td>
		</tr>
	<tr>
		<td colspan="6" class="tableSubHeading" style="text-align: right"><img src="../site_icons/16/add2.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="article_edit.php?d=<? echo $domain->domain_id ?>">New Article </a> </td>
	</tr>
	<tr>
		<td class="tableColumnHeader">Title</td>
		<td align="center" class="tableColumnHeader">Rank<br />
			<span style="font-weight:normal">(Within Keyword)</span></td>
		<td class="tableColumnHeader">Keyword</td>
		<td class="tableColumnHeader">Author</td>
		<td align="center" class="tableColumnHeader">Edit</td>
		<td align="center" class="tableColumnHeader">Delete</td>
	</tr>
	<?
	if (count($articles) > 0) {
		foreach ($articles as $id => $details) {
			$counter++;
			$css_num = ($counter % 2) + 1;
			$article = new Article($id);
			$article->setVars($details);
			$rank_up = $article->article_rank - 1;
			$rank_down = $article->article_rank + 1;
	?>
	<tr>
		<td class="tableRowRight<? echo $css_num ?>"><? echo $article->article_title ?>&nbsp;</td>
		<td align="center" class="tableRowRight<? echo $css_num ?>"><? echo $article->article_rank ?><br />
			<a href="<? echo getScriptName(); ?>?a=<? echo $article->article_id ?>&action=rank&rank=<? echo $rank_up ?>"><img src="../site_icons/16/arrow_up_blue.gif" width="16" height="16" hspace="2" border="0" align="absmiddle" /></a>
			<a href="<? echo getScriptName(); ?>?a=<? echo $article->article_id ?>&action=rank&rank=<? echo $rank_down ?>"><img src="../site_icons/16/arrow_down_blue.gif" width="16" height="16" hspace="2" border="0" align="absmiddle" /></a></td>
		<td class="tableRowRight<? echo $css_num ?>"><? echo $keys[$article->key_id]; ?>&nbsp;</td>
		<td class="tableRowRight<? echo $css_num ?>"><? echo $authors[$article->author_id]; ?>&nbsp;</td>
		<td align="center" class="tableRowRight<? echo $css_num ?>">
		<img src="../site_icons/16/edit.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="article_edit.php?a=<? echo $article->article_id ?>&d=<? echo $domain->domain_id ?>">Edit</a></td>
		<td align="center" class="tableRowRight<? echo $css_num ?>"><img src="../site_icons/16/delete2.gif" width="16" height="16" hspace="2" align="absmiddle" /> <a href="<? echo getScriptName(); ?>?action=delete&a=<? echo $article->article_id ?>&d=<? echo $domain->domain_id ?>" onclick="return confirmIt(':: WARNING ::\n\nYou are about to permanently delete this article!');">Delete</a></td>
	</tr>
	<? }} else { ?>
	<tr>
		<td colspan="6" class="tableRowNoResults">None Found. </td>
	</tr>
	<? } ?>
</table>

<?
include("_footer.php");
?>
