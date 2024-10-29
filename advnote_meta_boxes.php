<?php

	
	add_action( 'add_meta_boxes', 'advnote_add_meta_box_userrole' );
	add_action( 'add_meta_boxes', 'advnote_add_meta_box_styling' );
	add_action( 'add_meta_boxes', 'advnote_add_meta_box_tips' );
	add_action( 'add_meta_boxes', 'advnote_add_meta_box_proadvert' );	

	add_action( 'save_post', 'advnote_save_postdata' );
	
	/* Adds a box to the main column on the Post and Page edit screens */
	function advnote_add_meta_box_userrole() {
		add_meta_box( 
		'advanced-notifications-userrole', 
		__( 'Notification Placement Options', 'advnote_meta_title_userrole' ),
		'advnote_meta_box_userrole',
		'notifications' , 
		'normal', 
		'high' );
		
	}
 
	/* Adds a box to the main column on the Post and Page edit screens */
	function advnote_add_meta_box_styling() {
		add_meta_box( 
		'advanced-notifications-styling', 
		__( 'Notification Styling Options', 'advnote_meta_title_styling' ),
		'advnote_meta_box_styling',
		'notifications' , 
		'normal', 
		'high' );		
	}

	/* Adds a box to the main column on the Post and Page edit screens */
	function advnote_add_meta_box_tips() {
		add_meta_box( 
		'advanced-notifications-tips', 
		__( 'Notification Tips & Tricks', 'advnote_meta_title_tips' ),
		'advnote_meta_box_tips',
		'notifications' , 
		'normal', 
		'high' );		
	}
	
	/* Adds a box to the main column on the Post and Page edit screens */
	function advnote_add_meta_box_proadvert() {
		global 	$global_permission;		
		if ($global_permission!=1)
		{
			//echo 1; exit;
			//echo 1; exit;
			add_meta_box( 
			'advanced-notifications-advert', 
			__( 'Buy Advanced Notifications Pro!', 'advnote_meta_title_advert' ),
			'advnote_meta_box_advert',
			'notifications' , 
			'side', 
			'low' );
		}
	}


	function advnote_meta_box_userrole()
	{
		global $post;
		global $table_prefix;
		global $global_permission;
		$post_id = $post->ID;
		//echo $post_id;exit;
		$roles = get_editable_roles();
		$post_types = get_post_types('','names');
		//print_r($roles);
		
		$query = "SELECT * FROM {$table_prefix}advnote_notifications WHERE post_id='{$post_id}'";
		$result = mysql_query($query);
		if (!$result) { echo $query;  echo mysql_error(); exit;}
		$arr = mysql_fetch_array($result);
		$advnote_status = $arr['status'];
		$rules = $arr['rules'];	
		$rules = json_decode($rules,1);
		//print_r($rules);
		$advnote_expire_by = $rules['expire_by'];
		$advnote_expire_by_date = $rules['expire_by_date'];
		$advnote_expire_by_visitors = $rules['expire_by_visitors'];
		$advnote_expire_by_visitors_count = $rules['expire_by_visitors_count'];
		$advnote_userroles_permit = explode(',',$rules['userroles_permit']);
		$advnote_userroles_prevent = explode(',',$rules['userroles_prevent']);
		$advnote_post_types = explode(',',$rules['post_types']);
		$advnote_persistance = $rules['persistance'];
		$advnote_persistance_reset = $rules['persistance_reset']; 
		$advnote_persistance_reset_rule = $rules['persistance_reset_rule'];
		$advnote_useragent_prevent = $rules['useragent_prevent'];
		
		if (!$global_permission)
		{
			$label = "(pro version only)";
			$mod = true; 
		}
		?>
		<div class=" " >
			<div class="inside">
					<table>
						<tr>
							<td valign=top style='width:205px'>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Please seperate keywords with commas. How this works: If the the url of the page that referrered the traffic contains any of these keywords then the visitor will be redirected. Otherwise no redirection will take place unless a * wildcard is placed into this field."> 	
									Is Active?
							</td>
							<td valign='top'>
								<input type='radio' name='advnote_status' value='1' <?php if ($advnote_status==1){echo "checked"; }?>> &nbsp; Yes &nbsp;&nbsp;
								<input type='radio' name='advnote_status' value='0'  <?php if ($advnote_status==0||!$advnote_status){echo "checked"; }?>> &nbsp; No &nbsp;&nbsp;
								<br><br>
							</td>
						</tr>
						<tr>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Please seperate keywords with commas. How this works: If the the url of the page that referrered the traffic contains any of these keywords then the visitor will be redirected. Otherwise no redirection will take place unless a * wildcard is placed into this field."> 	
									Expires:
							</td>
							<td valign='top'>
								<select name= 'advnote_expire_by' id='advnote_expire_by' >							
									<option value = 'eternal' <?php if ($advnote_expire_by=='eternal'||!$advnote_expire_by){ echo "selected='selected'";} ?>>Never Expires</option>
									<option value = 'date' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_expire_by=='date'){ echo "selected='selected'";} ?>>By Date</option>
									<option value = 'visitors' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_expire_by=='visitors'){ echo "selected='selected'";} ?>>By X Visitors</option>
								</select>
								<em><small><?php echo $label; ?></small></em>
								<br><br>
							</td>
						</tr>
						<tr class='advnote_expire_by_date' <?php if ($advnote_expire_by!='date'){ echo "style='display:none;'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Please seperate keywords with commas. How this works: If the the url of the page that referrered the traffic contains any of these keywords then the visitor will be redirected. Otherwise no redirection will take place unless a * wildcard is placed into this field."> 	
									Notice Expiration Date:
							</td>
							<td valign='top'>
								<input type="text" name='advnote_expire_by_date' id="datepicker" class="datepicker"  value='<?php echo $advnote_expire_by_date; ?>'>
								<br><br>
							</td>
						</tr>
						<tr class='advnote_expire_by_visitor' <?php if ($advnote_expire_by!='visitors'){ echo "style='display:none;'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Place the number of unique visitors it will take to shut off/expire this notification permantly."> 	
									Expiration Goal:
							</td>
							<td valign='top'>
								<input type="hidden" name='advnote_expire_by_visitors_count' size='1' value='<?php if ($advnote_expire_by_visitors_count){ echo $advnote_expire_by_visitors_count;} else { echo "0"; }  ?>'>
								<input type="text" name='advnote_expire_by_visitors' size='1' value='<?php echo $advnote_expire_by_visitors; ?>'> <em><small>( Eg: Enter 200 to disable notification after the 200th visitor. )</small></em>
								<br><br>
							</td>
						</tr>
						<tr>
							<td valign='top'>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH; ?>images/tip.png" style="cursor:pointer;" border=0 title="Selecting 'unregistered' here will apply notifications to ALL userroles because all userroles above 'unregistered' encompass the privledges of the unregistered userrole. Don't overthink it! Just remember to add aexemptions if you want to prevent displaying notifications to users with higher authorities. "> 
									Apply to these user Roles:
							</td>
							<td  valign='top'>
						<?php 						
							foreach ($roles as $key=>$role)
							{
								
								
								?>
								
								<input type='checkbox'  name='advnote_userroles_permit[]' value='<?php echo $key; ?>' <?php if (in_array($key,$advnote_userroles_permit)){ echo "checked"; } ?>> &nbsp;<?php echo $key; ?><br>
								<?php							
							}
							?>
							<input type='checkbox'  name='advnote_userroles_permit[]' value='unregistered'  <?php if (in_array('unregistered',$advnote_userroles_permit)||!$advnote_userroles_permit){ echo "checked"; } ?>> &nbsp;unregistered&nbsp;&nbsp;	
							<br><br>
							</td>
						</tr>
						<tr>
							<td valign='top'>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH; ?>images/tip.png" style="cursor:pointer;" border=0 title="It's important to exempt even if the userrole is not selected above. If only 'unregistered' is selected above administrators will still be shown the notice because an administrator absorbs all the privledges of the unregistered userrole. Selecting userroles here will prevent notifications from displaying."> 
									Exempt from these user Roles:
							</td>
							<td valign='top'>
								<?php 						
								foreach ($roles as $key=>$role)
								{
									
									
									?>
									
									<input type='checkbox'  name='advnote_userroles_prevent[]' value='<?php echo $key; ?>' <?php if (in_array('unregistered',$advnote_userroles_prevent)){ echo "checked"; } ?>> &nbsp;<?php echo $key; ?><br>						
									
									<?php							
								}
								?>
								<input type='checkbox'  name='advnote_userroles_prevent[]' value='unregistered'  <?php if (in_array($key,$advnote_userroles_prevent)||!$advnote_userroles_prevent){ echo "checked'"; } ?>> &nbsp;unregistered&nbsp;&nbsp;	
								<br><br>
							</td>
						</tr>
						<tr>
							<td valign='top'>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH; ?>images/tip.png" style="cursor:pointer;" border=0 title="Redirect URL here."> 
									Apply to these Page Types:
							</td>
							<td  valign='top'>
							<?php 						
							foreach ($post_types as $key=>$post_type)
							{
								if ($post_type!='revision'&&$post_type!='attachment'&&$post_type!='nav_menu_item'&&$post_type!='notifications')
								{
								?>								
								<input type='checkbox'  name='advnote_post_types[]' value='<?php echo $post_type; ?>' <?php if (in_array($post_type,$advnote_post_types)){ echo "checked"; } ?>> &nbsp;<?php echo $post_type; ?><br>
								<?php	
								}								
							}
							?>
							<input type='checkbox'  name='advnote_post_types[]' value='home'  <?php if (in_array('home',$advnote_post_types)||!$advnote_post_type){ echo "checked"; } ?>> &nbsp;home page<br>
							<input type='checkbox'  name='advnote_post_types[]' value='archive'  <?php if (in_array('archive',$advnote_post_types)){ echo "checked"; } ?>> &nbsp;archive (tag or cat)&nbsp;&nbsp;	
							<br><br>
							</td>
						</tr>
						<tr>
							<td valign=top>
								<label for=keyword>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Persistant = Displays until closed during every new browser session. Not persistant = Displays until closed and then will not show again for the lifetime of the notification. We use the visitor's IP addresses for tracking who has closed the notification and who hasn't. "> 	
									Notification Persistance
								</label>
							</td>
							<td>
								<input type='radio'  id='advnote_persistance'  name='advnote_persistance' value='persistant'  <?php if ($advnote_persistance=='persistant'||!$advnote_persistance){echo "checked";} ?>> &nbsp; Persistant &nbsp;&nbsp;
								<input type='radio' id='advnote_persistance' name='advnote_persistance' value='once'  <?php if ($advnote_persistance=='once'){echo "checked";} ?>> &nbsp; Not Persistant &nbsp;&nbsp;
								<br><br>
							</td>
						</tr>
						<tr class='advnote_persistance' <?php if ($advnote_persistance!='once') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<label for=keyword>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="If notification is set to not persistant, how long should we wait until we show the visitor the notification again? In other words, what should trigger notification reset? We can choose to reset visitor protection after the visitor has viewed page X times or we can reset protection after X days has passed since the visitor first saw & closed the notification.  "> 	
									Reset Persistance Safety
								</label>
							</td>
							<td>
								<select name='advnote_persistance_reset' id='advnote_persistance_reset' 	>
									<option value='never' <?php if ($advnote_persistance_reset=='never'){echo "selected='selected'";} ?>> &nbsp; Never Reset For Visitor &nbsp;&nbsp;
									<option value='pageviews'  <?php if ($advnote_persistance_reset=='pageviews'){echo "selected='selected'";} ?>  <?php if ($mod==true) { echo "disabled='disabled'"; } ?>> &nbsp; Reset After X Visitor Page Views &nbsp;&nbsp;
									<option value='days'  <?php if ($advnote_persistance_reset=='days'){echo "selected='selected'";} ?>  <?php if ($mod==true) { echo "disabled='disabled'"; } ?>> &nbsp; Reset For Visitor After X Days&nbsp;&nbsp;
								</select>
								<em><small><?php echo $label; ?></small></em>
								<br><br>
							</td>
						</tr>
						<tr class='advnote_persistance_reset_rule' <?php if (($advnote_persistance!='once'&&$advnote_persistance_reset!='never')||!$advnote_persistance_reset_rule){ echo "style='display:none;'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Place the number of unique visitors it will take to shut off/expire this notification permantly."> 	
									Reset Rule
							</td>
							<td valign='top'>
								<input type="text" name='advnote_persistance_reset_rule' size='1' value='<?php echo $advnote_persistance_reset_rule; ?>'> <em><small>( Eg: Enter 10 to represent 10 page views or 10 days )</small></em>
								<br><br>
							</td>
						</tr>
						<tr >
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="List strings separated by commas that will tell the plugin not to fire the notification if user is using a browser containing a useragent containing this string. An example string would be 'iPhone' which would prevent the notification from firing on iphones."> 	
									Exempt from these useragents
							</td>
							<td valign='top'>
								<input type="text" name='advnote_useragent_prevent' size='25' value='<?php echo $advnote_useragent_prevent; ?>'> <em><small>( Separate with commas )</small></em>
								<br><br>
							</td>
						</tr>
						
				
				</table>
			</div>	
		</div>
		<?php
	}

	

	function advnote_meta_box_styling()
	{
		global $post;
		global $table_prefix;	
		global $global_permission;
		
		$post_id = $post->ID;
		
		$theme_path =ADVANCEDNOTIFICATIONS_PATH."/themes/" ; 
		$results = scandir($theme_path);

		foreach ($results as $result) {
			if ($result === '.' or $result === '..') continue;

			if (is_dir($theme_path . '/' . $result)) {
				$themes[] = $result;
			}
		}
		
		//print_r($themes);exit;
		
		$query = "SELECT styling FROM {$table_prefix}advnote_notifications WHERE post_id='{$post_id}'";
		$result = mysql_query($query);
		if (!$result) { echo $query;  echo mysql_error(); exit;}
		$arr = mysql_fetch_array($result);
		$styling = $arr['styling'];	
		$rules = json_decode($styling,1);
		//print_r($rules);
		$advnote_theme = $rules['theme'];
		$advnote_width = $rules['width'];
		$advnote_height = $rules['height'];
		$advnote_delay_nature = $rules['delay_nature'];
		$advnote_delay_delay = $rules['delay_delay'];
		$advnote_delay_scrollpoint_coordinate = $rules['delay_scrollpoint_coordinate'];
		$advnote_delay_scrollpoint_element = $rules['delay_scrollpoint_element'];
		$advnote_delay_scrollpoint_features = $rules['delay_scrollpoint_features'];
		$advnote_draggable = $rules['draggable'];
		$advnote_placement = $rules['placement'];
		$advnote_modal = $rules['modal'];
		$advnote_closable = $rules['closable'];
		$advnote_resizable = $rules['resizable'];
		$advnote_show_title = $rules['show_title'];
		$advnote_animate_show_hide = $rules['animate_show_hide'];
		$advnote_animate_show_effect = $rules['animate_show_effect'];
		$advnote_animate_hide_effect = $rules['animate_hide_effect'];
		$advnote_animate_show_effect_duration = $rules['animate_show_effect_duration'];
		$advnote_animate_hide_effect_duration = $rules['animate_hide_effect_duration'];
		$advnote_animate_show_effect_direction = $rules['animate_show_effect_direction'];
		$advnote_animate_hide_effect_direction = $rules['animate_hide_effect_direction'];
		$advnote_animate_show_effect_fold_method = $rules['animate_hide_effect_fold_method'];
		$advnote_animate_hide_effect_fold_method = $rules['animate_hide_effect_fold_method'];
		$advnote_animate_show_effect_size = $rules['animate_show_effect_size'];
		$advnote_animate_hide_effect_size = $rules['animate_hide_effect_size'];
		$advnote_animate_show_effect_origin = $rules['animate_show_effect_origin'];
		$advnote_animate_show_effect_scale_method = $rules['animate_show_effect_scale_method'];
		$advnote_animate_show_effect_direction = $rules['animate_show_effect_direction'];		
		$advnote_animate_hide_effect_origin = $rules['animate_hide_effect_origin'];
		$advnote_animate_hide_effect_scale_method = $rules['animate_hide_effect_scale_method'];
		$advnote_animate_hide_effect_direction = $rules['animate_hide_effect_direction'];
		$advnote_animate_after_effect = $rules['animate_after_effect'];
		$advnote_animate_after_effect_effect = $rules['animate_after_effect_effect'];
		$advnote_animate_after_effect_delay = $rules['animate_after_effect_delay'];
		$advnote_animate_after_effect_duration = $rules['animate_after_effect_duration'];
		$advnote_animate_after_effect_direction = $rules['animate_after_effect_direction'];
		$advnote_animate_after_effect_distance = $rules['animate_after_effect_distance'];
		
		if (!$global_permission)
		{
			$label = "(pro version only)";
			$mod = true; 
		}
		
		?>
		<div class=" " >
			<div class="inside">
					<table>						
						<tr>
							<td valign=top style='width:205px'>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Select where you want the notification to be presented."> 	
									Style Theme
							</td>
							<td valign='top'>
								<select name='advnote_theme' id='advnote_theme'>
									<?php 		
									$i = 1; 
									foreach ($themes as $theme)
									{										
										?><option <?php 
										echo " value='$theme'";
										if ($advnote_theme==$theme){
											echo " selected='selected' "; 
										} 
										if ($i>10) {
											if ($mod==true) 
											{ 
											echo " disabled='disabled' "; 
											} 
										}
										?>> &nbsp;<?php echo $theme; ?></option>
										<?php
										$i++;
									}
								?>
								</select>
								<a href='<?php echo ADVANCEDNOTIFICATIONS_URLPATH; ?>advnote_theme_demonstration.php' target='_blank'><img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/preview_icon.png" style="cursor:pointer;" border=0 title="Preview Themes" id='advnote_preview_selected_theme'> </a>
							</td>
						</tr>
						<tr>
							<td valign=top style='width:205px'>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Select where you want the notification to be presented."> 	
									Placement
							</td>
							<td valign='top'>
								<select name= 'advnote_placement' id='advnote_placement'>									
									<option value = 'top' <?php if ($advnote_placement=='top'||!$advnote_expire_by){ echo "selected='selected'";} ?>>Top of Page</option>
									<option value = 'bottom' <?php if ($advnote_placement=='bottom'){ echo "selected='selected'";} ?>>Bottom of Page</option>
									<option value = 'center' <?php if ($advnote_placement=='center'){ echo "selected='selected'";} ?>>Center of Page</option>
									<option value = "['right','top']" <?php if ($advnote_placement=="['right','top']"){ echo "selected='selected'";} ?>>Top Right</option>
									<option value = "['left','top']" <?php if ($advnote_placement=="['left','top']"){ echo "selected='selected'";} ?>>Top Left</option>
									<option value = "['right','bottom']" <?php if ($advnote_placement=="['bottom','right']"){ echo "selected='selected'";} ?>>Bottom Right</option>
									<option value = "['left','bottom']" <?php if ($advnote_placement=="['left','bottom']"){ echo "selected='selected'";} ?>>Bottom Left</option>									
									<option value = 'above_post' <?php if ($advnote_placement=='above_post'){ echo "selected='selected'";} ?>>Above Post</option>
									<option value = 'below_post' <?php if ($advnote_placement=='below_post'){ echo "selected='selected'";} ?>>Below Post</option>
									<option value = 'php' <?php if ($advnote_placement=='php'){ echo "selected='selected'";} ?>>Custom PHP Insert</option>
								
								</select>
							</td>
						</tr>
						<tr>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="When & How should we fire the notification? 'Instant' immediately fires the notification upon pageload. 'Delayed' will fire the notification after x time has passed. 'Scrollpoint Y Coordinate' mode will use a scrollpoint to toggle the notification on or off whereas 'Scrollpoint Element' will use a an element found within the DOM to toggle the notification on or off."> 	
									Placement Nature:
							</td>
							<td valign='top'>
								<select name= 'advnote_delay_nature' id='advnote_delay_nature'>									
									<option value = 'none' <?php if ($advnote_delay_nature=='none'||!$advnote_delay_nature){ echo "selected='selected'";} ?>>Instant placement</option>
									<option value = 'delay' <?php if ($advnote_delay_nature=='delay'){ echo "selected='selected'";} ?>  <?php if ($mod==true) { echo "disabled='disabled'"; } ?>>Delayed placemnet</option>
									<option value = 'scrollpoint_coordinate' <?php if ($advnote_delay_nature=='scrollpoint_coordinate'){ echo "selected='selected'";} ?>  <?php if ($mod==true) { echo "disabled='disabled'"; } ?>>Scrollpoint -Y Cordinate </option>
									<option value = 'scrollpoint_element' <?php if ($advnote_delay_nature=='scrollpoint_element'){ echo "selected='selected'";} ?>  <?php if ($mod==true) { echo "disabled='disabled'"; } ?>>Scrollpoint - Element </option>
								</select>
								<em><small><?php echo $label; ?></small></em>
							</td>
						</tr>									
						<tr id='tr_delay_delay' <?php if ($advnote_delay_nature!='delay') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Time it takes before this notification displays. Set to 0 for no delay."> 	
									Delay:
							</td>
							<td valign='top'>
								<input size=5 name='advnote_delay' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> value='<?php  if ($advnote_delay_delay) { echo $advnote_delay_delay; } else { echo "3500"; } ?>'>  <em><small>in milliseconds</small></em>
							</td>
						</tr>
						<tr id='tr_delay_scrollpoint_coordinate' <?php if ($advnote_delay_nature!='scrollpoint_coordinate') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Time it takes before this notification displays. Set to 0 for no delay."> 	
								  Scrollpoint Y coordinate:
							</td>
							<td valign='top'>
								<input size=5 name='advnote_delay_scrollpoint_coordinate'  <?php if ($mod==true) { echo "disabled='disabled'"; } ?> value='<?php  if ($advnote_delay_scrollpoint_coordinate) { echo $advnote_delay_scrollpoint_coordinate; } else { echo "1000"; } ?>'>  <em><small>in pixels from top of screen</small></em>
							</td>
						</tr>
						<tr id='tr_delay_scrollpoint_element' <?php if ($advnote_delay_nature!='scrollpoint_element') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Input the html classname of the element we will use for a scroll marker. For example if the element is  <div class='footer'></div> then 'footer' would be the id name. You may have to manually edit your theme's template and add an element where you want it. "> 	
								  Scrollpoint element classname:
							</td>
							<td valign='top'>
								<input size=10 name='advnote_delay_scrollpoint_element' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> value='<?php  if ($advnote_delay_scrollpoint_element) { echo $advnote_delay_scrollpoint_element; } else { echo ""; } ?>'>  <em><small></small></em>
							</td>
						</tr>
						<tr id='tr_delay_scrollpoint_features' <?php if ($advnote_delay_nature!='scrollpoint_coordinate'&&$advnote_delay_nature!='scrollpoint_element') { echo "style='display:none'"; } ?> >
							<td valign=top >
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Reversable means that the same scrollpoint used to fire the notification will cause the notification to close/open once it has left/entered the in-scroll-view area. The scrollpoint will fire an open event or close event depending on your scrollpint feature setting."> 	
								Scrollpoint Features	
							</td>
							<td valign='top'>
								<select name= 'advnote_delay_scrollpoint_features' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >									
									<option value = '0' <?php if ($advnote_delay_scrollpoint_features=='0'){ echo "selected='selected'";} ?>>Begin closed - non reversable.</option>
									<option value = '1' <?php if ($advnote_delay_scrollpoint_features=='1'){ echo "selected='selected'";} ?>>Begin closed - reversable.</option>
									<option value = '2' <?php if ($advnote_delay_scrollpoint_features=='2'){ echo "selected='selected'";} ?>>Begin open - non reversable.</option>
									<option value = '3' <?php if ($advnote_delay_scrollpoint_features=='3'){ echo "selected='selected'";} ?>>Begin open - reversable.</option>
									
								</select>
							</td>
						</tr>
						<tr>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Typically we should leave this at 100%, but also this setting accepts pixel based paramaters."> 	
									Width:
							</td>
							<td valign='top'>
								<input type="text" name='advnote_width' size='5' value='<?php if (!$advnote_width){ echo "100%";}else { echo $advnote_width; } ?>'> <em><small>( Eg: 100% or 100px )</small></em>
								<br>
							</td>
						</tr>
						<tr>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Typically we should leave this at 100%, but also this setting accepts pixel based paramaters."> 	
									Height:
							</td>
							<td valign='top'>
								<input type="text" name='advnote_height' size='5' value='<?php if (!$advnote_height){ echo "100%";}else { echo $advnote_height; } ?>'> <em><small>( Eg: 100% or 100px )</small></em>
								<br>
							</td>
						</tr>						
						<tr>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Checking this means the background of the page will appear darkened until the annoucement is closed."> 	
									Display as Modal:
							</td>
							<td valign='top'>
								<input type="checkbox" name='advnote_modal' id='advnote_checkbox_modal'  value='true' <?php if ($advnote_modal=='true'){ echo "checked";}?>> <em></em>
								<br>
							</td>
						</tr>
						<tr class='tr_modal' <?php if ($advnote_modal!='true') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="CSS color syle of the dimming effect modaling creates. eg #a9a9a9"> 	
									Modal Screen Color:
							</td>
							<td valign='top'>
								<input type="input" name='advnote_modal_screen_color' size=7 value='<?php if ($advnote_modal_screen_color){ echo $advnote_modal_screen_color;} else { echo "#a9a9a9"; }?>' > 
								<br>
							</td>
						</tr>
						<tr>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Checking this means the user will be able to resize the annoucement."> 	
									Resizable:
							</td>
							<td valign='top'>
								<input type="checkbox" name='advnote_resizable' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> value='true' <?php if ($advnote_resizable=='true'){ echo "checked";}?>> <em></em>
								<br>
								<em><small><?php echo $label; ?></small></em>
							</td>
						</tr>
						<tr>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Checking this means the user will be able to dislodge the annoucement from starting position and move it around."> 	
									Movable:
							</td>
							<td valign='top'>
								<input type="checkbox" name='advnote_draggable' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> value='true' <?php if ($advnote_draggable=='true'){ echo "checked";}?>> <em></em>
								<br>
								<em><small><?php echo $label; ?></small></em>
							</td>
						</tr>
						<tr>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Checking this means the user will be able to close notification dialog."> 	
									Closable:
							</td>
							<td valign='top'>
								<input type="checkbox" name='advnote_closable' <?php if ($mod==true) { echo "disabled='disabled'"; } ?>  value='true' <?php if ($advnote_closable=='true'){ echo "checked";}?>> 
								<br>
							</td>
						</tr>
						<tr>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Checking this will cause the title to display above the notification. Leaving it unchecked will disable the notification title."> 	
									Show Title:
							</td>
							<td valign='top'>
								<input type="checkbox" name='advnote_show_title'  value='true' <?php if ($advnote_show_title=='true'){ echo "checked";}?>> 
								<br>
							</td>
						</tr>	
						<tr>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Checking this will apply animation effects to the notification as it appears and is closed."> 	
									Enable Show & Hide Animation:
							</td>
							<td valign='top'>
								<input type="checkbox" name='advnote_animate_show_hide' id='advnote_checkbox_animate_show_hide' value='true' <?php if ($advnote_animate_show_hide=='true'){ echo "checked";}?>> 
								<br>
								<em><small><?php echo $label; ?></small></em>
							</td>
						</tr>	
						<tr>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Checking this will apply animation effects to the notification as it appears and is closed."> 	
									Enable After Effect Animation:
							</td>
							<td valign='top'>
								<input type="checkbox" name='advnote_animate_after_effect' id='advnote_checkbox_animate_after_effect' value='true' <?php if ($advnote_animate_after_effect=='true'){ echo "checked";}?>> 
								<br>
								<em><small><?php echo $label; ?></small></em>
							</td>
						</tr>
						<tr class='tr_animate_show_hide'  <?php if ($advnote_animate_show_hide!='true') { echo "style='display:none'"; } ?>>
							<td colspan=2  >
								<br><br>
								<u><small>Notification 'Show' Settings</small></u>
							</td>
						</tr>
						<tr class='tr_animate_show_hide' <?php if ($advnote_animate_show_hide!='true') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing"> 	
									Show Animation:
							</td>
							<td valign='top'>
								<select name='advnote_animate_show_effect' id='advnote_select_animate_show_effect'>
									<option value='blind' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_show_effect=='blind'){echo "selected='true'"; } ?>>blind</option>
									<option value='clip' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_show_effect=='clip'){echo "selected='true'"; } ?>>clip</option>
									<option value='drop' <?php if ($mod==true) { echo "disabled='disabled'"; } ?>  <?php if ($advnote_animate_show_effect=='drop'){echo "selected='true'"; } ?>>drop</option>									
									<option value='fade'  <?php if ($advnote_animate_show_effect=='fade'){echo "selected='true'"; } ?>>fade</option>
									<option value='fold' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_show_effect=='fold'){echo "selected='true'"; } ?>>fold</option>	
									<option value='puff' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_show_effect=='puff'){echo "selected='true'"; } ?>>puff</option>																	
									<option value='slide'<?php if ($mod==true) { echo "disabled='disabled'"; } ?>  <?php if ($advnote_animate_show_effect=='slide'){echo "selected='true'"; } ?>>slide</option>
									<option value='scale' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_show_effect=='scale'){echo "selected='true'"; } ?>>scale</option>
								</select>
							</td>
						</tr>
						<tr class ='tr_animate_show_hide_settings' id='tr_show_animate_blind_direction' <?php if ($advnote_animate_show_hide!='true'&&$advnote_animate_show_effect!='blind'||$advnote_animate_show_hide=='true'&&$advnote_animate_show_effect!='blind') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. The direction of the effect. Can be 'vertical' or 'horizontal'."> 	
									Blind Direction:
							</td>
							<td valign='top' id = 'id_advnote_animate_blind_direction'>
								<select name='advnote_animate_show_effect_blind_direction' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option id='id_option_blind_direction' value='left' <?php if ($advnote_animate_show_effect_direction=='left'){echo "selected='true'"; } ?>>left</option>
									<option id='id_option_blind_direction' value='right' <?php if ($advnote_animate_show_effect_direction=='right'){echo "selected='true'"; } ?>>right</option>
									<option id='id_option_blind_direction' value='up' <?php if ($advnote_animate_show_effect_direction=='up'){echo "selected='true'"; } ?>>up</option>
									<option id='id_option_blind_direction' value='down' <?php if ($advnote_animate_show_effect_direction=='down'){echo "selected='true'"; } ?>>down</option>
								</select>
							</td>
						</tr>
						<tr  class ='tr_animate_show_hide_settings'   id='tr_show_animate_drop_direction' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_show_effect!='drop') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. The direction of the effect. Can be 'vertical' or 'horizontal'."> 	
									Drop Direction:
							</td>
							<td valign='top' id = 'id_advnote_animate_drop_direction'>
								<select name='advnote_animate_show_effect_drop_direction' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option id='id_option_drop_direction' value='left' <?php if ($advnote_animate_show_effect_direction=='left'){echo "selected='true'"; } ?>>left</option>
									<option id='id_option_drop_direction' value='right' <?php if ($advnote_animate_show_effect_direction=='right'){echo "selected='true'"; } ?>>right</option>
									<option id='id_option_drop_direction' value='up' <?php if ($advnote_animate_show_effect_direction=='up'){echo "selected='true'"; } ?>>up</option>
									<option id='id_option_drop_direction' value='down' <?php if ($advnote_animate_show_effect_direction=='down'){echo "selected='true'"; } ?>>down</option>
								</select>
							</td>
						</tr>
						<tr  class ='tr_animate_show_hide_settings'   id='tr_show_animate_clip_direction' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_show_effect!='clip') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. The direction of the effect. Can be 'vertical' or 'horizontal'."> 	
									Clip Direction:
							</td>
							<td valign='top' id = 'id_advnote_animate_clip_direction'>
								<select name='advnote_animate_show_effect_clip_direction' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option id='id_option_clip_direction' value='horizontal' <?php if ($advnote_animate_show_effect_direction=='horizontal'){echo "selected='true'"; } ?>>Horizontal</option>
									<option id='id_option_clip_direction' value='vertical' <?php if ($advnote_animate_show_effect_direction=='vertical'){echo "selected='true'"; } ?>>Vertical</option>
								</select>
							</td>
						</tr>
						<tr  class ='tr_animate_show_hide_settings'   id='tr_show_animate_slide_direction' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_show_effect!='slide') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing"> 	
									Slide Direction:
							</td>
							<td valign='top' >
								<select name='advnote_animate_show_effect_slide_direction' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option id='id_option_slide_direction' value='left' <?php if ($advnote_animate_show_effect_direction=='left'){echo "selected='true'"; } ?>>left</option>
									<option id='id_option_slide_direction' value='right' <?php if ($advnote_animate_show_effect_direction=='right'){echo "selected='true'"; } ?>>right</option>
									<option id='id_option_slide_direction' value='up' <?php if ($advnote_animate_show_effect_direction=='up'){echo "selected='true'"; } ?>>up</option>
									<option id='id_option_slide_direction' value='down' <?php if ($advnote_animate_show_effect_direction=='down'){echo "selected='true'"; } ?>>down</option>
								</select>
							</td>
						</tr>	
						<tr  class ='tr_animate_show_hide_settings'   id='tr_show_animate_scale_direction' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_show_effect!='scale') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. The direction of the effect. Can be 'both', 'vertical' or 'horizontal'."> 	
									Scale Direction:
							</td>
							<td valign='top' id = 'id_advnote_animate_direction'>
								<select name='advnote_animate_show_effect_scale_direction' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option id='id_option_scale_direction' value='horizontal' <?php if ($advnote_animate_show_effect_direction=='horizontal'){echo "selected='true'"; } ?>>Horizontal</option>
									<option id='id_option_scale_direction' value='vertical' <?php if ($advnote_animate_show_effect_direction=='vertical'){echo "selected='true'"; } ?>>Vertical</option>
									<option id='id_option_scale_direction' value='both' <?php if ($advnote_animate_show_effect_direction=='both'){echo "selected='true'"; } ?>>Both</option>
								</select>
							</td>
						</tr>
						
						
						<tr  class ='tr_animate_show_hide_settings'   id='tr_show_animate_fold_method' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_show_effect!='fold') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. Whether to fold horizontally first or not."> 	
									Fold Method:
							</td>
							<td valign='top'>
								<select name='advnote_animate_show_effect_fold_method' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option value='horizontally' <?php if ($advnote_animate_show_effect_fold_method=='horizontally'){echo "selected='true'"; } ?>>Horizontally First</option>
									<option value='vertically' <?php if ($advnote_animate_show_effect_fold_method=='vertically'){echo "selected='true'"; } ?>>Virtically First</option>
								</select>
							</td>
						</tr>		
						<tr  class ='tr_animate_show_hide_settings'   id='tr_show_animate_size' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_show_effect!='fold') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing"> 	
									Fold Size:
							</td>
							<td valign='top'>
								<input size=5 name='advnote_animate_show_effect_size' <?php if ($mod==true) { echo "disabled='disabled'"; } ?>  value='<?php  if ($advnote_animate_show_effect_size) { echo $advnote_animate_show_effect_size; } else { echo "100"; } ?>'> 
							</td>
						</tr>
						<tr  class ='tr_animate_show_hide_settings'   id='tr_show_animate_origin' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_show_effect!='scale') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. The vanishing/appearing point"> 	
									Origin:
							</td>
							<td valign='top'>
								<select name='advnote_animate_show_effect_origin' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option value="['middle','center']" <?php if ($advnote_animate_show_effect_origin=="['middle','center']"){echo "selected='true'"; } ?>>Middle</option>
									<option value="['top','left']" <?php if ($advnote_animate_show_effect_origin=="['top','left']"){echo "selected='true'"; } ?>>Top Left</option>
									<option value="['top','center']" <?php if ($advnote_animate_show_effect_origin=="['top','center']"){echo "selected='true'"; } ?>>Top Middle</option>
									<option value="['top','right']" <?php if ($advnote_animate_show_effect_origin=="['top','right']"){echo "selected='true'"; } ?>>Top Right</option>
									<option value="['middle','right']" <?php if ($advnote_animate_show_effect_origin=="['middle','right']"){echo "selected='true'"; } ?>>Right</option>
									<option value="['bottom','right']" <?php if ($advnote_animate_show_effect_origin=="['bottom','right']"){echo "selected='true'"; } ?>>Bottom Right</option>
									<option value="['bottom','center']" <?php if ($advnote_animate_show_effect_origin=="['bottom','center']"){echo "selected='true'"; } ?>>Bottom Middle</option>
									<option value="['bottom','left']" <?php if ($advnote_animate_show_effect_origin=="['bottom','left']"){echo "selected='true'"; } ?>>Bottom Left</option>
									<option value="['middle','left']" <?php if ($advnote_animate_show_effect_origin=="['middle','left']"){echo "selected='true'"; } ?>>Left</option>
								</select>
							</td>
						</tr>
						<tr  class ='tr_animate_show_hide_settings'   id='tr_show_animate_scale_method' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_show_effect!='scale') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. Which areas of the element will be resized: 'both', 'box', 'content' Box resizes the border and padding of the element Content resizes any content inside of the element."> 	
									Scale Method:
							</td>
							<td valign='top'>
								<select name='advnote_animate_show_effect_scale_method' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option value="both" <?php if ($advnote_animate_show_effect_scale_method=="both"){echo "selected='true'"; } ?>>Both</option>
									<option value="content" <?php if ($advnote_animate_show_effect_scale_method=="content"){echo "selected='true'"; } ?>>Content</option>
									<option value="box" <?php if ($advnote_animate_show_effect_scale_method=="box"){echo "selected='true'"; } ?>>Box</option>
								</select>
							</td>
						</tr>						
						
						
						<tr  class ='tr_animate_show_hide_settings' id='tr_show_animate_duration'  <?php if ($advnote_animate_show_hide!='true') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Time it takes for animation to complete in miliseconds"> 	
									Animation Duration :
							</td>
							<td valign='top'>
								<input size=5 name='advnote_animate_show_effect_duration'  value='<?php  if ($advnote_animate_show_effect_duration) { echo $advnote_animate_show_effect_duration; } else { echo "500"; } ?>'> 
							</td>
						</tr>
						
						<tr  class='tr_animate_show_hide'   >
							<td colspan=2  class='tr_animate_show_hide'  <?php if ($advnote_animate_show_hide!='true') { echo "style='display:none'"; } ?>>
								<br><br>
								<u><small>Notification 'Hide' Settings</small></u>
							</td>
						</tr>
						
						
						<tr class='tr_animate_show_hide' <?php if ($advnote_animate_show_hide!='true') { echo "style='display:none'"; } ?>>
							<td valign=top>
								
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing"> 	
									Hide Animation:
							</td>
							<td valign='top'>
								<select name='advnote_animate_hide_effect' id='advnote_select_animate_hide_effect'>
									<option value='blind' <?php if ($mod==true) { echo "disabled='disabled'"; } ?>  <?php if ($advnote_animate_hide_effect=='blind'){echo "selected='true'"; } ?>>blind</option>
									<option value='clip' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_hide_effect=='clip'){echo "selected='true'"; } ?>>clip</option>
									<option value='drop' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_hide_effect=='drop'){echo "selected='true'"; } ?>>drop</option>									
									<option value='fade' <?php if ($advnote_animate_hide_effect=='fade'){echo "selected='true'"; } ?>>fade</option>
									<option value='fold' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_hide_effect=='fold'){echo "selected='true'"; } ?>>fold</option>	
									<option value='puff' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_hide_effect=='puff'){echo "selected='true'"; } ?>>puff</option>																	
									<option value='slide' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_hide_effect=='slide'){echo "selected='true'"; } ?>>slide</option>
									<option value='scale' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_hide_effect=='scale'){echo "selected='true'"; } ?>>scale</option>
								</select>
							</td>
						</tr>
							
						<tr  class ='tr_animate_show_hide_settings'   id='tr_hide_animate_blind_direction' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_hide_effect!='blind') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. The direction of the effect. Can be 'vertical' or 'horizontal'."> 	
									Blind Direction:
							</td>
							<td valign='top' id = 'id_advnote_animate_blind_direction'>
								<select name='advnote_animate_hide_effect_blind_direction' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option id='id_option_blind_direction' value='left' <?php if ($advnote_animate_hide_effect_direction=='left'){echo "selected='true'"; } ?>>left</option>
									<option id='id_option_blind_direction' value='right' <?php if ($advnote_animate_hide_effect_direction=='right'){echo "selected='true'"; } ?>>right</option>
									<option id='id_option_blind_direction' value='up' <?php if ($advnote_animate_hide_effect_direction=='up'){echo "selected='true'"; } ?>>up</option>
									<option id='id_option_blind_direction' value='down' <?php if ($advnote_animate_hide_effect_direction=='down'){echo "selected='true'"; } ?>>down</option>
								</select>
							</td>
						</tr>		
						<tr  class ='tr_animate_show_hide_settings'   id='tr_hide_animate_drop_direction' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_hide_effect!='drop') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. The direction of the effect.  Can be 'left', 'right', 'up', 'down'.."> 	
									Drop Direction:
							</td>
							<td valign='top' id = 'id_advnote_animate_drop_direction'>
								<select name='advnote_animate_hide_effect_drop_direction' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option id='id_option_drop_direction' value='left' <?php if ($advnote_animate_hide_effect_direction=='left'){echo "selected='true'"; } ?>>left</option>
									<option id='id_option_drop_direction' value='right' <?php if ($advnote_animate_hide_effect_direction=='right'){echo "selected='true'"; } ?>>right</option>
									<option id='id_option_drop_direction' value='up' <?php if ($advnote_animate_hide_effect_direction=='up'){echo "selected='true'"; } ?>>up</option>
									<option id='id_option_drop_direction' value='down' <?php if ($advnote_animate_hide_effect_direction=='down'){echo "selected='true'"; } ?>>down</option>
								</select>
							</td>
						</tr>				
						<tr  class ='tr_animate_show_hide_settings'   id='tr_hide_animate_scale_direction' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_hide_effect!='scale') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. The direction of the effect. Can be 'both', 'vertical' or 'horizontal'."> 	
									Scale Direction:
							</td>
							<td valign='top' id = 'id_advnote_animate_direction'>
								<select name='advnote_animate_hide_effect_scale_direction' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option id='id_option_hide_scale_clip_direction' value='horizontal' <?php if ($advnote_animate_hide_effect_direction=='horizontal'){echo "selected='true'"; } ?>>Horizontal</option>
									<option id='id_option_hide_scale_clip_direction' value='vertical' <?php if ($advnote_animate_hide_effect_direction=='vertical'){echo "selected='true'"; } ?>>Vertical</option>
									<option id='id_option_hide_scale_clip_direction' value='both' <?php if ($advnote_animate_hide_effect_direction=='both'){echo "selected='true'"; } ?>>Both</option>
								</select>
							</td>
						</tr>
						<tr  class ='tr_animate_show_hide_settings'   id='tr_hide_animate_slide_direction' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_hide_effect!='slide') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing"> 	
									Slide Direction:
							</td>
							<td valign='top' id = 'id_advnote_animate_direction'>
								<select name='advnote_animate_hide_effect_slide_direction' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option id='id_option_hide_slide_direction'  value='left' <?php if ($advnote_animate_hide_effect_direction=='left'){echo "selected='true'"; } ?>>left</option>
									<option id='id_option_hide_slide_direction'  value='right' <?php if ($advnote_animate_hide_effect_direction=='right'){echo "selected='true'"; } ?>>right</option>
									<option id='id_option_hide_slide_direction'  value='up' <?php if ($advnote_animate_hide_effect_direction=='up'){echo "selected='true'"; } ?>>up</option>
									<option  id='id_option_hide_slide_direction' value='down' <?php if ($advnote_animate_hide_effect_direction=='down'){echo "selected='true'"; } ?>>down</option>
								</select>
							</td>
						</tr>	
						<tr  class ='tr_animate_show_hide_settings'   id='tr_hide_animate_clip_direction' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_hide_effect!='clip') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. The direction of the effect. Can be 'both', 'vertical' or 'horizontal'."> 	
									Clip Direction:
							</td>
							<td valign='top' id = 'id_advnote_animate_clip_direction'>
								<select name='advnote_animate_hide_effect_direction' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option  id='id_option_hide_scale_clip_direction' value='horizontal' <?php if ($advnote_animate_hide_effect_direction=='horizontal'){echo "selected='true'"; } ?>>Horizontal</option>
									<option  id='id_option_hide_scale_clip_direction' value='vertical' <?php if ($advnote_animate_hide_effect_direction=='vertical'){echo "selected='true'"; } ?>>Vertical</option>
								</select>
							</td>
						</tr>
						<tr  class ='tr_animate_show_hide_settings'   id='tr_hide_animate_origin' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_hide_effect!='scale') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. The vanishing/appearing point"> 	
									Origin:
							</td>
							<td valign='top'>
								<select name='advnote_animate_hide_effect_origin' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option value="['middle','center']" <?php if ($advnote_animate_hide_effect_origin=="['middle','center']"){echo "selected='true'"; } ?>>Middle</option>
									<option value="['top','left']" <?php if ($advnote_animate_hide_effect_origin=="['top','left']"){echo "selected='true'"; } ?>>Top Left</option>
									<option value="['top','center']" <?php if ($advnote_animate_hide_effect_origin=="['top','center']"){echo "selected='true'"; } ?>>Top Middle</option>
									<option value="['top','right']" <?php if ($advnote_animate_hide_effect_origin=="['top','right']"){echo "selected='true'"; } ?>>Top Right</option>
									<option value="['middle','right']" <?php if ($advnote_animate_hide_effect_origin=="['middle','right']"){echo "selected='true'"; } ?>>Right</option>
									<option value="['bottom','right']" <?php if ($advnote_animate_hide_effect_origin=="['bottom','right']"){echo "selected='true'"; } ?>>Bottom Right</option>
									<option value="['bottom','center']" <?php if ($advnote_animate_hide_effect_origin=="['bottom','center']"){echo "selected='true'"; } ?>>Bottom Middle</option>
									<option value="['bottom','left']" <?php if ($advnote_animate_hide_effect_origin=="['bottom','left']"){echo "selected='true'"; } ?>>Bottom Left</option>
									<option value="['middle','left']" <?php if ($advnote_animate_hide_effect_origin=="['middle','left']"){echo "selected='true'"; } ?>>Left</option>
								</select>
							</td>
						</tr>
						<tr  class ='tr_animate_show_hide_settings'   id='tr_hide_animate_scale_method' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_hide_effect!='scale') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. Which areas of the element will be resized: 'both', 'box', 'content' Box resizes the border and padding of the element Content resizes any content inside of the element."> 	
									Scale Method:
							</td>
							<td valign='top'>
								<select name='advnote_animate_hide_effect_scale_method' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option value="both" <?php if ($advnote_animate_hide_effect_scale_method=="both"){echo "selected='true'"; } ?>>Both</option>
									<option value="content" <?php if ($advnote_animate_hide_effect_scale_method=="content"){echo "selected='true'"; } ?>>Content</option>
									<option value="box" <?php if ($advnote_animate_hide_effect_scale_method=="box"){echo "selected='true'"; } ?>>Box</option>
								</select>
							</td>
						</tr>	
						
						<tr  class ='tr_animate_show_hide_settings'   id='tr_hide_animate_fold_method' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_hide_effect!='fold') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing. Whether to fold horizontally first or not."> 	
									Fold Method:
							</td>
							<td valign='top'>
								<select name='advnote_animate_hide_effect_fold_method' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> >
									<option value='horizontally' <?php if ($advnote_animate_hide_effect_fold_method=='horizontally'){echo "selected='true'"; } ?>>Horizontally First</option>
									<option value='vertically' <?php if ($advnote_animate_hide_effect_fold_method=='vertically'){echo "selected='true'"; } ?>>Virtically First</option>
								</select>
							</td>
						</tr>		
						<tr  class ='tr_animate_show_hide_settings'   id='tr_hide_animate_size' <?php if ($advnote_animate_show_hide!='true'||$advnote_animate_show_hide=='true'&&$advnote_animate_hide_effect!='fold') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification appearance and closing"> 	
									Fold Size:
							</td>
							<td valign='top'>
								<input size=5 name='advnote_animate_hide_effect_size' <?php if ($mod==true) { echo "disabled='disabled'"; } ?>  value='<?php  if ($advnote_animate_hide_effect_size) { echo $advnote_animate_hide_effect_size; } else { echo "100"; } ?>'> 
							</td>
						</tr>						
						<tr  class ='tr_animate_show_hide_settings'    id='tr_hide_animate_duration'  <?php if ($advnote_animate_show_hide!='true') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Time it takes for animation to complete in miliseconds"> 	
									Animation Duration :
							</td>
							<td valign='top'>
								<input size=5 name='advnote_animate_hide_effect_duration'  value='<?php  if ($advnote_animate_hide_effect_duration) { echo $advnote_animate_hide_effect_duration; } else { echo "500"; } ?>'> 
							</td>
						</tr>	

						<tr>
							<td colspan=2  class='tr_animate_after_effect'  <?php if ($advnote_animate_after_effect!='true') { echo "style='display:none'"; } ?>>
								<br><br>
								<u><small>Notification After Effects Settings</small></u>
							</td>
						</tr>
						<tr class='tr_animate_after_effect' <?php if ($advnote_animate_after_effect!='true') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation is applied once the notification is already displayed"> 	
									After Effect Animation:
							</td>
							<td valign='top'>
								<select name='advnote_animate_after_effect_effect' id='advnote_select_animate_after_effect'>
									<option value='bounce' <?php if ($advnote_animate_after_effect_effect=='blind'){echo "selected='true'"; } ?>>bounce</option>
									<option value='highlight' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_after_effect_effect=='highlight'){echo "selected='true'"; } ?>>highlight</option>
									<option value='pulsate' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_after_effect_effect=='pulsate'){echo "selected='true'"; } ?>>pulsate</option>									
									<option value='shake' <?php if ($mod==true) { echo "disabled='disabled'"; } ?> <?php if ($advnote_animate_after_effect_effect=='shake'){echo "selected='true'"; } ?>>shake</option>								
								</select>
							</td>
						</tr>
						
						<tr class='tr_animate_after_effect'  <?php if ($advnote_animate_after_effect!='true') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Time it takes before this animation fires in miliseconds"> 	
									Effect Delay :
							</td>
							<td valign='top'>
								<input size=5 name='advnote_animate_after_effect_delay' <?php if ($mod==true) { echo "disabled='disabled'"; } ?>  value='<?php  if ($advnote_animate_after_effect_delay) { echo $advnote_animate_after_effect_delay; } else { echo "1000"; } ?>'>  <em><small>in milliseconds</small></em>
							</td>
						</tr>						
						<tr class='tr_animate_after_effect' id='tr_after_effect_animate_bounce_direction' <?php if ($advnote_animate_after_effect!='true'||$advnote_animate_after_effect_effect!='bounce') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification once it is displayed. The direction of the effect. Can be 'both', 'vertical' or 'horizontal'."> 	
									Bounce Direction:
							</td>
							<td valign='top' >
								<select name='advnote_animate_after_effect_bounce_direction'>
									<option  value='left' <?php if ($advnote_animate_after_effect_direction=='left'){echo "selected='true'"; } ?>>left</option>
									<option  value='right' <?php if ($advnote_animate_after_effect_direction=='right'){echo "selected='true'"; } ?>>right</option>
									<option  value='up' <?php if ($advnote_animate_after_effect_direction=='up'){echo "selected='true'"; } ?>>up</option>
									<option  value='down' <?php if ($advnote_animate_after_effect_direction=='down'){echo "selected='true'"; } ?>>down</option>
								</select>
							</td>
						</tr>
						<tr class='tr_animate_after_effect' id='tr_after_effect_animate_shake_distance' <?php if ($advnote_animate_after_effect!='true'||$advnote_animate_after_effect_effect!='shake') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification once it is displayed. Distance to Shake. Interger."> 	
									Shake Distance:
							</td>
							<td valign='top' >
								<input size=5 name='advnote_animate_after_effect_shake_distance'  value='<?php  if ($advnote_animate_after_effect_distance) { echo $advnote_animate_after_effect_distance; } else { echo "20"; } ?>'> 
							</td>
						</tr>
						<tr class='tr_animate_after_effect' id='tr_after_effect_animate_bounce_distance' <?php if ($advnote_animate_after_effect!='true'||$advnote_animate_after_effect_effect!='bounce') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification once it is displayed. Distance to bounce. Interger."> 	
									Bounce Distance:
							</td>
							<td valign='top' >
								<input size=5 name='advnote_animate_after_effect_bounce_distance'  value='<?php  if ($advnote_animate_after_effect_distance) { echo $advnote_animate_after_effect_distance; } else { echo "20"; } ?>'> 
							</td>
						</tr>
						
						<tr class='tr_animate_after_effect' id='tr_after_effect_animate_highlight_color' <?php if ($advnote_animate_after_effect!='true'||$advnote_animate_after_effect_effect!='highlight') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Type of animation applied to notification once it is displayed.Highlight color. eg #ffffff"> 	
									Highlight Color:
							</td>
							<td valign='top' >
								<input size=5 name='advnote_animate_after_effect_highlight_color' <?php if ($mod==true) { echo "disabled='disabled'"; } ?>  value='<?php  if ($advnote_animate_after_effect_highlight_color) { echo $advnote_animate_after_effect_highlight_color; } else { echo "#ffff99"; } ?>'> 
							</td>
						</tr>
						
						<tr class='tr_animate_after_effect'  <?php if ($advnote_animate_after_effect!='true') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="Time it takes for animation to complete in miliseconds"> 	
									Animation Duration :
							</td>
							<td valign='top'>
								<input size=5 name='advnote_animate_after_effect_duration'  value='<?php  if ($advnote_animate_after_effect_duration) { echo $advnote_animate_after_effect_duration; } else { echo "300"; } ?>'> 
							</td>
						</tr>
						<tr class='tr_animate_after_effect'  id='tr_after_effect_animate_times'  <?php if ($advnote_animate_after_effect!='true') { echo "style='display:none'"; } ?>>
							<td valign=top>
								<img src="<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/tip.png" style="cursor:pointer;" border=0 title="How many times should we execute the animation?"> 	
									Execution Times :
							</td>
							<td valign='top'>
								<input size=5 name='advnote_animate_after_effect_times'  value='<?php  if ($advnote_animate_after_effect_times) { echo $advnote_animate_after_effect_times; } else { echo "3"; } ?>'> 
							</td>
						</tr>	
				</table>
			</div>	
		</div>
		<?php
	}
	
	
	
	function advnote_meta_box_tips()
	{		
		
		?>
		<div class=" " >
			<div class="inside">
				<table>
					<tr>	
						<td>
						<ul>
							<ol><em>Create your own close button for custom styling:<br>
							<?php
								$string = "<a href='#' id='id_close_notification'>close text here</a>";
								$string = htmlentities($string);
								echo $string;
							?>
							</em>
								
							</ol>
							<br><br>
							<ol><em>Code for custom PHP placement of notifcation:<br>
							<?php
								echo "<pre>advnote_php_placement();</pre>";							
							?>
							</em>
								
							</ol>
						</ul>
						</td>
					</tr>
				</table>
					
			</div>	
		</div>
		<?php
	}

	
	function advnote_meta_box_advert() {
		?>
		<div >
			<div class="inside" style='max-height:37px'>
				<table>
					<tr>	
						<td>
							<a href='http://www.hatnohat.com/members/advanced-notifications-pro/' target='_blank' title='Go PRO now to unlock advanced features!'><img src='<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>images/meta_advert_button.png' style='position:relative;margin-left:-13px;margin-top:-3px;'></a>
	
						</td>
					</tr>
				</table>
					
			</div>	
		</div>
		<?php
	}		


	function advnote_save_postdata($post_id)
	{
		global $table_prefix;
		$post_type = $_POST['post_type'];
		
		//echo 3; exit;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ||$_POST['post_type']=='revision')
		{
			return;
		}
					
		
		//echo $_POST['lp-variation-id'];exit;
		if($parent_id = wp_is_post_revision($postID))
		{
			$postID = $parent_id;
		}
		
		if ($post_type=='notifications')
		{
			$advnote_status = $_POST['advnote_status'];
			$rules['expire_by'] = $_POST['advnote_expire_by'];
			$rules['expire_by_date'] = $_POST['advnote_expire_by_date'];
			$rules['expire_by_visitors'] = $_POST['advnote_expire_by_visitors'];
			$rules['expire_by_visitors_count'] = $_POST['advnote_expire_by_visitors_count'];
			if ($_POST['advnote_userroles_permit'])
			{
				$rules['userroles_permit'] = implode(',',$_POST['advnote_userroles_permit']);
			}
			if ($_POST['advnote_userroles_prevent'])
			{
				$rules['userroles_prevent'] = implode(',',$_POST['advnote_userroles_prevent']);
			}
			if ($_POST['advnote_post_types'])
			{
				$rules['post_types'] = implode(',',$_POST['advnote_post_types']);
			}
			
			$rules['persistance'] = $_POST['advnote_persistance'];
			$rules['persistance_reset']	= $_POST['advnote_persistance_reset'];
			$rules['persistance_reset_rule'] = $_POST['advnote_persistance_reset_rule'];			
			$rules['useragent_prevent'] = $_POST['advnote_useragent_prevent'];			
			$rules = json_encode($rules);
			
			$styling['placement'] = $_POST['advnote_placement'];
			$styling['theme'] = $_POST['advnote_theme'];
			$styling['width'] = str_replace('px','',$_POST['advnote_width']);
			$styling['height'] =  str_replace('px','',$_POST['advnote_height']);
			
			if ($styling['height']=='100%')
			{
				$styling['height'] = 'auto';
			}
			
			$styling['delay_nature'] = $_POST['advnote_delay_nature'];
			if ($styling['delay_nature']=='none')
			{
				$styling['delay_delay'] = 0;
			}
			else
			{
				$styling['delay_delay'] = $_POST['advnote_delay'];
			}
			//echo $styling['delay_delay'];exit;
			$styling['delay_scrollpoint_coordinate'] = $_POST['advnote_delay_scrollpoint_coordinate'];
			$styling['delay_scrollpoint_element'] = $_POST['advnote_delay_scrollpoint_element'];
			$styling['delay_scrollpoint_features'] = $_POST['advnote_delay_scrollpoint_features'];
			
			$styling['resizable'] = $_POST['advnote_resizable'] == 'true' ? "true":  "false";
			$styling['modal'] = $_POST['advnote_modal'] == 'true' ? "true":  "false";
			$styling['modal_screen_color'] = $_POST['advnote_modal_screen_color'];
			$styling['draggable'] = $_POST['advnote_draggable'] == 'true' ? "true": "false";
			$styling['closable'] = $_POST['advnote_closable'] == 'true' ? "true":  "false";
			$styling['show_title'] = $_POST['advnote_show_title'] == 'true' ? "true": "false";
			
			$styling['animate_show_hide'] = $_POST['advnote_animate_show_hide'] == 'true' ? "true": "false";
			$styling['animate_hide_effect'] = $_POST['advnote_animate_hide_effect'];
			$styling['animate_show_effect'] = $_POST['advnote_animate_show_effect'];
			
			$styling['animate_show_effect_direction'] = "up";
			$styling['animate_hide_effect_direction'] = "up";
			$styling['animate_show_effect_duration'] = $_POST['advnote_animate_show_effect_duration'];
			$styling['animate_hide_effect_duration'] = $_POST['advnote_animate_hide_effect_duration'];
			
			$styling['animate_show_effect_fold_method'] = $_POST['advnote_animate_show_effect_fold_method'];
			$styling['animate_hide_effect_fold_method'] = $_POST['advnote_animate_hide_effect_fold_method'];
			$styling['animate_show_effect_size'] = $_POST['advnote_animate_show_effect_size'];
			$styling['animate_hide_effect_size'] = $_POST['advnote_animate_hide_effect_size'];
			
			$styling['animate_show_effect_scale_method'] = $_POST['advnote_animate_show_effect_scale_method'];
			$styling['animate_show_effect_origin'] = $_POST['advnote_animate_show_effect_origin'];
			$styling['animate_hide_effect_scale_method'] = $_POST['advnote_animate_hide_effect_scale_method'];
			$styling['animate_hide_effect_origin'] = $_POST['advnote_animate_hide_effect_origin'];
			
			if ($_POST['advnote_animate_show_effect']=='blind')
			{
				$styling['animate_show_effect_direction'] = $_POST['advnote_animate_show_effect_blind_direction'];
			}
			if ($_POST['advnote_animate_show_effect']=='drop')
			{
				$styling['animate_show_effect_direction'] = $_POST['advnote_animate_show_effect_drop_direction'];
			}
			if ($_POST['advnote_animate_show_effect']=='clip')
			{
				$styling['animate_show_effect_direction'] = $_POST['advnote_animate_show_effect_clip_direction'];
			}
			if ($_POST['advnote_animate_show_effect']=='scale')
			{
				$styling['animate_show_effect_direction'] = $_POST['advnote_animate_show_effect_scale_direction'];
			}				
			if ($_POST['advnote_animate_show_effect']=='slide')
			{
				$styling['animate_show_effect_direction'] = $_POST['advnote_animate_show_effect_slide_direction'];
			}
			
			if ($_POST['advnote_animate_hide_effect']=='blind')
			{
				$styling['animate_hide_effect_direction'] = $_POST['advnote_animate_hide_effect_blind_direction'];
			}
			if ($_POST['advnote_animate_hide_effect']=='drop')
			{
				$styling['animate_hide_effect_direction'] = $_POST['advnote_animate_hide_effect_drop_direction'];
			}
			if ($_POST['advnote_animate_hide_effect']=='scale')
			{
				$styling['animate_hide_effect_direction'] = $_POST['advnote_animate_hide_effect_scale_direction'];
			}
			if ($_POST['advnote_animate_hide_effect']=='slide')
			{
				$styling['animate_hide_effect_direction'] = $_POST['advnote_animate_hide_effect_slide_direction'];
			}
			if ($_POST['advnote_animate_hide_effect']=='clip')
			{
				$styling['animate_hide_effect_direction'] = $_POST['advnote_animate_hide_effect_clip_direction'];
			}
			
			$styling['animate_after_effect_times'] = 5;
			$styling['animate_after_effect_distance'] = 20;
			$styling['animate_after_effect'] = $_POST['advnote_animate_after_effect'] == 'true' ? "true": "false";
			$styling['animate_after_effect_effect'] = $_POST['advnote_animate_after_effect_effect'];
			$styling['animate_after_effect_delay'] = $_POST['advnote_animate_after_effect_delay'];
			$styling['animate_after_effect_duration'] = $_POST['advnote_animate_after_effect_duration'];
			$styling['animate_after_effect_times'] = $_POST['advnote_animate_after_effect_times'];
			
			if ($_POST['advnote_animate_after_effect_effect']=='bounce')
			{
				$styling['animate_after_effect_direction'] = $_POST['advnote_animate_after_effect_bounce_direction'];
				$styling['animate_after_effect_distance'] = $_POST['advnote_animate_after_effect_bounce_distance'];
			}
			if ($_POST['advnote_animate_after_effect_effect']=='shake')
			{
				$styling['animate_after_effect_direction'] = $_POST['advnote_animate_after_effect_shake_direction'];
				$styling['animate_after_effect_distance'] = $_POST['advnote_animate_after_effect_shake_distance'];
			}
			
			
			$styling = json_encode($styling);
			$styling = str_replace('\\\\','\\',$styling);
			
			$query = "SELECT * FROM {$table_prefix}advnote_notifications WHERE post_id='{$post_id}'";
			$result = mysql_query($query);
			if (!$result){ echo $query; echo mysql_error(); exit;}
			$count = mysql_num_rows($result);
			
				
			
			if ($count>0)
			{
				$query = "UPDATE {$table_prefix}advnote_notifications SET status='$advnote_status', rules = '".mysql_real_escape_string($rules)."', styling='$styling' WHERE post_id='{$post_id}'";
				$result = mysql_query($query);
				if (!$result){ echo $query; echo mysql_error(); exit;}
			}
			else
			{
				$query = "INSERT INTO {$table_prefix}advnote_notifications (`post_id`,`rules`,`styling`,`status`) VALUES ('$post_id','".mysql_real_escape_string($rules)."','$styling','$advnote_status')";
				$result = mysql_query($query);
				if (!$result){ echo $query; echo mysql_error(); exit;}
			}
		}	
	}
	
	function advnote_admin_footer() {
		?>
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('.datepicker').datepicker({
				dateFormat : 'yy-mm-dd'
			});
			
			jQuery("#advnote_expire_by").live("change", function(){	
			   var input = jQuery(this).val();
			   if (input=='date')
			   {
				  jQuery(".advnote_expire_by_visitor").hide();
				  jQuery(".advnote_expire_by_date").show();
				   
			   }
			   else if (input=='visitors')
			   {
				  jQuery(".advnote_expire_by_date").hide();
				  jQuery(".advnote_expire_by_visitor").show();
				   
			   }
				else
				{
					jQuery(".advnote_expire_by_date").hide();
					jQuery(".advnote_expire_by_visitor").hide();
				}
			   
			});
			
			jQuery("#advnote_persistance").live("change", function(){	
			   var input = jQuery(this).val();
			   if (input=='persistant')
			   {
				  jQuery(".advnote_persistance").hide();
				   
			   }
			   else if (input=='once')
			   {
				  jQuery(".advnote_persistance").show();				   
			   }		   
			   
			});
					
			jQuery("#advnote_persistance_reset").live("change", function(){	
			    var input = jQuery(this).val();
			    if (input=='days')
			    {
				   jQuery(".advnote_persistance_reset_rule").show();
				   
			    }
			    else if (input=='pageviews')
			    {
				  jQuery(".advnote_persistance_reset_rule").show();				   
			    }		  
				else
				{
					 jQuery(".advnote_persistance_reset_rule").hide();		
				}
			   
			});
			
			jQuery("#advnote_checkbox_modal").live("click", function(){	
				if (this.checked) {
					jQuery(".tr_modal").show();	
				}
				else
				{
					jQuery(".tr_modal").hide();	
				}
			   
			});
				
			jQuery("#advnote_delay_nature").live("change", function(){	
			    var input = jQuery(this).val();
			    if (input=='none')
			    {
					jQuery("#tr_delay_delay").hide();
					jQuery("#tr_delay_scrollpoint_element").hide();		
					jQuery("#tr_delay_scrollpoint_coordinate").hide();		
					jQuery("#tr_delay_scrollpoint_features").hide();	
			    }
			    else if (input=='delay')
			    {
					jQuery("#tr_delay_delay").show();					
					jQuery("#tr_delay_scrollpoint_element").hide();		
					jQuery("#tr_delay_scrollpoint_coordinate").hide();		
					jQuery("#tr_delay_scrollpoint_features").hide();			   
			    }		  
				else if (input=='scrollpoint_coordinate')
				{
					jQuery("#tr_delay_delay").hide();
					jQuery("#tr_delay_scrollpoint_element").hide();		
					jQuery("#tr_delay_scrollpoint_coordinate").show();		
					jQuery("#tr_delay_scrollpoint_features").show();		
				}
				else if (input=='scrollpoint_element')
				{
					jQuery("#tr_delay_delay").hide();					
					jQuery("#tr_delay_scrollpoint_element").show();		
					jQuery("#tr_delay_scrollpoint_coordinate").hide();		
					jQuery("#tr_delay_scrollpoint_features").show();			
				}
			   
			});
			
			jQuery("#advnote_checkbox_animate_show_hide").live("click", function(){	
				if (this.checked) {
					jQuery(".tr_animate_show_hide").show();
					jQuery("#advnote_select_animate_show_effect").trigger("change");				
					jQuery("#advnote_select_animate_hide_effect").trigger("change");			
				}
				else
				{
					jQuery(".tr_animate_show_hide_settings").hide();	
					jQuery(".tr_animate_show_hide").hide();	
				}
			   
			});
			
			jQuery("#advnote_checkbox_animate_after_effect").live("click", function(){	
				if (this.checked) {
					jQuery(".tr_animate_after_effect").show();	
				}
				else
				{
					jQuery(".tr_animate_after_effect").hide();	
				}
			   
			});
			
			jQuery("#advnote_select_animate_show_effect").live("change", function(){	
			    var input = jQuery(this).val();
			    if (input=='blind')
			    {
					jQuery("#tr_show_animate_blind_direction").show();
					jQuery("#tr_show_animate_drop_direction").hide();
					jQuery("#tr_show_animate_slide_direction").hide();
					jQuery("#tr_show_animate_clip_direction").hide();
					jQuery("#tr_show_animate_scale_direction").hide();
					jQuery('#tr_show_animate_duration').show();
					jQuery('#tr_show_animate_fold_method').hide();	
					jQuery('#tr_show_animate_size').hide();
					jQuery("#tr_show_animate_origin").hide();					
					jQuery("#tr_show_animate_scale_method").hide();
				   
			    } 
				else if (input=='drop')
			    {
					jQuery("#tr_show_animate_blind_direction").hide();
					jQuery("#tr_show_animate_drop_direction").show();
					jQuery("#tr_show_animate_slide_direction").hide();
					jQuery("#tr_show_animate_clip_direction").hide();
					jQuery("#tr_show_animate_scale_direction").hide();
					jQuery('#tr_show_animate_duration').show();
					jQuery('#tr_show_animate_fold_method').hide();	
					jQuery('#tr_show_animate_size').hide();
					jQuery("#tr_show_animate_origin").hide();					
					jQuery("#tr_show_animate_scale_method").hide();
				   
			    }	
			    else if (input=='slide')
			    {
					jQuery("#tr_show_animate_blind_direction").hide();
					jQuery("#tr_show_animate_drop_direction").hide();
					jQuery("#tr_show_animate_slide_direction").show();
					jQuery("#tr_show_animate_clip_direction").hide();
					jQuery("#tr_show_animate_scale_direction").hide();
					jQuery('#tr_show_animate_duration').show();
					jQuery('#tr_show_animate_fold_method').hide();	
					jQuery('#tr_show_animate_size').hide();
					jQuery("#tr_show_animate_origin").hide();					
					jQuery("#tr_show_animate_scale_method").hide();
				   
			    }	
				else if (input=='fade')
			    {
					jQuery("#tr_show_animate_blind_direction").hide();
					jQuery("#tr_show_animate_drop_direction").hide();
					jQuery("#tr_show_animate_slide_direction").hide();
					jQuery("#tr_show_animate_clip_direction").hide();
					jQuery("#tr_show_animate_scale_direction").hide();
					jQuery('#tr_show_animate_duration').show();
					jQuery('#tr_show_animate_fold_method').hide();	
					jQuery('#tr_show_animate_size').hide();
					jQuery("#tr_show_animate_origin").hide();					
					jQuery("#tr_show_animate_scale_method").hide();
				   
			    }		
				else if (input=='puff')
			    {
					jQuery("#tr_show_animate_blind_direction").hide();
					jQuery("#tr_show_animate_drop_direction").hide();
					jQuery("#tr_show_animate_slide_direction").hide();
					jQuery("#tr_show_animate_clip_direction").hide();
					jQuery("#tr_show_animate_scale_direction").hide();
					jQuery("#tr_show_animate_direction").hide();
					jQuery('#tr_show_animate_duration').show();
					jQuery('#tr_show_animate_fold_method').hide();	
					jQuery('#tr_show_animate_size').hide();
					jQuery("#tr_show_animate_origin").hide();					
					jQuery("#tr_show_animate_scale_method").hide();
				   
			    }
				else if (input=='scale')
			    {
					jQuery("#tr_show_animate_blind_direction").hide();
					jQuery("#tr_show_animate_drop_direction").hide();
					jQuery("#tr_show_animate_slide_direction").hide();
					jQuery("#tr_show_animate_clip_direction").hide();
					jQuery("#tr_show_animate_scale_direction").show();
					jQuery('#tr_show_animate_fold_method').hide();	
					jQuery('#tr_show_animate_size').hide();
					jQuery("#tr_show_animate_origin").show();					
					jQuery("#tr_show_animate_scale_method").show();
					jQuery("#tr_show_animate_direction").show();
						
			    }
				else if (input=='fold')
			    {
					jQuery("#tr_show_animate_blind_direction").hide();
					jQuery("#tr_show_animate_drop_direction").hide();
					jQuery("#tr_show_animate_slide_direction").hide();
					jQuery("#tr_show_animate_clip_direction").hide();
					jQuery("#tr_show_animate_scale_direction").hide();
					jQuery('#tr_show_animate_duration').show();
					jQuery('#tr_show_animate_fold_method').show();	
					jQuery('#tr_show_animate_size').show();
					jQuery("#tr_show_animate_origin").hide();					
					jQuery("#tr_show_animate_scale_method").hide();		
						
			    }
				else if (input=='clip')
			    {
					jQuery("#tr_show_animate_blind_direction").hide();
					jQuery("#tr_show_animate_drop_direction").hide();
					jQuery("#tr_show_animate_slide_direction").hide();
					jQuery("#tr_show_animate_clip_direction").show();
					jQuery("#tr_show_animate_scale_direction").hide();
					jQuery('#tr_show_animate_duration').show();
					jQuery('#tr_show_animate_fold_method').hide();	
					jQuery('#tr_show_animate_size').hide();
					jQuery("#tr_show_animate_origin").hide();					
					jQuery("#tr_show_animate_scale_method").hide();		
						
			    }			      
				else
				{
					jQuery("#tr_show_animate_blind_direction").hide();
					jQuery("#tr_show_animate_drop_direction").hide();
					jQuery("#tr_show_animate_slide_direction").hide();
					jQuery("#tr_show_animate_clip_direction").hide();
					jQuery("#tr_show_animate_scale_direction").hide();	
					jQuery('#tr_show_animate_duration').show();						
					jQuery('#tr_show_animate_fold_method').hide();	
					jQuery('#tr_show_animate_size').hide();
					jQuery("#tr_show_animate_origin").hide();					
					jQuery("#tr_show_animate_scale_method").hide();					
				}			   
			});
			
			jQuery("#advnote_select_animate_hide_effect").live("change", function(){	
			    var input = jQuery(this).val();
			    if (input=='blind')
			    {
					jQuery("#tr_hide_animate_blind_direction").show();
					jQuery("#tr_hide_animate_drop_direction").hide();
					jQuery("#tr_hide_animate_slide_direction").hide();
					jQuery("#tr_hide_animate_clip_direction").hide();
					jQuery("#tr_hide_animate_scale_direction").hide();
				    jQuery('#tr_hide_animate_duration').show();
					jQuery('#tr_hide_animate_fold').hide();					
					jQuery('#tr_hide_animate_fold_method').hide();	
					jQuery('#tr_hide_animate_size').hide();
					jQuery("#tr_hide_animate_origin").hide();					
					jQuery("#tr_hide_animate_scale_method").hide();
				   
			    }	
				else if (input=='drop')
			    {
					jQuery("#tr_hide_animate_blind_direction").hide();
					jQuery("#tr_hide_animate_drop_direction").show();
					jQuery("#tr_hide_animate_slide_direction").hide();
					jQuery("#tr_hide_animate_clip_direction").hide();
					jQuery("#tr_hide_animate_scale_direction").hide();
				    jQuery('#tr_hide_animate_duration').show();
					jQuery('#tr_hide_animate_fold').hide();					
					jQuery('#tr_hide_animate_fold_method').hide();	
					jQuery('#tr_hide_animate_size').hide();
					jQuery("#tr_hide_animate_origin").hide();					
					jQuery("#tr_hide_animate_scale_method").hide();
				   
			    }	
			    else if (input=='slide')
			    {
					jQuery("#tr_hide_animate_blind_direction").hide();
					jQuery("#tr_hide_animate_drop_direction").hide();
					jQuery("#tr_hide_animate_slide_direction").show();
					jQuery("#tr_hide_animate_clip_direction").hide();
					jQuery("#tr_hide_animate_scale_direction").hide();
				    jQuery('#tr_hide_animate_duration').show();
					jQuery('#tr_hide_animate_fold').hide();					
					jQuery('#tr_hide_animate_fold_method').hide();	
					jQuery('#tr_hide_animate_size').hide();
					jQuery("#tr_hide_animate_origin").hide();					
					jQuery("#tr_hide_animate_scale_method").hide();
				   
			    }	
				else if (input=='fade')
			    {
					jQuery("#tr_hide_animate_blind_direction").hide();
					jQuery("#tr_hide_animate_drop_direction").hide();
					jQuery("#tr_hide_animate_slide_direction").hide();
					jQuery("#tr_hide_animate_clip_direction").hide();
					jQuery("#tr_hide_animate_scale_direction").hide();
					jQuery('#tr_hide_animate_duration').show();
					jQuery('#tr_hide_animate_fold').hide();
					jQuery('#tr_hide_animate_fold_method').hide();	
					jQuery('#tr_hide_animate_size').hide();
					jQuery("#tr_hide_animate_origin").hide();					
					jQuery("#tr_hide_animate_scale_method").hide();
				   
			    }	
				else if (input=='scale')
			    {
					jQuery("#tr_hide_animate_blind_direction").hide();
					jQuery("#tr_hide_animate_drop_direction").hide();
					jQuery("#tr_hide_animate_slide_direction").hide();
					jQuery("#tr_hide_animate_clip_direction").hide();
					jQuery("#tr_hide_animate_scale_direction").show();
					jQuery('#tr_hide_animate_fold_method').hide();	
					jQuery('#tr_hide_animate_size').hide();	
					jQuery("#tr_hide_animate_origin").show();					
					jQuery("#tr_hide_animate_scale_method").show();
					jQuery("#tr_hide_animate_direction").show();
				   
			    }
				else if (input=='puff')
			    {
					jQuery("#tr_hide_animate_blind_direction").hide();
					jQuery("#tr_hide_animate_drop_direction").hide();
				    jQuery("#tr_hide_animate_slide_direction").hide();
					jQuery("#tr_hide_animate_clip_direction").hide();
					jQuery("#tr_hide_animate_scale_direction").hide();
				    jQuery('#tr_hide_animate_duration').show();
					jQuery('#tr_hide_animate_fold_method').hide();	
					jQuery('#tr_hide_animate_size').hide();
					jQuery("#tr_hide_animate_origin").hide();					
					jQuery("#tr_hide_animate_scale_method").hide();
				   
			    }
				else if (input=='fold')
			    {
					jQuery("#tr_hide_animate_blind_direction").hide();
					jQuery("#tr_hide_animate_drop_direction").hide();
					jQuery("#tr_hide_animate_slide_direction").hide();
					jQuery("#tr_hide_animate_clip_direction").hide();
					jQuery("#tr_hide_animate_scale_direction").hide();
					jQuery('#tr_hide_animate_duration').show();
					jQuery('#tr_hide_animate_fold_method').show();	
					jQuery('#tr_hide_animate_size').show();	
					jQuery("#tr_hide_animate_origin").hide();					
					jQuery("#tr_hide_animate_scale_method").hide();
				   
			    }	
				else if (input=='clip')
			    {
					jQuery("#tr_hide_animate_blind_direction").hide();
					jQuery("#tr_hide_animate_drop_direction").hide();
					jQuery("#tr_hide_animate_slide_direction").hide();
					jQuery("#tr_hide_animate_clip_direction").show();
					jQuery("#tr_hide_animate_scale_direction").hide();
					jQuery('#tr_hide_animate_duration').show();
					jQuery('#tr_hide_animate_fold_method').hide();	
					jQuery('#tr_hide_animate_size').hide();	
					jQuery("#tr_hide_animate_origin").hide();					
					jQuery("#tr_hide_animate_scale_method").hide();
				   
			    }			      
				else
				{
					jQuery("#tr_hide_animate_blind_direction").hide();
					jQuery("#tr_hide_animate_drop_direction").hide();
					jQuery("#tr_hide_animate_direction").hide();	
					jQuery('#tr_hide_animate_duration').show();
					jQuery('#tr_hide_animate_fold_method').hide();	
					jQuery('#tr_hide_animate_size').hide();						
					jQuery("#tr_hide_animate_origin").hide();					
					jQuery("#tr_hide_animate_scale_method").hide();
				}			   
			});
			
			jQuery("#advnote_select_animate_after_effect").live("change", function(){	
			    var input = jQuery(this).val();
			    if (input=='bounce')
			    {
					jQuery("#tr_after_effect_animate_bounce_direction").show();
					jQuery("#tr_after_effect_animate_bounce_distance").show();
				   	jQuery("#tr_after_effect_animate_shake_direction").hide();
				   	jQuery("#tr_after_effect_animate_shake_distance").hide();
					jQuery("#tr_after_effect_animate_times").show();
					jQuery("#tr_after_effect_animate_highlight_color").hide();					
				   
			    }	
				else if (input=='highlight')
			    {
					jQuery("#tr_after_effect_animate_bounce_direction").hide();
				   	jQuery("#tr_after_effect_animate_bounce_distance").hide();
				   	jQuery("#tr_after_effect_animate_shake_direction").hide();
				   	jQuery("#tr_after_effect_animate_shake_distance").hide();
					jQuery("#tr_after_effect_animate_highlight_color").show();	
					jQuery("#tr_after_effect_animate_times").hide();
				   
			    }	
				else if (input=='pulsate')
			    {				
					jQuery("#tr_after_effect_animate_bounce_direction").hide();
				   	jQuery("#tr_after_effect_animate_bounce_distance").hide();
				   	jQuery("#tr_after_effect_animate_shake_direction").hide();
				   	jQuery("#tr_after_effect_animate_shake_distance").hide();
					jQuery("#tr_after_effect_animate_times").show();
					jQuery("#tr_after_effect_animate_highlight_color").hide();	
			    }	
				else if (input=='shake')
			    {		
				   	jQuery("#tr_after_effect_animate_bounce_direction").hide();
				   	jQuery("#tr_after_effect_animate_bounce_distance").hide();
				   	jQuery("#tr_after_effect_animate_shake_direction").show();
				   	jQuery("#tr_after_effect_animate_shake_distance").show();
					jQuery("#tr_after_effect_animate_times").show();
					jQuery("#tr_after_effect_animate_highlight_color").hide();	
			    }
				else if (input=='size')
			    {		
				   
			    }	
			   
			});
					
					
		});
		</script>
		<?php
	}
	add_action('admin_footer', 'advnote_admin_footer');
?>