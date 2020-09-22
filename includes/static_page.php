<?php
/***************************************************************************
 *
 *   File                 : includes/static_page.php
 *   Software             : Kwikcontent
 *   Version              : 1.1
 *   Release Date         : August 29, 2009
 *   Copyright            : (C) 2009 Samson Software
 *   Contact              : http://www.kwikcontent.com/
 *   License              : Limited to one owner.  My not be reproduced or
 *                          redistributed under any circumstance.
 *
 ***************************************************************************/



/*
	FUNCTIONS
*/

	function getStaticPageNavTitleStrings($delim = '>') {
		$array = getStaticPageNavTitles();
		if (count($array) > 0) {
			foreach ($array as $sp_id => $sp) {

				$parent_sp_id = $sp['parent_sp_id'];

				if ($parent_sp_id == '0') {
					$parents_nav_string[$sp_id] = $sp['sp_nav_title'];
				} else {
					$parents_nav_string[$sp_id] = $parents_nav_string[$parent_sp_id].' '.$delim.' '.$sp['sp_nav_title'];
				}

				$nav_titles[$sp_id] = $parents_nav_string[$sp_id];
			}
		}
		return $nav_titles;
	}

	function getStaticPageNavTitles() {
		global $multi_prefix;
		$sql = "
			SELECT sp_id, parent_sp_id, sp_nav_title
			FROM {$multi_prefix}static_page
			ORDER BY sp_rank ASC
		";
		$q = mysql_query($sql);
		return traverseStaticPageNavTitles(0, 0, $q, array());
	}

	function traverseStaticPageNavTitles($root, $depth, $q, $pages) {

		while ($r = mysql_fetch_array($q)) {

			if ($r['parent_sp_id'] == $root) {

				$parent_sp_id = $r['parent_sp_id'];
				$sp_id = $r['sp_id'];

				$pages[$sp_id] = $r;

				mysql_data_seek($q,0);
				$pages = traverseStaticPageNavTitles($r['sp_id'], ($depth + 1), $q, $pages);
			}

			$row++;
			@mysql_data_seek($q,$row);
		}

		return $pages;
	}

	function traverseStaticPageNavTitlesList($root, $depth, $q, $pages, $pages_data_array, $list_html_var) {

		while ($r = mysql_fetch_array($q)) {

			$GLOBALS[$list_html_var] .= '<ul>';
			if ($r['parent_sp_id'] == $root) {

				$parent_sp_id = $r['parent_sp_id'];
				$sp_id = $r['sp_id'];

				$pages[$sp_id] = $r;

				$GLOBALS[$list_html_var] .= '<li><a href="'.$GLOBALS[$pages_data_array][$sp_id]['sp_location'].'">'.$GLOBALS[$pages_data_array][$sp_id]['sp_nav_title'].'</a></li>';

				mysql_data_seek($q,0);
				traverseStaticPageNavTitlesList($r['sp_id'], ($depth + 1), $q, $pages, $pages_data_array, $list_html_var);
			}

			$row++;
			@mysql_data_seek($q,$row);

			$GLOBALS[$list_html_var] .= '</ul>';
		}
	}

	function getStaticPages($parent_sp_id = '-1') {
		global $multi_prefix;
		$sql = "
			SELECT *
			FROM {$multi_prefix}static_page
		";

		if ($parent_sp_id > -1) {
			$sql .= "
				WHERE parent_sp_id = '$parent_sp_id'
			";
		}

		$sql .= "
			ORDER BY sp_rank
		";
		return getArrayFromSql('sp_id', $sql);
	}


/*
	CLASSES
*/
class StaticPage extends DbTable
{
	function StaticPage($id = 0) {
		$db_table = "static_page";
		$key_name = "sp_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}

	function getLocation() {
		global $domain;
		if (trim($this->sp_slug) != '') {
			return BASE_FOLDER.$domain->slugs['static_page'].$this->key_value.'/'.$this->sp_slug;
		} else {
			return BASE_FOLDER.$domain->slugs['static_page'].$this->key_value.'/'.getFakeFilename($this->sp_nav_title).'.html';
		}
	}

	function getChildren() {
		global $multi_prefix;
		$sql = "
			SELECT *
			FROM {$multi_prefix}$this->db_table
			WHERE parent_sp_id = '$this->sp_id'
			ORDER BY sp_rank
		";
		return getArrayFromSql('sp_id', $sql);
	}

	function assignParent($parent_sp_id) {
		//ensure this is a new parent
		if ($this->parent_sp_id != $parent_sp_id) {

			//ensure we are not assigning a parent of itself
			$current_parent = $parent_sp_id;
			while ($current_parent != '0') {
				$sp = new StaticPage($current_parent);
				$sp->loadVars();
				if ($sp->parent_sp_id != $this->sp_id) {
					$current_parent = $sp->parent_sp_id;
				} else {
					//trying to assign a child of itself
					return 'You cannot make this page a child of itself.';
					break;
				}
			}

			//if we get here then we can reassign the parent
			//remove all ranking
			$this->assignRank(0);
			$this->setColumnValue('sp_rank', '0');
			//give the highest rank in the new category
			$this->setColumnValue('parent_sp_id', $parent_sp_id);
			$this->loadVars();
			$this->assignRank(0);
			return false;

		} else {
			return 'This page already has that parent.';
		}

	}

	function assignRank($new_rank = 0) {
		global $multi_prefix;
		//let's first get all of the current standings at this level
		$array_current = condenseArray(getStaticPages($this->parent_sp_id), "sp_id", "sp_rank");

		//check to see if $new_rank value needs to be altered
		if ($new_rank == 0 || count($array_current) < 1 || count($array_current) < $new_rank) {
			//give it the lowest rank(e.g. highest number)
			$new_rank = sizeof($array_current);
			if ($new_rank == 0) {
				$new_rank = 1;
			}
		}

		//if this had an existing rank, decrement all above it by one
		if ($this->sp_rank > 0) {
			$sql = "
				UPDATE {$multi_prefix}$this->db_table
				SET sp_rank = sp_rank - 1
				WHERE sp_rank > '".$this->sp_rank."'
				AND parent_sp_id = '$this->parent_sp_id'
			";
			$update = mysql_query($sql) or die(mysql_error());
		}

		//now increment all ranks that hold the new rank or higher
		$sql = "
			UPDATE {$multi_prefix}$this->db_table
			SET sp_rank = sp_rank + 1
			WHERE sp_rank >= '$new_rank'
			AND parent_sp_id = '$this->parent_sp_id'
		";
		$update = mysql_query($sql) or die(mysql_error());

		//now assign this one its new rank
		$this->setColumnValue("sp_rank", $new_rank);
	}

	function destroyObject() {
		//give all of its children the current parent id
		$kids = $this->getChildren();
		if (count($kids) > 0) {
			foreach ($kids as $id => $details) {
				$sp = new StaticPage($id);
				$sp->setVars($details);
				//assign the child's parent to the value of it's grandparent
				$sp->setColumnValue('parent_sp_id', $this->parent_sp_id);
				$sp->setColumnValue('sp_rank', '0');
				//rank it highest in that category.  Note: the kids were delivered in the properly ranked order
				$sp->loadVars();
				$sp->assignRank(0);
			}
		}

		//rank it highest in this level
		$this->assignRank(0);

		//delete this page
		parent::destroyObject();
	}
}
?>
