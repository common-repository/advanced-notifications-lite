<?php

function advnote_settings_javascript()
	{
		?>
			
			<!-- Support for the delete button , we use Javascript here -->
			<script type="text/javascript">
			

			</script>
		<?php
	}

function advnote_settings()
{
	global $global_advnote;
	global $global_advnote_handle;
	global $global_api_location;
	global $wordpress_url;
	advnote_license_settings();
	
	?>
	
	<form action='?post_type=notifications&page=advanced-notifications-lite/advanced-notifications.php' id='id_form_activate_advnote' method='post'>
	<input type='hidden' name='nature' value='activate_wptraffictools'>
	<center>
	<br><br>
	<table>
		<tr>
			<td  align=left style="font-size:13px;">
				License Key:
			</td>
			<td  align=left style="font-size:13px;">
				<input name=license_key size=30 value='<?php echo $global_advnote; ?>'>
			</td>
		</tr> 
		<tr>
			<td  align=left style="font-size:13px;">
				License Email:
			</td>
			<td  align=left style="font-size:13px;">
				<input name=license_email size=30 value='<?php echo $global_advnote_handle; ?>'><br>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<br>
				<center>
					<div style='height:27px;'>
					<?php
					if (!$global_advnote)
					{
					?>
					<input class='button-primary' type='submit' name='Save' value='Activate Advanced Notifications Pro!' id='submitbutton' />
					<?php
					}
					else
					{
					?>
					<input class='button-primary' type='submit' name='Save' value='Update Advanced Notifications Pro License Information!' id='submitbutton' />
					<?php
					}
					?>
					</div>
				</center>
				<br>
				<center>
					<div style='height:90px;font-size:11px;'>Don't have a license key yet? <a href='http://www.hatnohat.com/members/advanced-notifications-pro/' target='_blank' >Get Pro Version Now!</a></div>
				</center>
			</td>
		</tr>
	</table>
	</center>
	</form>
	<?php	
}


function advnote_license_settings()
{
	global $table_prefix;
	global $advnote_options; 
	global $wordpress_url; 
	global $wpdb; 
	

	if (isset($_POST['license_key']))
	{
		$global_advnote = $_POST['license_key'];
		$advnote_options['license_key'] = $_POST['license_key'];
		$global_advnote_handle = $_POST['license_email'];	
		$advnote_options['license_email'] = $_POST['license_email'];	
		
		
		$parse = parse_url($wordpress_url);
		$domain = $parse['host'];
		$url = "http://www.hatnohat.com/api/advanced-notifications/validate.php?key={$advnote_options['license_key']}&email={$advnote_options['license_email']}&domain={$domain}";
		$return = advnote_remote_connect($url);
		
		if ($return=='1')
		{
			$advnote_options['permission'] =1;
			echo "<br><br><br><center><font color='green'>Congratulations! Your license information is valid!<br>
			<a href='edit.php?post_type=notifications'>Let's Go!</a></font></center> "; 
			$advnote_options = json_encode($advnote_options);
			$sql = "UPDATE {$wpdb->prefix}advnote_options SET option_value='$advnote_options' WHERE option_name='advnote_options'";
			$result = $wpdb->get_results($sql, ARRAY_A);exit;
			
			
		}
		else
		{
			echo "<br><br><br><center><font color='red'>Your license information is invalid. Please try again.<br>";
			$advnote_options['permission'] = 0;
			$advnote_options = json_encode($advnote_options);
			$sql = "UPDATE {$wpdb->prefix}advnote_options SET option_value='$advnote_options' WHERE option_name='advnote_options'";
			$result = $wpdb->get_results($sql, ARRAY_A);
		}
		
	}

}

?>