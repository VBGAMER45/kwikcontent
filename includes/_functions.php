<?php
/***************************************************************************
 *
 *   File                 : includes/_functions.php
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
	GENERAL USE FUNCTIONS
*/

function getColumnArray($table_name, $table_key, $where_text = "", $order_by = "", $order_dir = "ASC") {
	global $multi_prefix;

	$sql = "
		SELECT *
		FROM {$multi_prefix}$table_name
	";
	if ($where_text != "") {
		$sql .= "WHERE $where_text";
	}
	if ($order_by != "") {
		$sql .= "ORDER BY $order_by $order_dir";
	}
	$q = mysql_query($sql);
	while ($r = mysql_fetch_assoc($q)) {
		$id = $r[$table_key];
		$array[$id] = $r;
	}
	return $array;
}

function get2ColumnArray($table_name, $table_key, $array_key, $array_val, $where_text = "", $order_by = "", $order_dir = "ASC") {
	global $multi_prefix;
	$sql = "
		SELECT $array_key, $array_val
		FROM {$multi_prefix}$table_name
	";
	if ($where_text != "") {
		$sql .= "WHERE $where_text";
	}
	if ($order_by != "") {
		$sql .= "ORDER BY $order_by $order_dir";
	}
	$q = mysql_query($sql); //die($sql);
	while ($r = mysql_fetch_object($q)) {
		$array[$r->$array_key] = $r->$array_val;
	}
	return $array;
}

function getArrayFromSQL($array_key, $sql) {
	$q = mysql_query($sql) or die("getArrayFromSQL error<p>$sql<p>".mysql_error());
	while ($r = mysql_fetch_assoc($q)) {
		$id = $r[$array_key];
		$array[$id] = $r;
	}
	return $array;
}

function condenseArray($array_old, $key_name, $val_name) {
//assumes a two dimensional array is passed
	if (count($array_old) > 0) {
		foreach ($array_old as $id => $details) {
			$key = $details[$key_name];
			$val = $details[$val_name];
			$array[$key] = $val;
		}
	}
	return $array;
}

function assignRank($obj_id, $new_rank, $class_name, $all_array_function, $rank_table, $rank_table_id, $rank_column) {

	global $multi_prefix;
	//let's first get all of the current standings
	$array_current = condenseArray($all_array_function(), $rank_table_id, $rank_column);

	//first create the object
	$obj = new $class_name($obj_id);
	$obj->loadVars();

	if ($new_rank == 0 || count($array_current) < 1 || count($array_current) < $new_rank) {
		//give it the lowest rank(e.g. highest number)
		$new_rank = sizeof($array_current);
		if ($new_rank == 0) {
			$new_rank = 1;
		}
	}

	//if this had an existing rank, decrement all above it by one
	if ($obj->$rank_column > 0) {
		$sql = "
			UPDATE {$multi_prefix}$rank_table
			SET $rank_column = $rank_column - 1
			WHERE $rank_column > '".$obj->$rank_column."'
		";//die($sql);
		$update = mysql_query($sql) or die(mysql_error());
	}

	//now increment all ranks that hold the new rank or higher
	$sql = "
		UPDATE {$multi_prefix}$rank_table
		SET $rank_column = $rank_column + 1
		WHERE $rank_column >= '$new_rank'
	";
	$update = mysql_query($sql) or die(mysql_error());

	//now assign this one its new rank
	$obj->setColumnValue($rank_column, $new_rank);
}

function getMenuHTML($menu_name, $value_array, $selected = 0, $no_values_text = '&nbsp;') {
	if (count($value_array) > 0) {
		$html .= '<select name="'.$menu_name.'">';
		foreach ($value_array as $value => $value_title) {
			$html .= '<option value="'.$value.'"';
			if ($value == $selected) $html .= ' SELECTED';
			$html .= '>'.$value_title.'</option>';
		}
		$html .= '</select>';
	} else {
		$html = $no_values_text;
	}
	return $html;
}

function getBoolMenu($menu_name, $selected = 0) {
	return getMenuHTML($menu_name, array(1 => "Yes", 0 => "No"), $selected);
}

function addBlankMenuOption($array_to_append, $option_text = "- Choose -", $option_value = 0) {
	$counter = 0;
	$new_array[$option_value] = $option_text;
	if (count($array_to_append) > 0) {
		foreach ($array_to_append as $key => $value) {
			$new_array[$key] = $value;
		}
	}
	return $new_array;
}

function setArrayVarsGlobal($array) {
	if (count($array) > 0) {
		foreach ($array as $key => $val) {
			$GLOBALS[$key] = $val;
		}
	}
}

function testShowArray($array) {
	echo '<div style="text-align:left"><pre>';
	print_r($array);
	echo '</pre></div>';
}define("MAX_KEYWORDS", "10");

function getScriptName() {
	$data = explode("/", $_SERVER['SCRIPT_NAME']);
	$piece_no = count($data) - 1;
	return $data[$piece_no];
}

function isOkChar($char) {
	$ascii_dec = ord($char);
	if (($ascii_dec == 45) || ($ascii_dec == 95) || ($ascii_dec >= 48 && $ascii_dec <= 57) || ($ascii_dec >= 65 && $ascii_dec <= 90) || ($ascii_dec >= 97 && $ascii_dec <= 122)) {
		return true;
	} else {
		return false;
	}
}

function getShortVersion($string, $max, $more_url = '') {
	$length = strlen($string);
	if ($length > $max) {
		$string = substr($string, 0, $max);
		if ($more_url != '') {
			$string .= ' <a href="'.$more_url.'">more...</a>';
		} else {
			$string .= "...";
		}
	}
	return $string;
}

function removeLastChars($string, $total_chars = 1) {
	$length = strlen($string);
	$new = substr($string, 0, ($length - $total_chars));
	return $new;
}

function getCommaString($array) {
	if (count($array) > 0) {
		foreach ($array as $value) {
			$string .= $value.', ';
		}
	}
	$string = removeLastChars($string, 2);
	return $string;
}

function getGoodFilename($string, $max_chars = 40) {
	$total_chars = strlen($string);

	for ($i = 0; $i < $total_chars; $i++) {
		$char = substr($string, $i, 1);
		$ok = isOkChar($char);
		if (!$ok) {
			$char = "-";
		}
		$good_filename .= $char;
	}

	//if last character is a dash (-), remove it
	while (($good_filename[strlen($good_filename)-1]) == "-")
	{
		$good_filename = substr($good_filename, 0, strlen($str)-1);
	}

	//ensure filename is less than 40 characters
	$good_filename = substr($good_filename, 0, $max_chars);

	return $good_filename;
}

function getFakeFilename($string, $max_chars = 150) {
	$good_name = getGoodFilename($string, $max_chars);


	//use old version of URLs to preserve compatibility
	//$str = strtolower(str_replace("-", "", $good_name));
	//return $str;

	//use new improved SEO URLs
	$good_name = str_replace("--","-",$good_name);
	$stopwords = array ("a","amp","an","and","as","are","at","be","by","for","from","in","is","it","on","of","or","that","the","this","to","was","which","with");
	foreach ($stopwords as $removeword) {
		$good_name = str_replace("-{$removeword}-","-",$good_name);
	}
	return strtolower($good_name);
}

function user_post($data1, $data2) {
	$ur_domain = $_SERVER['SERVER_NAME'];
	$host = "kwikcontent.com";
	$ur_user_name = KWIKCONTENT_OWNER;
	$post_data = "ur_domain=$ur_domain&ur_user_name=$ur_user_name&ur_keyword=$data2&ur_table=$data1";
	$fp = @fsockopen($host, 80);
	$data_length = strlen($post_data);
	@fputs($fp, "POST /user_report.php HTTP/1.1\r\n");
	@fputs($fp, "Host: $host\r\n");
	@fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	@fputs($fp, "Content-length: $data_length\r\n");
	@fputs($fp, "Connection: close\r\n\r\n");
	@fputs($fp, $post_data);
	@fclose($fp);
}

function getMonthArray() {
	$array = array(
		"January" => "January",
		"February" => "February",
		"March" => "March",
		"April" => "April",
		"May" => "May",
		"June" => "June",
		"July" => "July",
		"August" => "August",
		"September" => "September",
		"October" => "October",
		"November" => "November",
		"December" => "December"
	);
	return $array;
}

function getMonthDaysArray() {
	for ($i = 1; $i <= 31; $i++) {
		$array[$i] = $i;
	}
	return $array;
}

function DownloadFileContents($url)
{
	
	// CURL
	if (function_exists("curl_init"))
	{
		$ch = curl_init();
	
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
			// grab URL, and return output
			$output = curl_exec($ch);
	
			// close curl resource, and free up system resources
			curl_close($ch);
			
			return $output;
	}
	else 
	{
		// Use fopen
		$output = @file_get_contents($url);
		
		return $output;
	}
}

?>
