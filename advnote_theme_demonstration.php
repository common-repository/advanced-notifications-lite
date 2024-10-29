<?php
include_once('../../../wp-config.php');
define('ADVANCEDNOTIFICATIONS_PATH', ABSPATH.'wp-content/plugins/advanced-notifications/' );
$theme_path =ADVANCEDNOTIFICATIONS_PATH."/themes/" ; 
define('ADVANCEDNOTIFICATIONS_THEMESURLPATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ).'/themes/' );
$results = scandir($theme_path);

foreach ($results as $result) {
	if ($result === '.' or $result === '..') continue;

	if (is_dir($theme_path . '/' . $result)) {
		$styles[] = $result;
	}
}
		
		//print_r($styles);
?>
<html>
<head>
<title></title>

<link type="text/css" rel="stylesheet" href="<?php echo ADVANCEDNOTIFICATIONS_THEMESURLPATH ?>basic-grey/jquery.ui.all.css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js" type="text/javascript">
</script>

<script type='text/javascript'>
	$(document).ready(function () {

		jQuery("#advnote_theme").live("change", function(){	
			 var input = jQuery(this).val();
			 var url = "<?php echo ADVANCEDNOTIFICATIONS_THEMESURLPATH ?>"+input+"/jquery.ui.all.css";
			 $("link").attr("href",url);
			//$.cookie("css",$(this).attr('rel'), {expires: 365, path: '/'});
			return false;
		});

		//Show date time picker control
		$(function () {
			$("#datepicker").datepicker();
		});
 
		//Show Dialog
		$(function () {
			$("#dialog").dialog({
				width:900,
				height: 90,
				resizable: false,
				closable: false
			});
		});
 
		//Progress Bar
		$(function () {
			$("#progressbar").progressbar({
				value: 50
			});
		});
	});
	
	
</script>

</head>
<body>
<center>

<a href='http://jqueryui.com/themeroller/' target='_blank' >Click Here to Create Your Own Theme!</a><br>
<small><em>When importing custom themese change your main css file to look like this: jquery-ui-1.8.23.custom.css -> jquery-ui.custom.css</em></small>
<br>

<table>
						<tr>
							<td valign=top style='width:225px'>
								Select Theme to Preview:
							</td>
						</tr>
						<tr>
							<td valign='top'>
								<select name='advnote_theme' id='advnote_theme'>
									<?php 						
									foreach ($styles as $style)
									{
										?>																
										<option  value='<?php echo $style; ?>' <?php if ($advnote_style=="$style"){ echo "selected='selected'"; } ?>> &nbsp;<?php echo $style; ?></option>
										<?php
									}
								?>
								</select>
							</td>
						</tr>
</table>

<div id="dialog" title="Announcement Dialog" style='font-size:11px;'>
    <p>
    This is a Demo Dialog box using JQuery!!    Change theme to see this Dialog in different theme.
    </p>
</div>

	
</center>
</body>
</html>