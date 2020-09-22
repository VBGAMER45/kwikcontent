<?php
/***************************************************************************
 *
 *   File                 : admin/lib.sitesetup.php
 *   Software             : Kwikcontent
 *   Version              : 1.1
 *   Release Date         : August 29, 2009
 *   Copyright            : (C) 2009 Samson Software
 *   Contact              : http://www.kwikcontent.com/
 *   License              : Limited to one owner.  My not be reproduced or
 *                          redistributed under any circumstance.
 *
 ***************************************************************************/

function SetupSite($databasePrefix = '', $domainName = '', $domainTitle = '', $yahoo_app_id = '', $bing_app_id = '', $google_pub_id = '', $google_analytics_code = '')
{
	// Check if domain is already setup if so do nothing
	$config_file = '../settings/'.$domainName.'.php';
	if (is_file($config_file))
	{
		die("Site $domainName already exists!");
		return;
	}
	
	// Generate a prefix if none was passed
	if (empty($databasePrefix))
	{
		$databasePrefix = $domainName;
		
		if (strlen($databasePrefix) > 32)
			$databasePrefix = substr($databasePrefix,0,32);
	}
	
	$databasePrefix = str_replace(".","",$databasePrefix);
	$domainName = str_replace('http://','',$domainName);
	$domainName = str_replace('www.','',$domainName);

	$current_time_stamp = mktime();
	
	$prefix = $databasePrefix."_";

	$fullurl = 'http://www.' . $domainName;

	$baseurl = $domainName;
	
	
	// Write the settings file
				$filename = 'default_settings.php';
				$handle = fopen ($filename, "r");
				$contents = fread ($handle, filesize ($filename));
				fclose ($handle);
				$contents = str_replace("%prefix%", $prefix, $contents);
				$contents = str_replace("%site%", $domainName, $contents);
				$contents = str_replace("%base%", $baseurl, $contents);
				$contents = str_replace("%fullurl%", $fullurl, $contents);
				$filename = "../settings/".$domainName.'.php';


				if (!$handle = fopen($filename, 'w'))
				{
					echo 'Cannot create settings file "settings" folder is not writable';
					exit;
				}

				if (!fwrite($handle, $contents))
				{
					echo 'Can not write to file "' . $domainName . '.php"';
					exit;
				}

				fclose($handle);
	

	$filename = 'db.sql';

	if (!$fp = fopen($filename,'rb'))
	{
		die("Unable to open the sql file: " . $filename);
	}


	$buffer = '';
	$inside_quote = 0;
	$quote_inside = '';
	$started_query = 0;

	$data_buffer = '';

	$last_char = "\n";


				while ((!feof($fp) || strlen($buffer)))
				{
					do
					{
						
						if (!strlen($buffer))
						{
							$buffer .= fread ($fp,1024);
						}

						$current_char = $buffer[0];
						$buffer = substr($buffer, 1);

						if ($started_query)
						{
							$data_buffer .= $current_char;
						}
						elseif (preg_match("/[A-Za-z]/i",$current_char) && $last_char == "\n")
						{
							$started_query = 1;
							$data_buffer = $current_char;
						}
						else
						{
							$last_char = $current_char;
						}
					} while (!$started_query && (!feof($fp) || strlen($buffer)));


					if ($inside_quote && $current_char == $quote_inside && $last_char != '\\')
					{

						$inside_quote = 0;
					}
					elseif ($current_char == '\\' && $last_char == '\\')
					{
						$current_char = '';
					}
					elseif (!$inside_quote && ($current_char == '"' || $current_char == '`' || $current_char == '\''))
					{
						
						$inside_quote = 1;
						$quote_inside = $current_char;
					}
					elseif (!$inside_quote && $current_char == ';')
					{

						$data_buffer = str_replace('%dbprefix%', $prefix, $data_buffer);
	        			$data_buffer = str_replace('%domaintitle%', $domainName, $data_buffer);
	        
						mysql_query($data_buffer);

						if (mysql_errno())
						{
							die("Database error " . mysql_error());
						}


						$data_buffer = '';
						$last_char = "\n";
						$started_query = 0;
					}

					$last_char = $current_char;
				}

				fclose($fp);



			// Create the cache folder
			mkdir("../cache/$domainName/");
			@chmod("../cache/$domainName/", 0777);
				
			// Create smarty templates cache folder
			mkdir("../templates_c/$domainName/");
			@chmod("../templates_c/$domainName/", 0777);

			$sql_domainname = addslashes($domainName);
			$sql_dateadded = addslashes($current_time_stamp);

			$insertSql = "INSERT INTO sites (domain, date) VALUES ('$sql_domainname', '$sql_dateadded')";
			
			mysql_query($insertSql);
			
			
			// Insert the final domain settings
			mysql_query("
INSERT INTO `{$prefix}domain` 
(domain_id, domain_name, domain_title, domain_window_titles, domain_home_text,
domain_home_meta_desc,hide_public_errors,yahoo_app_id,yahoo_auto_update,yahoo_auto_update_frequency,
yahoo_news_update_frequency,yahoo_news_display_total,admin_wysiwyg,preview_articles, preview_y_images,
preview_y_qas,preview_y_news,google_search,google_analytics,google_ad_client,google_ad_channel,amazon_id,
amazon_keyword,ebay_id,ebay_keyword,forum_url,forum_blank,region,bing_app_id,linkadge_key)

VALUES('1', '{$domainTitle}', '', '1', '', '', '0', '{$yahoo_app_id}', '1', 7, 4, 50, '1', 3, 5, 6, 5, '1', '{$google_analytics_code}', '{$google_pub_id}', '', '', '', '', '', '', '', 'us','{$bing_app_id}','');
");
			
}

?>