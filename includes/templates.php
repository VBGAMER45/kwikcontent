<?php
/***************************************************************************
 *
 *   File                 : admin/templates.php
 *   Software             : Kwikcontent
 *   Version              : 1.1
 *   Release Date         : October 27, 2009
 *   Copyright            : (C) 2009 Samson Software
 *   Contact              : http://www.kwikcontent.com/
 *   License              : Limited to one owner.  My not be reproduced or
 *                          redistributed under any circumstance.
 *
 ***************************************************************************/

include_once("../includes/_global.php");

// Copy files
copyr( BASE_DIRECTORY."templates/yahoo_grids_1/", BASE_DIRECTORY."templates/custom/". $multiCheck."/");


//page definitions
define("WINDOW_TITLE", "Template Manager");
define("PAGE_TITLE", "Template Manager");
?>
<?
include("_header.php");
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table">
	<tr>
		<td class="tableHeading">Template Manager</td>
		</tr>

	<tr>
		<td align="center" class="tableColumnHeader">Template Name</td>
	</tr>
	<?php
	
	      $dir = dir(BASE_DIRECTORY."templates/custom/". $multiCheck."/");
      while (false !== $entry = $dir->read())
      {
		  // Skip pointers
		  if ($entry == '.' || $entry == '..')
		  {
		  continue;
		  }
		  
		  if (substr_count($entry,".htm") > 0)
		  {

		echo '
		<tr>
			<td class="tableRowRightd" align="center"><a href="edit_template.php?t=' . ($entry) . '">' . $entry . '</a>&nbsp;</td>
			
			
		</tr>';
		  }
	
      }
	
	
?>

</table>

<?
function copyr($source, $dest)
{

	// Simple copy for a file

	if(is_file($source))
	{
		$c = copy($source, $dest);
		chmod($dest, 0777);
		return $c;
	}

      // Make destination directory
      if (!is_dir($dest))
      {
		  $oldumask = umask(0);
		  mkdir($dest, 0777);
		  umask($oldumask);

      }

      // Loop through the folder
      $dir = dir($source);
      while (false !== $entry = $dir->read())
      {
		  // Skip pointers
		  if ($entry == '.' || $entry == '..')
		  {
		  continue;
		  }

		  // Deep copy directories
		  if ($dest !== "$source/$entry")
		  {
			copyr("$source/$entry", "$dest/$entry");
		  }

      }
      // Clean up
      $dir->close();
      return true;
}




include("_footer.php");
?>
