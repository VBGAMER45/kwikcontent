<?php
/***************************************************************************
 *
 *   File                 : includes/yahoo.php
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


/*
	CLASSES
*/

class YahooNews extends DbTable
{
	function YahooNews($id = 0) {
		$db_table = "yahoo_news";
		$key_name = "ynw_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}

	function ripData() {
		if (!trim($this->ynw_data)) $this->loadVars();
		$this->data = unserialize(html_entity_decode($this->ynw_data));
	}

	function assignRank($new_rank = 0) {
		global $multi_prefix;
		/*
			USES TIMESTAMP RATHER THAN RANK, SO THIS IS USELESS
		*/

		//let's first get all of the current standings at this top_key_level
		$keyword = new Keyword($this->key_id);
		$array_current = condenseArray($keyword->getYahooNews(), "ynw_id", "ynw_rank");

		//check to see if $new_rank value needs to be altered
		if ($new_rank == 0 || count($array_current) < 1 || count($array_current) < $new_rank) {
			//give it the lowest rank(e.g. highest number)
			$new_rank = sizeof($array_current);
			if ($new_rank == 0) {
				$new_rank = 1;
			}
		}

		//if this had an existing rank, decrement all above it by one
		if ($this->ylk_rank > 0) {
			$sql = "
				UPDATE {$multi_prefix}$this->db_table
				SET ynw_rank = ynw_rank - 1
				WHERE ynw_rank > '".$this->ynw_rank."'
				AND key_id = '$this->key_id'
			";
			$update = mysql_query($sql) or die(mysql_error());
		}

		//now increment all ranks that hold the new rank or higher
		$sql = "
			UPDATE {$multi_prefix}$this->db_table
			SET ynw_rank = ynw_rank + 1
			WHERE ynw_rank >= '$new_rank'
			AND key_id = '$this->key_id'
		";
		$update = mysql_query($sql) or die(mysql_error());

		//now assign this one its new rank
		$this->setColumnValue("ynw_rank", $new_rank);
	}

	function destroyObject() {
		//$this->assignRank(0); - ranks not used here
		parent::destroyObject();
	}
}

class YahooImage extends DbTable
{
	function YahooImage($id = 0) {
		$db_table = "yahoo_image";
		$key_name = "yim_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}

	function ripData() {
		if (!trim($this->yim_data)) $this->loadVars();
		$this->img_info = unserialize(html_entity_decode($this->yim_data));
	}

	function getLocation() {
		$this->ripData();
		$info = $this->img_info;
		global $domain;
		return BASE_FOLDER.$domain->slugs['yahoo_image'].$this->key_value.'/'.getFakeFilename($info['Title']).'.html';
	}

	function assignRank($new_rank = 0) {
		global $multi_prefix;
		//let's first get all of the current standings at this top_key_level
		$keyword = new Keyword($this->key_id);
		$array_current = condenseArray($keyword->getYahooImages(), "yim_id", "yim_rank");

		//check to see if $new_rank value needs to be altered
		if ($new_rank == 0 || count($array_current) < 1 || count($array_current) < $new_rank) {
			//give it the lowest rank(e.g. highest number)
			$new_rank = sizeof($array_current);
			if ($new_rank == 0) {
				$new_rank = 1;
			}
		}

		//if this had an existing rank, decrement all above it by one
		if ($this->yim_rank > 0) {
			$sql = "
				UPDATE {$multi_prefix}$this->db_table
				SET yim_rank = yim_rank - 1
				WHERE yim_rank > '".$this->yim_rank."'
				AND key_id = '$this->key_id'
			";
			$update = mysql_query($sql) or die(mysql_error());
		}

		//now increment all ranks that hold the new rank or higher
		$sql = "
			UPDATE {$multi_prefix}$this->db_table
			SET yim_rank = yim_rank + 1
			WHERE yim_rank >= '$new_rank'
			AND key_id = '$this->key_id'
		";
		$update = mysql_query($sql) or die(mysql_error());

		//now assign this one its new rank
		$this->setColumnValue("yim_rank", $new_rank);
	}

	function destroyObject() {
		$this->assignRank(0);
		parent::destroyObject();
	}
}

class YahooLink extends DbTable
{
	function YahooLink($id = 0) {
		$db_table = "yahoo_link";
		$key_name = "ylk_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}

	function ripData() {
		if (!trim($this->ylk_data)) $this->loadVars();
		$this->link_info = unserialize(html_entity_decode($this->ylk_data));
		if (count($this->link_info) > 0) {
			foreach ($this->link_info as $key => $val) {
				$this->link_info[$key] = @html_entity_decode($val);
			}
		}
	}

	function getLocation() {
		//this doesn't actually exist
		$this->ripData();
		$info = $this->link_info;
		return BASE_FOLDER.'otherresources/view/'.$this->key_value.'/'.getFakeFilename($info['Title']).'.html';
	}

	function assignRank($new_rank = 0) {
		global $multi_prefix;
		//let's first get all of the current standings at this top_key_level
		$keyword = new Keyword($this->key_id);
		$array_current = condenseArray($keyword->getYahooLinks(), "ylk_id", "ylk_rank");

		//check to see if $new_rank value needs to be altered
		if ($new_rank == 0 || count($array_current) < 1 || count($array_current) < $new_rank) {
			//give it the lowest rank(e.g. highest number)
			$new_rank = sizeof($array_current);
			if ($new_rank == 0) {
				$new_rank = 1;
			}
		}

		//if this had an existing rank, decrement all above it by one
		if ($this->ylk_rank > 0) {
			$sql = "
				UPDATE {$multi_prefix}$this->db_table
				SET ylk_rank = ylk_rank - 1
				WHERE ylk_rank > '".$this->ylk_rank."'
				AND key_id = '$this->key_id'
			";
			$update = mysql_query($sql) or die(mysql_error());
		}

		//now increment all ranks that hold the new rank or higher
		$sql = "
			UPDATE {$multi_prefix}$this->db_table
			SET ylk_rank = ylk_rank + 1
			WHERE ylk_rank >= '$new_rank'
			AND key_id = '$this->key_id'
		";
		$update = mysql_query($sql) or die(mysql_error());

		//now assign this one its new rank
		$this->setColumnValue("ylk_rank", $new_rank);
	}

	function destroyObject() {
		$this->assignRank(0);
		parent::destroyObject();
	}
}

class YahooQA extends DbTable
{
	function YahooQA($id = 0) {
		$db_table = "yahoo_qa";
		$key_name = "yqa_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}

	function ripData() {
		if (!trim($this->yqa_data)) $this->loadVars();
		$this->qa_info = unserialize(html_entity_decode($this->yqa_data));
	}

	function getLocation() {
		global $domain;
		return BASE_FOLDER.$domain->slugs['yahoo_qa'].$this->key_value.'/'.getFakeFilename($this->y_q_subj).'.html';
	}

	function assignRank($new_rank = 0) {
		global $multi_prefix;
		//let's first get all of the current standings at this top_key_level
		$keyword = new Keyword($this->key_id);
		$array_current = condenseArray($keyword->getYahooQAs(), "yqa_id", "yqa_rank");

		//check to see if $new_rank value needs to be altered
		if ($new_rank == 0 || count($array_current) < 1 || count($array_current) < $new_rank) {
			//give it the lowest rank(e.g. highest number)
			$new_rank = sizeof($array_current);
			if ($new_rank == 0) {
				$new_rank = 1;
			}
		}

		//if this had an existing rank, decrement all above it by one
		if ($this->yqa_rank > 0) {
			$sql = "
				UPDATE {$multi_prefix}$this->db_table
				SET yqa_rank = yqa_rank - 1
				WHERE yqa_rank > '".$this->yqa_rank."'
				AND key_id = '$this->key_id'
			";
			$update = mysql_query($sql) or die(mysql_error());
		}

		//now increment all ranks that hold the new rank or higher
		$sql = "
			UPDATE {$multi_prefix}$this->db_table
			SET yqa_rank = yqa_rank + 1
			WHERE yqa_rank >= '$new_rank'
			AND key_id = '$this->key_id'
		";
		$update = mysql_query($sql) or die(mysql_error());

		//now assign this one its new rank
		$this->setColumnValue("yqa_rank", $new_rank);
	}

	function destroyObject() {
		$this->assignRank(0);
		parent::destroyObject();
	}
}



class YouTubeVideo extends DbTable
{
	function YouTubeVideo($id = 0) {
		$db_table = "youtube_video";
		$key_name = "ytv_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}


	function assignRank($new_rank = 0) {
		global $multi_prefix;
		/*
			USES TIMESTAMP RATHER THAN RANK, SO THIS IS USELESS
		*/

		//let's first get all of the current standings at this top_key_level
		$keyword = new Keyword($this->key_id);
		$array_current = condenseArray($keyword->getYahooNews(), "ytv_id", "ytv_rank");

		//check to see if $new_rank value needs to be altered
		if ($new_rank == 0 || count($array_current) < 1 || count($array_current) < $new_rank) {
			//give it the lowest rank(e.g. highest number)
			$new_rank = sizeof($array_current);
			if ($new_rank == 0) {
				$new_rank = 1;
			}
		}

		//if this had an existing rank, decrement all above it by one
		if ($this->ytv_rank > 0) {
			$sql = "
				UPDATE {$multi_prefix}$this->db_table
				SET ytv_rank = ytv_rank - 1
				WHERE ytv_rank > '".$this->ynw_rank."'
				AND key_id = '$this->key_id'
			";
			$update = mysql_query($sql) or die(mysql_error());
		}

		//now increment all ranks that hold the new rank or higher
		$sql = "
			UPDATE {$multi_prefix}$this->db_table
			SET ytv_rank = ytv_rank + 1
			WHERE ytv_rank >= '$new_rank'
			AND key_id = '$this->key_id'
		";
		$update = mysql_query($sql) or die(mysql_error());

		//now assign this one its new rank
		$this->setColumnValue("ytv_rank", $new_rank);
	}

	function destroyObject() {
		//$this->assignRank(0); - ranks not used here
		parent::destroyObject();
	}
}


class BingNews extends DbTable
{
	function BingNews($id = 0) {
		$db_table = "bing_news";
		$key_name = "bn_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}

	function ripData() {
		if (!trim($this->ynw_data)) $this->loadVars();
		$this->data = unserialize(html_entity_decode($this->ynw_data));
	}

	function assignRank($new_rank = 0) {
		global $multi_prefix;
		/*
			USES TIMESTAMP RATHER THAN RANK, SO THIS IS USELESS
		*/

		//let's first get all of the current standings at this top_key_level
		$keyword = new Keyword($this->key_id);
		$array_current = condenseArray($keyword->getBingNews(), "bn_id", "bn_rank");

		//check to see if $new_rank value needs to be altered
		if ($new_rank == 0 || count($array_current) < 1 || count($array_current) < $new_rank) {
			//give it the lowest rank(e.g. highest number)
			$new_rank = sizeof($array_current);
			if ($new_rank == 0) {
				$new_rank = 1;
			}
		}

		//if this had an existing rank, decrement all above it by one
		if ($this->bn_rank > 0) {
			$sql = "
				UPDATE {$multi_prefix}$this->db_table
				SET bn_rank = bn_rank - 1
				WHERE bn_rank > '".$this->bn_rank."'
				AND key_id = '$this->key_id'
			";
			$update = mysql_query($sql) or die(mysql_error());
		}

		//now increment all ranks that hold the new rank or higher
		$sql = "
			UPDATE {$multi_prefix}$this->db_table
			SET bn_rank = bn_rank + 1
			WHERE bn_rank >= '$new_rank'
			AND key_id = '$this->key_id'
		";
		$update = mysql_query($sql) or die(mysql_error());

		//now assign this one its new rank
		$this->setColumnValue("bn_rank", $new_rank);
	}

	function destroyObject() {
		//$this->assignRank(0); - ranks not used here
		parent::destroyObject();
	}
}
?>
