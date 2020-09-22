<?php
/***************************************************************************
 *
 *   File                 : includes/insert.php
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

	function getPageInserts() {
		global $multi_prefix;
		$sql = "
			SELECT *
			FROM {$multi_prefix}page_insert
			ORDER BY pi_page
		";
		return getArrayFromSql('pi_id', $sql);
	}


/*
	CLASSES
*/
class PageInsert extends DbTable
{
	function PageInsert($id = 0) {
		$db_table = "page_insert";
		$key_name = "pi_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}

	function getUrl() {
		return removeLastChars(BASE_URL, 1).$this->pi_page;
	}
}
?>
