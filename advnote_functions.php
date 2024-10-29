<?php

//**********USER ROLE INFO******************/
if ( !function_exists('wp_get_current_user') ) 
{
	require_once (ABSPATH . WPINC . '/pluggable.php');
}

//
add_action( 'ws_plugin__qcache_before_add_advanced', 'advnote_init' );
add_action('init','advnote_init');
function advnote_init()
{
	//$test=1;
	//echo 1; exit;
	global $current_url;
	//echo $_COOKIE['advnote_closed'];exit;
	
	if ( (!strstr($current_url,'wp-admin')&&!strstr($current_url,'wp-login')&&$_COOKIE['advnote_closed']!=1&&$_SESSION['advnote_closed']!=1)||$test==1)
	{
		//echo $_SESSION['advnote_closed'];exit; 
		//echo 1;exit;
		global $table_prefix;
		global $current_user;
		global $wordpress_url;
		global $current_url;
		$visitor_ip = $_SERVER['REMOTE_ADDR'];		
		$wordpress_date_time = date('m-d-Y');

		get_currentuserinfo();	
		//var_dump($current_user);exit;
		
		if ( ! $current_user->data)
		{
			$logged_in = 0 ;
		}
		else
		{
			$logged_in = 1;
		}

		//$permit = advnote_check
		
		//*********URL INFO********************/
		$wordpress_url = get_bloginfo('url');
		if (substr($wordpress_url, -1, -1)!='/')
		{
			$wordpress_url = $wordpress_url."/";
		}
		//$wordpress_url = str_replace('www.','',$wordpress_url);
		
		
		
		//*********POST INFO*******************/
		$post_id = url_to_postid($current_url);
		if (!$post_id){	$post_id = advnote_url_to_postid($current_url); }
		if (!$post_id)
		{
			//check if homepage and if there is redirect profile setup for homepage
			if (str_replace('/','',$current_url)==str_replace('/','',$wordpress_url))
			{
				$is_home = 1;
				//echo "home";exit;
			}
			else
			{
				//determin if tag or archive page
				$is_archive =1;
			}
		}
		else
		{
			//determine post type
			$this_post_type = get_post_type($post_id);
		}
		//echo $post_id;exit;
		
		/* Retrieve the global settings */
		$query = "SELECT * FROM {$table_prefix}advnote_notifications WHERE status=1 ";
		$result = mysql_query($query);
		if (!$result) { echo $query;  echo mysql_error(); exit;}
		//echo mysql_num_rows($result);
		while($arr = mysql_fetch_array($result))
		{	
			$today = date('Y-m-s');
			$nid = $arr['post_id'];	
			$rules = $arr['rules'];	
			$styling = $arr['styling'];	
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

			//check if applies to user first
			$pass = advnote_current_user_has_at_least($advnote_userroles_permit,$advnote_userroles_prevent,$advnote_useragent_prevent,$logged_in);
			
			
			if ($pass==1)
			{
			
				$pass = advnote_is_permitted_posttype($advnote_post_types, $post_id,$this_post_type,$is_archive,$is_home);
				
				if ($pass==1)
				{
					$pass = 0;
					if ($advnote_expire_by=='date'&&($advnote_expire_by_date>$wordpress_date_time))
					{
					
						$pass =1;
					}
					else if ($advnote_expire_by=='visitors'&&($advnote_expire_by_visitors<$advnote_expire_by_visitors_count))
					{
						$pass =1;
					}
					else if ($advnote_expire_by=='eternal')
					{
						$pass = 1;
					}
					
					if ($pass==1)
					{
						//echo 1;exit;
						//now check for permissions at the individual level
						if ($advnote_persistance=='once')
						{
							//echo 1;exit;
							$q = "SELECT * FROM {$table_prefix}advnote_ip WHERE ip = '$visitor_ip' AND notification_id='$nid' ";
							$r = mysql_query($q);
							if (!$r){ echo $q; echo mysql_error(); exit;}
							$count = mysql_num_rows($r);
							if ($count<1)
							{
								//echo 2; exit;
								$pass=1;
								//$q = "INSERT {$table_prefix}advnote_ip ( ip, notification_id, count, rules ) VALUES ('$visitor_ip','$nid','1','$wordpress_date_time')";
								//$r = mysql_query($q);
								//if (!$r){ echo $q; echo mysql_error(); exit;}
								//$new=1;
							}
							else
							{
								//echo 1; exit;
								$pass = 0;
								$arr = mysql_fetch_array($r);
								$this_id = $arr['id'];
								$this_date = $arr['rules'];
								$this_count = $arr['count'] + 1;
								if ($advnote_persistance_reset=='pageviews')
								{
									if ($this_count<$advnote_persistance_reset_rule)
									{
										$q = "UPDATE {$table_prefix}advnote_ip SET count=count+1  WHERE ip='{$visitor_ip}' and notification_id='{$nid}'";
										$r = mysql_query($q);
										if (!$r){ echo $q; echo mysql_error(); exit;} 
									}
									else
									{
										$q = "DELETE FROM {$table_prefix}advnote_ip WHERE id='{$nid}'";
										$r = mysql_query($q);
										if (!$r){ echo $q; echo mysql_error(); exit;} 
										$pass=1;
									}
								}
								else if ($advnote_persistance_reset=='days')
								{
									$date_placeholder = date ('Y-m-d', strtotime ("$this_date + $advnote_persistance_reset_rule day"));
									if ($date_placeholder<$wordpress_date_time)
									{
										$q = "DELETE FROM {$table_prefix}advnote_ip WHERE ip='{$visitor_ip}' and notification_id='{$nid}'";
										$r = mysql_query($q);
										if (!$r){ echo $q; echo mysql_error(); exit;} 
										$pass=1;
									}		
									else
									{
										//echo 1; exit;
										$q = "UPDATE {$table_prefix}advnote_ip SET count=count+1  WHERE ip='{$visitor_ip}' and notification_id='{$nid}'";
										$r = mysql_query($q);
										if (!$r){ echo $q; echo mysql_error(); exit;} 
									}
								}
							}
						}
						else
						{
							$pass=1;
						}
						
						if ($pass==1)
						{
//echo 1;
							define('ADVNOTE_ENABLED', $nid );
						
							$p_rules = json_decode($styling,1);	
//print_r($p_rules);							
							$advnote_theme = $p_rules['theme'];
							
							define('ADVNOTE_DELAY_NATURE', $p_rules['delay_nature'] );
							define('ADVNOTE_DELAY_DELAY', $p_rules['delay_delay'] );
							//echo ADVNOTE_DELAY_DELAY;exit;
							define('ADVNOTE_DELAY_SCROLLPOINT_COORDINATE', $p_rules['delay_scrollpoint_coordinate'] );
							define('ADVNOTE_DELAY_SCROLLPOINT_ELEMENT', $p_rules['delay_scrollpoint_element'] );
							define('ADVNOTE_DELAY_SCROLLPOINT_FEATURES', $p_rules['delay_scrollpoint_features'] );
							define('ADVNOTE_WIDTH', $p_rules['width'] );
							define('ADVNOTE_HEIGHT', $p_rules['height'] );					
							define('ADVNOTE_CLOSABLE', $p_rules['closable'] );
							define('ADVNOTE_DRAGGABLE', $p_rules['draggable'] );
							$advnote_placement =  $p_rules['placement'];
							define('ADVNOTE_POSITION', $advnote_placement );					
							define('ADVNOTE_MODAL', $p_rules['modal'] );
							define('ADVNOTE_MODAL_SCREEN_COLOR', $p_rules['modal_screen_color'] );
							define('ADVNOTE_RESIZABLE', $p_rules['resizable'] );
							define('ADVNOTE_SHOW_TITLE', $p_rules['show_title'] );
							define('ADVNOTE_ANIMATE_SHOW_HIDE', $p_rules['animate_show_hide'] );
							define('ADVNOTE_ANIMATE_SHOW_EFFECT', $p_rules['animate_show_effect'] );
							define('ADVNOTE_ANIMATE_HIDE_EFFECT', $p_rules['animate_hide_effect'] );	
							
							define('ADVNOTE_ANIMATE_SHOW_EFFECT_DURATION', $p_rules['animate_show_effect_duration'] );
							define('ADVNOTE_ANIMATE_HIDE_EFFECT_DURATION', $p_rules['animate_hide_effect_duration'] );
							define('ADVNOTE_ANIMATE_SHOW_EFFECT_DIRECTION', $p_rules['animate_show_effect_direction'] );
							define('ADVNOTE_ANIMATE_HIDE_EFFECT_DIRECTION', $p_rules['animate_hide_effect_direction'] );
							define('ADVNOTE_ANIMATE_HIDE_EFFECT_FOLD_METHOD', $p_rules['animate_hide_effect_fold_method'] );
							define('ADVNOTE_ANIMATE_SHOW_EFFECT_FOLD_METHOD', $p_rules['animate_show_effect_fold_method'] );
							define('ADVNOTE_ANIMATE_SHOW_EFFECT_SIZE', $p_rules['animate_show_effect_size'] );
							define('ADVNOTE_ANIMATE_HIDE_EFFECT_SIZE', $p_rules['animate_hide_effect_size'] );
							
							
							define('ADVNOTE_ANIMATE_SHOW_EFFECT_ORIGIN', $p_rules['animate_show_effect_origin'] );
							define('ADVNOTE_ANIMATE_SHOW_EFFECT_SCALE_METHOD', $p_rules['animate_show_effect_scale_method'] );					
							define('ADVNOTE_ANIMATE_HIDE_EFFECT_ORIGIN', $p_rules['animate_hide_effect_origin'] );
							define('ADVNOTE_ANIMATE_HIDE_EFFECT_SCALE_METHOD', $p_rules['animate_hide_effect_scale_method'] );
							
							
							define('ADVNOTE_ANIMATE_AFTER_EFFECT', $p_rules['animate_after_effect'] );
							define('ADVNOTE_ANIMATE_AFTER_EFFECT_EFFECT', $p_rules['animate_after_effect_effect'] );
							define('ADVNOTE_ANIMATE_AFTER_EFFECT_DELAY', $p_rules['animate_after_effect_delay'] );
							define('ADVNOTE_ANIMATE_AFTER_EFFECT_DISTANCE', $p_rules['animate_after_effect_distance'] );
							define('ADVNOTE_ANIMATE_AFTER_EFFECT_DURATION', $p_rules['animate_after_effect_duration'] );
							define('ADVNOTE_ANIMATE_AFTER_EFFECT_DIRECTION', $p_rules['animate_after_effect_direction'] );
							define('ADVNOTE_ANIMATE_AFTER_EFFECT_TIMES', $p_rules['animate_after_effect_times'] );
							
							
							//echo $advnote_placement;exit;
							//echo ADVNOTE_DELAY_NATURE;exit;
							if (ADVNOTE_DELAY_NATURE=='scrollpoint_coordinate')
							{
								//echo 1; exit;
								add_action('wp_head','advnote_scroll_fire');
							}
							
							if ($advnote_placement=='top')
							{
								//echo 1; exit;
								add_action('wp_head','advnote_top_bottom_of_page');
							}
												
							if ($advnote_placement=='bottom')
							{
								add_action('wp_footer','advnote_top_bottom_of_page');	
							}
							
							if ($advnote_placement=='center')
							{
								add_action('wp_footer','advnote_top_bottom_of_page');	
							}
							
							if (strstr($advnote_placement,"["))
							{
								add_action('wp_footer','advnote_top_bottom_of_page');	
							}
							
							if ($advnote_placement=='below_nav')
							{
								add_filter('loop_start','advnote_above_below_post');
							}
							
							if ($advnote_placement=='above_post')
							{
								//echo 1; exit;
								add_filter('loop_start','advnote_above_below_post');
								//add_filter('the_title','advnote_above_post');
							}
							
							if ($advnote_placement=='php')
							{
								//echo 1; exit;
								//add_filter('loop_start','advnote_php');
								//add_filter('the_title','advnote_above_post');
							}
							
							if ($advnote_placement=='below_post')
							{
								add_filter('loop_end','advnote_above_below_post');
							}
							
							
							wp_register_style( 'custom_jquery_ui_css', "".ADVANCEDNOTIFICATIONS_URLPATH."themes/{$advnote_theme}/jquery-ui.custom.css" );
							wp_enqueue_style( 'custom_jquery_ui_css' );
							add_action('wp_head','advnote_add_jquery');
							
							return;
						}
					}
				}
			}
		}
	}
	else
	{
		//echo 1; exit;
	}
	
}

function advnote_add_jquery()
{
	if (defined('ADVNOTE_ENABLED'))
	{
		global $table_prefix;

		/* Retrieve the post content */
		$query = "SELECT * FROM {$table_prefix}posts WHERE ID='".ADVNOTE_ENABLED."'";
		$result = mysql_query($query);
		if (!$result) { echo $query;  echo mysql_error(); exit;}
		
		$array = mysql_fetch_array($result);
		$title = str_replace('"',"'", $array['post_title']);
		$content = str_replace('"',"'", $array['post_content']);
		if (strstr($content,'<!'))
		{
			$content = preg_replace('/\<\!(.*?)\>/s',"", $content);
		}
		$content = str_replace(array("\r", "\r\n", "\n"), ' ', $content);
		//str_replace("_nl_","\n",$_SESSION['wpt_ad_content_1']);
		


?>		
				
<style  type="text/css" >
.class_advnote_position
{
	position: fixed;
	<?php
	if (ADVNOTE_POSITION=='center')
	{
	?>						
	top: 50%;
	left: 50%;
	<?php	
	}				
	?>
}
.ui-widget-overlay{
	 background:<?php echo ADVNOTE_MODAL_SCREEN_COLOR; ?>;
}
</style>
				
	
<script type='text/javascript'>
jQuery(document).ready(function () {
	var t1;
	var t2;
	var ts;
	
	<?php
	if (ADVNOTE_POSITION!='above_post'&&ADVNOTE_POSITION!='below_post'&&ADVNOTE_POSITION!='php')
	{
	?>
	//populate dialog
	var dialog_element = jQuery('#notice_top_bottom_page');
	dialog_element.attr('title', "<?php echo $title; ?>");
	dialog_element.append("<?php echo str_replace(array('<script','</script>'), array('<scr"+"ipt', '</scr"+"ipt>'), $content); ?>");
	<?php
	}
	else
	{
	?>
	var dialog_element = jQuery('#notice_above_below_post');
	dialog_element.attr('title',"<?php echo $title; ?>");
	dialog_element.append("<?php echo str_replace(array('<script','</script>'), array('<scr"+"ipt', '</scr"+"ipt>'), $content); ?>");
	var ow = jQuery('.class_notification_container').outerWidth();
	var oh = jQuery('.class_notification_container').outerHeight();
	var po = jQuery('.class_notification_container').position();
	<?php
	}
	?>
	
	function isScrolledIntoView(elem)
	{						
		var docViewTop = jQuery(window).scrollTop();
		var docViewBottom = docViewTop + jQuery(window).height();

		var elemTop = jQuery(elem).offset().top;
		var elemBottom = elemTop + jQuery(elem).height();

		return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
	}
	
	<?php 
	if (strstr(ADVNOTE_DELAY_NATURE,'scrollpoint'))
	{
		if (ADVNOTE_DELAY_SCROLLPOINT_FEATURES==0)
		{
		?>loaded = 0;
		start = 0;
		reversable=0;
		ao = false;
		<?php
		}
		else if (ADVNOTE_DELAY_SCROLLPOINT_FEATURES==1)
		{
		?>loaded = 0;
		start = 0;
		reversable = 1;
		ao = false;
		<?php
		}
		else if (ADVNOTE_DELAY_SCROLLPOINT_FEATURES==2)
		{
		?>loaded = 1;
		start = 1;
		reversable = 0;
		ao = false;
		<?php
		}
		else if (ADVNOTE_DELAY_SCROLLPOINT_FEATURES==3)
		{
		?>loaded = 1;
		start = 1;
		reversable = 1;
		ao = false;
		<?php
		}
	?>
		
		didScroll = false;
		stay_closed = 0;

		jQuery(window).scroll(function() {
			didScroll = true;
		});
		
		setInterval(function() {
			if ( didScroll ) {
				didScroll = false;

				<?php
				if (ADVNOTE_DELAY_NATURE=='scrollpoint_coordinate')
				{
				?>var scrollpoint = jQuery(".class_scroll_waypoint");
				<?php
				}
				else
				{
				?>var scrollpoint = jQuery(".<?php echo ADVNOTE_DELAY_SCROLLPOINT_ELEMENT; ?>");
				<?php
				}
				?>
				
				var bool = isScrolledIntoView(scrollpoint);
				
				//alert(start);
				//alert(loaded);				
				//alert (bool);
				if (((start==0&&loaded==0&&bool==1)||(start==1&&loaded==0&&bool==0&&reversable==1))&&stay_closed==0)
				{			
					//alert(loaded);
					ao = true;
					create_dialog();
					loaded = 1;
				}
				else if ((bool==0&&start==0&&reversable==1)||bool==1&&start==1)
				{
					
					if (loaded==1)
					{
						//alert('here');
						jQuery('#notice_top_bottom_page').stop(true,true);
						jQuery('#notice_top_bottom_page').dialog("close");		
						jQuery('.class_advnote_position').dialog("close");		
						loaded=0;
					}
				}
			}
		},500);
		
		if (ao==true)
		{
			t1 = setTimeout(create_dialog, <?php echo ADVNOTE_DELAY_DELAY; ?>);
		}
	<?php
	}
	else
	{	
		?>
		ao = true;
		t1 = setTimeout(create_dialog, <?php echo ADVNOTE_DELAY_DELAY; ?>);
		<?php
	}
	?>
	
	
	function create_dialog()
	{			
		//alert("one");
		var dialog = dialog_element.dialog({
		autoOpen: ao,			
		dialogClass: 'class_advnote_position',
		<?php
		if (strstr(ADVNOTE_POSITION,'['))
		{
		?>position: <?php echo ADVNOTE_POSITION; ?>,
		width:'<?php echo ADVNOTE_WIDTH; ?>',
		height: '<?php echo ADVNOTE_HEIGHT; ?>',
		maxHeight: '<?php echo ADVNOTE_HEIGHT; ?>',
		<?php
		}
		else if (ADVNOTE_POSITION=='bottom'||ADVNOTE_POSITION=='top')
		{
		?>position: "<?php echo ADVNOTE_POSITION; ?>",
		width:'<?php echo ADVNOTE_WIDTH; ?>',
		height: '<?php echo ADVNOTE_HEIGHT; ?>',
		maxHeight: '<?php echo ADVNOTE_HEIGHT; ?>',
		minHeight: '<?php echo ADVNOTE_HEIGHT; ?>',
		<?php
		}
		else if (ADVNOTE_POSITION=='php')
		{
		?>
		position: [0,0],
		width:'<?php echo ADVNOTE_WIDTH; ?>',
		height: '<?php echo ADVNOTE_HEIGHT; ?>',
		maxHeight: '<?php echo ADVNOTE_HEIGHT; ?>',
		minHeight: '<?php echo ADVNOTE_HEIGHT; ?>',
		<?php
		}	
		else if (ADVNOTE_POSITION=='above_post'||ADVNOTE_POSITION=='below_post')
		{
		?>
		width: ow,
		position: [0,0],
		<?php
		}	
		if (ADVNOTE_ANIMATE_SHOW_HIDE=='true')
		{
		?>  show: {		
				
				effect: "<?php echo ADVNOTE_ANIMATE_SHOW_EFFECT; ?>",									
				size: "<?php echo ADVNOTE_ANIMATE_SHOW_EFFECT_SIZE; ?>", 
				origin: <?php echo ADVNOTE_ANIMATE_SHOW_EFFECT_ORIGIN; ?>, 
				horizFirst: "<?php echo ADVNOTE_ANIMATE_SHOW_EFFECT_FOLD_METHOD; ?>", 
				direction: "<?php echo ADVNOTE_ANIMATE_SHOW_EFFECT_DIRECTION; ?>",
				duration: <?php echo ADVNOTE_ANIMATE_SHOW_EFFECT_DURATION; ?>
				
				}, 
			hide: 
			{
				effect: "<?php echo ADVNOTE_ANIMATE_HIDE_EFFECT; ?>", 
				size: "<?php echo ADVNOTE_ANIMATE_HIDE_EFFECT_SIZE; ?>", 
				origin: <?php echo ADVNOTE_ANIMATE_HIDE_EFFECT_ORIGIN; ?>, 
				horizFirst: "<?php echo ADVNOTE_ANIMATE_HIDE_EFFECT_FOLD_METHOD; ?>", 
				direction: "<?php echo ADVNOTE_ANIMATE_HIDE_EFFECT_DIRECTION; ?>", 
				duration: <?php echo ADVNOTE_ANIMATE_HIDE_EFFECT_DURATION; ?>
			}, 
		<?php
		}
		?>
		draggable: <?php echo ADVNOTE_DRAGGABLE; ?>,
		resizable: <?php echo ADVNOTE_RESIZABLE; ?>,
		closable: <?php echo ADVNOTE_CLOSABLE; ?>,
		modal: <?php echo ADVNOTE_MODAL; ?>,
		zIndex:9999999,
		//open: function(event, ui) {
		//	$(this).css({'max-height': <?php echo ADVNOTE_HEIGHT; ?>, 'overflow-y': 'auto'}); 
		//}
		});					
		
		jQuery('.ui-dialog').ready(function() {
			//dialog.css('height','100px');
			<?php
			if (ADVNOTE_POSITION=='above_post'||ADVNOTE_POSITION=='below_post'||ADVNOTE_POSITION=='php')
			{
			?>
			//alert("here");
			jQuery(".notice_above_below_post").css("display","block");
			jQuery(".class_notification_container").append(jQuery(".ui-dialog"));
			jQuery(".ui-dialog").css('position','relative');
			jQuery(".class_advnote_position").css('postion','relative');
			jQuery(".class_advnote_position").css('z-index','0');
			//jQuery(".ui-dialog").css('top','0');
			<?php
			}
			if (ADVNOTE_ANIMATE_AFTER_EFFECT=='true')
			{
			?>
			t2 = setTimeout(animate_execute, <?php echo ADVNOTE_ANIMATE_AFTER_EFFECT_DELAY; ?>);
			function animate_execute()
			{
				//alert("hi");
				//"<?php echo ADVNOTE_ANIMATE_AFTER_EFFECT_EFFECT; ?>" ,
				jQuery('.ui-dialog').effect("<?php echo ADVNOTE_ANIMATE_AFTER_EFFECT_EFFECT; ?>",{
					times: <?php echo ADVNOTE_ANIMATE_AFTER_EFFECT_TIMES; ?>, 
					direction:"<?php echo ADVNOTE_ANIMATE_AFTER_EFFECT_DIRECTION; ?>", 
					distance: <?php echo ADVNOTE_ANIMATE_AFTER_EFFECT_DISTANCE; ?>, 
					duration: <?php echo ADVNOTE_ANIMATE_AFTER_EFFECT_DURATION; ?>
				});
				//alert("hi");
			}
			<?php
			}
			?>
		});

		
		jQuery('.ui-icon-closethick').bind('click', function(event) {
			//alert("here");
			 clearTimeout(t2);
			 dialog.stop(true,false);
			 jQuery.ajax({
				   url: "<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>ajax.php?nid=<?php echo ADVNOTE_ENABLED; ?>"
			});
			stay_closed=1;
			
		 });
		
		<?php 
		if (ADVNOTE_SHOW_TITLE!='true')
		{
			//echo 1; exit;
			//echo 'jQuery(".ui-dialog-title").hide();\n';
			echo "jQuery('.ui-dialog-titlebar').css('display','none');\n";
			//echo "jQuery('.ui-widget-header').css('display','none');\n";
		}
		?>
		
		return dialog;
	}
	
	
	jQuery('#id_close_notification').live("click",function(){
		jQuery('.class_advnote_position').dialog('close');
		jQuery('.class_notification_container').css('display','none');		
		jQuery.ajax({
			   url: "<?php echo ADVANCEDNOTIFICATIONS_URLPATH;?>ajax.php?nid=<?php echo ADVNOTE_ENABLED; ?>"
		});
		stay_closed=1;
		//alert("testing!");
	});
	
});		
	
</script>
	<?php		
	}
}

function advnote_scroll_fire()
{
	?>
		<div class='class_scroll_waypoint' style='position:absolute;top:<?php echo ADVNOTE_DELAY_SCROLLPOINT_COORDINATE; ?>px;z-index:-1;'></div>	
	<?php
}

function advnote_php_placement()
{
	//echo "<div class='notice_above_below_post' id='notice_above_below_post'></div>";
	$content = "<div class='class_notification_container' style=''>";
	$content .= "<div class='notice_above_below_post' id='notice_above_below_post' style='display:none;'></div>";
	$content .= "</div>";
	echo $content;
}

function advnote_above_post($title)
{
	//echo "<div class='notice_above_below_post' id='notice_above_below_post'></div>";
	$prepend = "<div class='class_notification_container' style=''>";
	$prepend .= "<div class='notice_above_below_post' id='notice_above_below_post' style='display:none;'></div>";
	$prepend .= "</div>";
	$title = $prepend.$title;
	return $title;
}

function advnote_above_below_post()
{
	
	?>
	<div class='class_notification_container' style=''>
		<?php
		//<div class="ui-dialog ui-widget ui-widget-content ui-corner-all class_advnote_position ui-resizable" id='notice_above_below_post' style='display:inline-table;margin-bottom:20px;'>
		 // <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix" id ='notification_title' ></div>
		 // <div class="notice_top_bottom_page ui-dialog-content ui-widget-content" id= 'notification_content' style='display:inline;position:relative;'>

		 // </div>
		//</div>
		?>
		<?php
		echo "<div class='notice_above_below_post' id='notice_above_below_post' style='display:none;'></div>";
		?>
	</div>
	
	<?php
}

function advnote_top_bottom_of_page()
{
	//echo 1; exit;
	//<script type='text/javascript'>	
	//jQuery(document).ready( function($) {
		//jQuery('body').prepend("<div class='notice_top_bottom_page' id='notice_top_bottom_page' style='display:none;'>hello</div>");
	//} );
	//</script>

	echo "<div class='notice_top_bottom_page' id='notice_top_bottom_page' style='display:none;'></div>";
	//return $content;
}



function advnote_current_user_has_at_least( $permit,$prevent,$useragent_prevent,$logged_in) {
	//global $the_roles;
	$useragent_prevent = explode(',',$useragent_prevent);
	$useragent_prevent = array_filter($useragent_prevent);
	$prevent = array_filter($prevent);
	$permit = array_filter($permit);
	
	if ($useragent_prevent)
	{
		$agent = $_SERVER['HTTP_USER_AGENT'];
		foreach($useragent_prevent as $val)
		{
			if (stristr($agent,$val))
			{
				return false;
			}
		}
	}
	
	if ($prevent)
	{
		foreach ( $prevent as $the_role ) {
			if ($logged_in==1&&$the_role=='unregistered')
			{
				return false;
			}			
			else if ( current_user_can( $the_role ) )
			{
				return false;
			}
		}
	}

	if ($permit)
	{
		foreach ( $permit as $the_role ) {
			if ($logged_in==0&&$the_role=='unregistered')
			{
				return true;
			}
			else if ( current_user_can( $the_role ) )
				return true;
		}
	}
	return false;
}

function advnote_is_permitted_posttype($advnote_post_types, $post_id,$post_type,$is_archive,$is_home) 
{
	if ($post_id)
	{
		if (in_array($post_type,$advnote_post_types))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	else
	{
		if (in_array('archive',$advnote_post_types))
		{
			if ($is_archive)
			{
				return 1;
			}
		}
		
		if (in_array('home',$advnote_post_types))
		{
			if ($is_home)
			{
				return 1;
			}
		}		
			
		return 0;

	}
}

/* Post URLs to IDs function, supports custom post types - borrowed and modified from url_to_postid() in wp-includes/rewrite.php */
function advnote_url_to_postid($url)
{
	global $wp_rewrite;

	$url = apply_filters('url_to_postid', $url);

	// First, check to see if there is a 'p=N' or 'page_id=N' to match against
	if ( preg_match('#[?&](p|page_id|attachment_id)=(\d+)#', $url, $values) )	{
		$id = absint($values[2]);
		if ( $id )
			return $id;
	}

	// Check to see if we are using rewrite rules
	$rewrite = $wp_rewrite->wp_rewrite_rules();

	// Not using rewrite rules, and 'p=N' and 'page_id=N' methods failed, so we're out of options
	if ( empty($rewrite) )
		return 0;

	// Get rid of the #anchor
	$url_split = explode('#', $url);
	$url = $url_split[0];

	// Get rid of URL ?query=string
	$url_split = explode('?', $url);
	$url = $url_split[0];

	// Add 'www.' if it is absent and should be there
	if ( false !== strpos(home_url(), '://www.') && false === strpos($url, '://www.') )
		$url = str_replace('://', '://www.', $url);

	// Strip 'www.' if it is present and shouldn't be
	if ( false === strpos(home_url(), '://www.') )
		$url = str_replace('://www.', '://', $url);

	// Strip 'index.php/' if we're not using path info permalinks
	if ( !$wp_rewrite->using_index_permalinks() )
		$url = str_replace('index.php/', '', $url);

	if ( false !== strpos($url, home_url()) ) {
		// Chop off http://domain.com
		$url = str_replace(home_url(), '', $url);
	} else {
		// Chop off /path/to/blog
		$home_path = parse_url(home_url());
		$home_path = isset( $home_path['path'] ) ? $home_path['path'] : '' ;
		$url = str_replace($home_path, '', $url);
	}

	// Trim leading and lagging slashes
	$url = trim($url, '/');

	$request = $url;
	// Look for matches.
	$request_match = $request;
	foreach ( (array)$rewrite as $match => $query) {
		// If the requesting file is the anchor of the match, prepend it
		// to the path info.
		if ( !empty($url) && ($url != $request) && (strpos($match, $url) === 0) )
			$request_match = $url . '/' . $request;

		if ( preg_match("!^$match!", $request_match, $matches) ) {
			// Got a match.
			// Trim the query of everything up to the '?'.
			$query = preg_replace("!^.+\?!", '', $query);

			// Substitute the substring matches into the query.
			$query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

			// Filter out non-public query vars
			global $wp;
			parse_str($query, $query_vars);
			$query = array();
			foreach ( (array) $query_vars as $key => $value ) {
				if ( in_array($key, $wp->public_query_vars) )
					$query[$key] = $value;
			}

		// Taken from class-wp.php
		foreach ( $GLOBALS['wp_post_types'] as $post_type => $t )
			if ( $t->query_var )
				$post_type_query_vars[$t->query_var] = $post_type;

		foreach ( $wp->public_query_vars as $wpvar ) {
			if ( isset( $wp->extra_query_vars[$wpvar] ) )
				$query[$wpvar] = $wp->extra_query_vars[$wpvar];
			elseif ( isset( $_POST[$wpvar] ) )
				$query[$wpvar] = $_POST[$wpvar];
			elseif ( isset( $_GET[$wpvar] ) )
				$query[$wpvar] = $_GET[$wpvar];
			elseif ( isset( $query_vars[$wpvar] ) )
				$query[$wpvar] = $query_vars[$wpvar];

			if ( !empty( $query[$wpvar] ) ) {
				if ( ! is_array( $query[$wpvar] ) ) {
					$query[$wpvar] = (string) $query[$wpvar];
				} else {
					foreach ( $query[$wpvar] as $vkey => $v ) {
						if ( !is_object( $v ) ) {
							$query[$wpvar][$vkey] = (string) $v;
						}
					}
				}

				if ( isset($post_type_query_vars[$wpvar] ) ) {
					$query['post_type'] = $post_type_query_vars[$wpvar];
					$query['name'] = $query[$wpvar];
				}
			}
		}

			// Do the query
			$query = new WP_Query($query);
			if ( !empty($query->posts) && $query->is_singular )
				return $query->post->ID;
			else
				return 0;
		}
	}
	return 0;
}

?>