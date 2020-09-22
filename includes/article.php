<?php
/***************************************************************************
 *
 *   File                 : includes/article.php
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

	function getAuthors() {
			global $multi_prefix;
		$sql = "
			SELECT *
			FROM {$multi_prefix}author
			ORDER BY author_name
		";
		return getArrayFromSql("author_id", $sql);
	}


/*
	CLASSES
*/
class Article extends DbTable
{
	function Article($id = 0) {
		$db_table = "article";
		$key_name = "article_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}

	function getLocation() {
		global $domain;
		return BASE_FOLDER.$domain->slugs['article'].$this->key_value.'/'.getFakeFilename($this->article_title).'.html';
	}

	function assignRank($new_rank = 0) {
		global $multi_prefix;
		//let's first get all of the current standings at this top_key_level
		$keyword = new Keyword($this->key_id);
		$array_current = condenseArray($keyword->getArticles(), "article_id", "article_rank");

		//check to see if $new_rank value needs to be altered
		if ($new_rank == 0 || count($array_current) < 1 || count($array_current) < $new_rank) {
			//give it the lowest rank(e.g. highest number)
			$new_rank = sizeof($array_current);
			if ($new_rank == 0) {
				$new_rank = 1;
			}
		}

		//if this had an existing rank, decrement all above it by one
		if ($this->article_rank > 0) {
			$sql = "
				UPDATE {$multi_prefix}$this->db_table
				SET article_rank = article_rank - 1
				WHERE article_rank > '".$this->article_rank."'
				AND key_id = '$this->key_id'
			";
			$update = mysql_query($sql) or die(mysql_error());
		}

		//now increment all ranks that hold the new rank or higher
		$sql = "
			UPDATE $this->db_table
			SET article_rank = article_rank + 1
			WHERE article_rank >= '$new_rank'
			AND key_id = '$this->key_id'
		";
		$update = mysql_query($sql) or die(mysql_error());

		//now assign this one its new rank
		$this->setColumnValue("article_rank", $new_rank);
	}

	function destroyObject() {
		$this->assignRank(0);
		parent::destroyObject();
	}
}


class Author extends DbTable
{
	function Author($id = 0) {
		$db_table = "author";
		$key_name = "author_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}

	function getName() {
		if (trim($this->author_url) != "") {
			$html = '<a href="'.$this->author_url.'">'.$this->author_name.'</a>';
		} else {
			$html = $this->author_name;
		}
		return $html;
	}
}
?>
