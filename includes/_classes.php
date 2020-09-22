<?php
/***************************************************************************
 *
 *   File                 : includes/_classes.php
 *   Software             : Kwikcontent
 *   Version              : 1.1
 *   Release Date         : August 29, 2009
 *   Copyright            : (C) 2009-2011 Samson Software
 *   Contact              : http://www.kwikcontent.com/
 *   License              : Limited to one owner.  My not be reproduced or
 *                          redistributed under any circumstance.
 *
 ***************************************************************************/


/*
	GENERAL USE CLASSES
*/
class DbTable
{
	function DbTable($db_table, $key_name = "", $key_value = "") {
		//db defs
		$this->db_table = $db_table;
		$this->key_name = $key_name;
		$this->key_value = $key_value;
		//update defs
		$this->time_last_mofified = MicroTimer::now();
		$this->time_last_loaded = 0;
	}

	/*
		begin typical functions for all objects
	*/

	function arrayToTable($value_array, $allow_html = false) {
		GLobal $multi_prefix;
		//get possible table column names
		$sql = "SELECT * FROM {$multi_prefix}$this->db_table WHERE $this->key_name = '$this->key_value' LIMIT 0,1";
		$q = mysql_query($sql);
		if (mysql_num_fields($q) > 0) {
			$row = mysql_fetch_assoc($q);
			foreach($row as $field_name => $field_value) {
				$column_array[$field_name] = $field_name;
			}
			unset($q, $row);

			//construct string for update query
			if (count($value_array) > 0) {
				foreach ($value_array as $column => $value) {
					if (in_array($column, $column_array)) {
						if ($allow_html) {
							$value = htmlentities(trim($value));
							if (ini_get("magic_quotes_gpc") != "On" && ini_get("magic_quotes_gpc") != "1") {
								$value = addslashes($value);
							}
						} else {
							$value = htmlentities(strip_tags(trim($value)));
							if (ini_get("magic_quotes_gpc") != "On" && ini_get("magic_quotes_gpc") != "1") {
								$value = addslashes($value);
							}
						}
						$sql_middle .= " $column='$value', ";
					}
				}
				unset($value_array, $column, $value, $column_array);

				//eliminate last comma
				$sql_middle = substr($sql_middle, 0, (strlen($sql_middle) - 2));

				//construct and execute query
				$sql = "UPDATE {$multi_prefix}$this->db_table SET $sql_middle WHERE $this->key_name = '$this->key_value'";
				$update = mysql_query($sql);

				//update modified time
				$this->updateModifyTime();

				return true;

			} else {
				return false;
			}

		} else {
			return false;
		}
	}

	function loadVars() {
		if ($this->areVarsFresh()) {
			//no action needed.  this instance has the most current stored vars
		} else {
			$this->setStoredVars();
		}
	}

	function setStoredVars() {
		global $multi_prefix;
		$q = mysql_query("SELECT * FROM {$multi_prefix}$this->db_table WHERE $this->key_name = '$this->key_value'") or die(mysql_error());
		if (mysql_num_rows($q) > 0) {
			$r = mysql_fetch_object($q);
			$var_array = get_object_vars($r);
			if (count($var_array) > 0) {
				foreach ($var_array as $key => $value) {
					$this->$key = $value;
				}
			}

			//update load time
			$this->updateLoadTime();
		}
	}

	function setVars($var_array) {
		if (count($var_array) > 0) {
			foreach ($var_array as $key => $value) {
				$this->$key = $value;
			}

			//update load time
			$this->updateLoadTime();
		}
	}

	function setColumnValue($column, $value, $allow_html = false) {
		global $multi_prefix;
		if ($allow_html) {
			$value = htmlentities(trim($value));
			if (ini_get("magic_quotes_gpc") != "On" && ini_get("magic_quotes_gpc") != "1") {
				$value = addslashes($value);
			}
		} else {
			$value = htmlentities(strip_tags(trim($value)));
			if (ini_get("magic_quotes_gpc") != "On" && ini_get("magic_quotes_gpc") != "1") {
				$value = addslashes($value);
			}
		}
		$update = mysql_query("UPDATE {$multi_prefix}$this->db_table SET $column='$value' WHERE $this->key_name = '$this->key_value'");

		//update modified time
		$this->updateModifyTime();
	}

	function createNew($insert_array, $show_error = false) {
		global $multi_prefix;
		//acts as a "catch-all" createNew
		if (count($insert_array) > 0) {
			$counter = 1;
			foreach ($insert_array as $key => $value) {
				$counter++;
				$value = htmlentities(trim($value));
				if (ini_get("magic_quotes_gpc") != "On" && ini_get("magic_quotes_gpc") != "1") {
					$value = addslashes($value);
				}
				$value_array[$counter] = $value;
				$key_array[$counter] = $key;
			}
		}
		$sql = "INSERT INTO {$multi_prefix}$this->db_table (";
		foreach ($key_array as $key) {
			$sql .= "$key, ";
		}
		$sql = removeLastChars($sql, 2);
		$sql .= ") VALUES (";
		foreach ($value_array as $value) {
			$sql .= "'$value', ";
		}
		$sql = removeLastChars($sql, 2);
		$sql .= ")";
		if ($show_error) {
			$insert = mysql_query($sql) or die(mysql_error().'<BR><BR>'.$sql);
		} else {
			$insert = mysql_query($sql);
		}
		$this->key_value = mysql_insert_id();

		//update modified time
		$this->updateModifyTime();
	}

	function updateModifyTime() {
		$this->time_last_mofified = MicroTimer::now();
	}

	function updateLoadTime() {
		$this->time_last_loaded = MicroTimer::now();
	}

	function areVarsFresh() {
		if ($this->time_last_mofified > $this->time_last_loaded) {
			return false;
		} else {
			return true;
		}
	}

	function destroyObject() {
		global $multi_prefix;
		//destroy this object
		$destroy = mysql_query("DELETE FROM {$multi_prefix}$this->db_table WHERE $this->key_name = '$this->key_value'");
	}
}

class Settings extends DbTable
{
	function Settings($id = 1) {
		$db_table = "settings";
		$key_name = "site_id";
		$key_value = $id;
		$this->DbTable($db_table, $key_name, $key_value);
	}
}

class ErrorStack
{
	function ErrorStack() {
		$this->error_stack = array();
		$this->total_errors = 0;
	}

	function addToStack($error) {
		$this->total_errors++;
		$this->error_stack[$this->total_errors] = $error;
	}

	function getStack() {
		return $this->error_stack;
	}

	function getTotalErrors() {
		return $this->total_errors;
	}

	function stackIsEmpty() {
		if ($this->getTotalErrors() < 1) {
			return true;
		} else {
			return false;
		}
	}

	function getULList() {
		if (count($this->error_stack) > 0) {
			$html = '<ul>';
			foreach ($this->error_stack as $error_msg) {
				$html .= '<li>'.$error_msg.'</li>';
			}
			$html .= '</ul>';
		}
		return $html;
	}

	function check($check_type, $data, $meta_data = "", $min_chars = 0) {
		switch ($check_type) {
			case "blank":
				if (trim($data) == "") {
					$this->addToStack($meta_data." cannot be left blank.");
				}
				break;

			case "email":
				if (!ereg("^.+@.+\..+", $data)) {
					$this->addToStack($meta_data." must be in the form <i>you@somesite.com</i>.");
				}
				break;

			case "min_chars":
				for ($i = 1; $i <= $min_chars; $i++) {
					$char_goal .= ".";
				}
				$char_goal .= "?";

				if (!ereg($char_goal, $data)) {
					$this->addToStack($meta_data." must be at least ".$min_chars." characters in length.");
				}
				break;
		}
	}
}

class MicroTimer
{
	function now() {
		//returns the current micro time without the space seperation
		$data = explode(" ", microtime());
		return $data[1] + $data[0];
	}

	//use the following three functions for running time tests
	function start() {
		$data = explode(" ", microtime());
		$this->start_time = $data[1] + $data[0];
	}
	function stop() {
		$data = explode(" ", microtime());
		$this->end_time = $data[1] + $data[0];
	}
	function getTotalTime() {
		return $this->end_time - $this->start_time;
	}
}
?>
