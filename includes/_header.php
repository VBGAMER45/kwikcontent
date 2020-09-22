<?
//check for a proper Yahoo! App ID
if ($domain->yahoo_app_id == '') {
	$yahoo_app_id_alert = 'You need to store your Yahoo! Application ID in order for your kwikcontent software to work properly.  <a href="http://search.yahooapis.com/webservices/register_application" target="_blank">Get one here</a>.';
}
if ($domain->yahoo_app_id == 'YahooDemo') {
	$yahoo_app_id_alert = 'Your software is currently using Yahoo\'s demo application ID.  Although this will allow you to update your content, you will need to store your own Yahoo! Application ID in order for your kwikcontent software to work properly.  <a href="http://search.yahooapis.com/webservices/register_application" target="_blank">Get one here</a>.';
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><? echo $domain->domain_title ?> ADMIN :: <? echo WINDOW_TITLE ?></title>
<script type="text/javascript">
function confirmIt(text) {
	var certain = confirm(text);
	if (certain) {
		return true;
	}
	else return false;
}
function confirmWithPass(text1) {
	var test_pass = prompt(text1, "");
	var my_pass = "<? echo ADMIN_CONFIRM_PASS ?>";
	
	if (test_pass != null) {
		if (test_pass != my_pass) {
			alert("You have entered an invalid password.  Action Cancelled.");
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}
function expandColapse(div_id) {
	var div = document.getElementById(div_id);
	if (div.className == "hide") {
		div.className = "unhide";
	} else {
		div.className = "hide";
	}
}
</script>
<link href="_style.css" rel="stylesheet" type="text/css" />
</head>

<body>

<? if ($yahoo_app_id_alert) { ?>	
<div style="padding: 5px; background-color: #ffccff"><? echo $yahoo_app_id_alert ?></div>
<? } ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background:url(images/admin_header_thin_bg.gif); background-repeat:repeat-x">
	<tr>
		<td>


<table width="780" border="0" align="center" cellspacing="0" cellpadding="0">
	<tr>
		<td height="68" valign="bottom" style="background:url(images/admin_logo_thin.gif); background-repeat:no-repeat">&nbsp;</td>
	</tr>
	<tr>
		<td style="">
		<div class="headerNavigation">
			<a href="index.php">Home</a>
			<a href="settings.php">Settings</a>
			<a href="keywords.php">Keywords</a>
			<a href="articles.php">Articles</a>
			<a href="authors.php">Authors</a>
			<a href="static_pages.php">Static Pages</a>
			<a href="inserts.php">Inserts</a>
			<a href="deletes.php">Deletes</a>
			<!--<a href="templates.php">Templates</a>-->
			<a href="manage_sites.php">Manage Sites</a>
			<a href="http://www.kwikcontent.com/help.php" target="_blank">Help</a>
		</div></td>
	</tr>
	<tr>
		<td height="400" valign="top" style="padding-top: 20px; padding-bottom: 30px">
		<?
		if (trim($alert['text']) != "") {
			echo '<div class="alertBox" style="background-color: ';
			switch ($alert['type']) {
				case "bad":
					echo '#ffffcc"><img src="../site_icons/24/warning.gif" width="24" height="24" hspace="4" border="0" align="absmiddle"> ';
					break;
				
				case "good":
					echo '#ffeeff"><img src="../site_icons/24/information.gif" width="24" height="24" hspace="4" border="0" align="absmiddle"> ';
					break;
				
				default:
					echo '#ffffff"><img src="../site_icons/24/information.gif" width="24" height="24" hspace="4" border="0" align="absmiddle"> ';
					break;
			}
			echo $alert['text'];
			echo '</div>';
		}
		?>
<!-- END HEADER -->
