<?php
/*
Plugin Name: Advanced Notifications Plugin
Plugin URI: http://www.hatnohat.com/go/advanced-notifications
Description: Create Advanvced Placement Notifications for your UserGroups.
Version: 1.023
Author: @atwellpub
Author URI: https://plus.google.com/115026361664097398228/
*/
define('ADVNOTE_CURRENT_VERSION', '1.023' );
//define("QUICK_CACHE_ALLOWED", false);
//define("DONOTCACHEPAGE", true);
//$_SERVER["QUICK_CACHE_ALLOWED"] = false;

//register jquery files
function advnote_init_enqueue() {
    if (is_admin()) {
        wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_register_style( 'custom_jquery_ui_css', ''.ADVANCEDNOTIFICATIONS_URLPATH.'jquery-ui-1.7.2.custom.css' );
		wp_enqueue_style( 'custom_jquery_ui_css' );
		
    }
	else
	{
		 wp_enqueue_script( 'jquery' );
		 //wp_register_script( 'jquery-cookie', ''.ADVANCEDNOTIFICATIONS_URLPATH.'jquery-cookie.js' );
		 //wp_enqueue_script( 'jquery-cookie' );
		 wp_deregister_script( 'jquery-ui-core');
		 wp_register_script( 'jquery-ui-core', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js' );
		 wp_enqueue_script( 'jquery-ui-core' );

	}
}

add_action('init', 'advnote_init_enqueue');

include_once('advnote_functions.php');
include_once('advnote_meta_boxes.php');
include_once('advnote_settings.php');

$download_location ="downloadurlhere";
$donation_location ="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TS7UVSTJQ5WG8";
$news_location = "http://www.hatnohat.com/forum/forumdisplay.php/91-Advanced-Notifications-Plugin%E2%84%A2";
$support_location = "http://www.hatnohat.com/forum/forumdisplay.php/91-Advanced-Notifications-Plugin%E2%84%A2";

if (!isset($_SESSION))session_start();
define('ADVANCEDNOTIFICATIONS_URLPATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ).'/' );
define('ADVANCEDNOTIFICATIONS_PATH', ABSPATH.'wp-content/plugins/advanced-notifications-lite/' );
define('ADVNOTE_PLUGIN_SLUG', 'advanced-notifications' );


/* Retrieve the global settings */
$query = "SELECT `option_value` FROM {$table_prefix}advnote_options WHERE option_name='advnote_options' ORDER BY id ASC";
$result = mysql_query($query);

if ($result)
{
	//echo $table_prefix;
	$array = mysql_fetch_array($result);
	$advnote_options = $array['option_value'];
	$advnote_options = str_replace("\r\n", "\n", $advnote_options);
    $advnote_options = str_replace("\r", "\n", $advnote_options);
    $advnote_options = str_replace("\n", "\\n", $advnote_options);
	$advnote_options = json_decode($advnote_options, true);
	//var_dump($advnote_options);exit;

	$global_advnote = $advnote_options['license_key'];
	$global_advnote_handle = $advnote_options['license_email'];
	$global_permission = $advnote_options['permission'];
	$current_version = $advnote_options['current_version'];
	if ($pm==0)
	{
		
	}
	//echo 1;
	//print_r($advnote_options);exit;

}
else
{
	//echo $query; echo mysql_error(); exit;
}

$wordpress_url = get_bloginfo('url');
if (substr($wordpress_url, -1, -1)!='/')
{
	$wordpress_url = $wordpress_url."/";
}
$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";

function advnote_str_replace_once($remove , $replace , $string)
{
    //$remove = str_replace('/','\/',$remove);
    $return = preg_replace('/'.preg_quote($remove,'/').'/', $replace, $string, 1);
	if (!$return)
	{
		echo "advnote_str_replace_once fail"; exit;
		echo "<br><br> Here is the string:<br><br>$string";  EXIT;
	}
    return $return;
}

function advnote_get_string_between($string, $start, $end)
{
	if (strstr($start,'%wildcard%'))
	{
		$start = str_replace("%wildcard%", ".*?", preg_quote($start, "/"));
	}
	else
	{
		$start = preg_quote($start, "/");
	}
	if (strstr($end,'%wildcard%'))
	{
		$end = str_replace("%wildcard%", ".*?", preg_quote($end, "/"));
	}
	else
	{
		//echo $end;exit;
		$end = preg_quote($end, "/");
		//echo $end; exit;
	}
    $regex = "/$start(.*?)$end/si";


    if (preg_match($regex, $string, $matches))
        return $matches[1];
    else
        return false;
}

function advnote_template_dropdown($name, $tid)
{
	$out = "";
	$posts = get_posts(
		array(
			'post_type'  => 'advnote_templates',
			'numberposts' => -1
		)
	);
	if($posts )
	{

		$out = '<select name="'.$name.'" ><option>Select a Template</option>';
		foreach( $posts as $p )
		{
			if ($p->ID==$tid)
			{
				$selected = "selected = 'true'";
			}
			else
			{
				$selected = "";
			}
			//echo $tid.":".$p->ID;
			//echo "<br>";
			$out .= '<option value="' . $p->ID . '" '.$selected.'>' .$p->post_title  . '</option>';
		}
		$out .= '</select>';
	}
	else
	{
		$out = '<select name="'.$name.'" value="x" ><option>Select a Template</option>';
		foreach( $posts as $p )
		{
			$out .= '<option value="x" '.$selected.'>None Created Yet.</option>';
		}
		$out .= '</select>';
	}
	return $out;
}

function advnote_activate()
{
	global $wpdb;
	
	
	if (function_exists('is_multisite') && is_multisite()) {
			$oldblog = $wpdb->blogid;
            $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
			$multisite = 1;
    }

	//print_r($blogids);exit;

	if (count($blogids)>1)
	{
		$count = count($blogids);
	}
	else
	{
		$count=1;
	}

	for ($i=0;$i<$count;$i++)
	{
		if ($multisite==1)
		{
			 switch_to_blog($blogids[$i]);
		}
		//echo $wpdb->prefix;
		//echo "<br>";
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}advnote_notifications (
				id INT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				post_id INT(20) NOT NULL,
				rules TEXT NOT NULL,	
				styling TEXT NOT NULL,
				status INT(2) NOT NULL
				) {$charset_collate};";

		$result = $wpdb->get_results($sql, ARRAY_A);
		
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}advnote_ip (
				id INT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				notification_id INT(20) NOT NULL,
				rules TEXT NOT NULL,
				ip VARCHAR(20) NOT NULL,
				active INT(12) NOT NULL,
				count INT(12) NOT NULL
				) {$charset_collate};";

		$result = $wpdb->get_results($sql, ARRAY_A);
		
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}advnote_options (
				id INT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				option_name VARCHAR(225) NOT NULL,
				option_value TEXT NOT NULL,
				status  INT(12) NOT NULL
				) {$charset_collate};";

		$result = $wpdb->get_results($sql, ARRAY_A);

		$advnote_options['license_key'] = "";
		$advnote_options['license_email'] = "";
		$advnote_options['current_version'] = ADVNOTE_CURRENT_VERSION;
		//$advnote_options['permissions'] = "1.1.1.1.1.1.1";
		//$advnote_options['popups_cookie_timeout'] = 7200;

		//print_r($advnote_options);exit;
		$advnote_options = json_encode($advnote_options);
		$sql = "INSERT  INTO {$wpdb->prefix}advnote_options (
		`id`,`option_name`,`option_value`,`status`)
		VALUES ('1','advnote_options','".mysql_real_escape_string($advnote_options)."','1')";
		$result = $wpdb->get_results($sql, ARRAY_A);
		//if (!$result) { echo $query; echo mysql_error(); exit;}
		
	
	}
	//exit;

	if ($multisite==1)
	{
		switch_to_blog($oldblog);
	}
}




function advnote_remote_connect($url)
{
	$method1 = ini_get('allow_url_fopen') ? "Enabled" : "Disabled";
	if ($method1 == 'Disabled')
	{
		//do curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "$url");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		$string = curl_exec($ch);
	}
	else
	{
		$string = file_get_contents($url);
	}

	return $string;
}

function advnote_activation_check()
{
	global $table_prefix;
	global $advnote_options; 
	global $wordpress_url; 
	

	$global_advnote = $advnote_options['license_key'];
	$global_advnote_handle = $advnote_options['license_email'];
	
	
	$parse = parse_url($wordpress_url);
	$domain = $parse['host'];
	$url = "http://www.hatnohat.com/api/advanced-notifications/validate.php?key={$advnote_options['license_key']}&email={$advnote_options['license_email']}&domain={$domain}";
	$return = advnote_remote_connect($url);
	
	if ($return=='1')
	{
		$advnote_options['permission'] =1;
		$advnote_options = json_encode($advnote_options);
			$sql = "INSERT  INTO {$wpdb->prefix}advnote_options (
			`id`,`option_name`,`option_value`,`status`)
			VALUES ('','advnote_options','".mysql_real_escape_string($advnote_options)."','1')";
			$result = $wpdb->get_results($sql, ARRAY_A);
		$pm = 1;
	}
	else
	{
		
		$pm = 0;
	}

}


function advnote_activate_prompt()
{
	$this_key = md5($wordpress_url.$_SERVER['REMOTE_ADDR']);
	//check for update
	echo "<center><img src='".ADVANCEDNOTIFICATIONS_URLPATH."images/advnote_logo.png'></center>";
	?>

	<form action='?page=advanced-notifications/advanced-notifications.php' id='id_form_activate_bcta' method='post'>
	<input type='hidden' name='nature' value='activate_bcta'>
	<center>
	<br><br>
	<table>
		<tr>
			<td  align=left style="font-size:13px;">
				License Key:
			</td>
			<td  align=left style="font-size:13px;">
				<input name=license_key size=30 value='<?php echo $key; ?>'>
			</td>
		</tr>
		<tr>
			<td  align=left style="font-size:13px;">
				License Email:
			</td>
			<td  align=left style="font-size:13px;">
				<input name=license_email size=30 value=''><br>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<br>
				<center>
					<div style='height:27px;'><a id="id_button_activate_bcta" class="button"  style="padding-left:51px;padding-right:53px;cursor:pointer;" >Activate Now</a></div>
				</center>
				<br>
				<center>
					<div style='height:90px;font-size:11px;'>Don't have a license key yet? <a id="id_button_activate_bcta" href='http://www.hatnohat.com/api/calls-to-action/register.php?key=<?php echo $this_key; ?>' target='_blank' >Register Here!</a></div>
				</center>
			</td>
		</tr>
	</table>
	</center>
	</form>
	<?php
}
function advnote_display_footer()
{
	global $download_location;
	global $donation_location;
	global $news_location;
	global $support_location;

	echo "<div class='advnote_footer'><a href='$donation_location' target='_blank'>Donate to Author</a> &nbsp;|&nbsp;";
	//echo " <a href='".ADVANCEDNOTIFICATIONS_URLPATH."advnote_update_sql.php?debug=1' target='_blank'>Repair Tables</a> &nbsp;|&nbsp;";
	//echo " <a href='".ADVANCEDNOTIFICATIONS_URLPATH."advnote_update.php' target='_blank'>Force Update</a> &nbsp;|&nbsp;";
	echo " <a href='$support_location' target='_blank'>Support</a> &nbsp;|&nbsp;";
}

function advnote_options_page()
{

	?>
		
	<?php
}

//$pm = explode(".",$advnote_options['permissions']);
//$global_advnote = "bettercallstoactionbeta";
//include_once('advnote_setup.php');

register_activation_hook(__FILE__, 'advnote_activate');


function advnote_add_menu()
{
	global $menu;
	global $table_prefix;
	global $pm;
	global $global_advnote;
	global $global_permission;
	//echo $pm;exit;

	if (current_user_can('manage_options')&&$global_permission!=1)
	{
		//add_menu_page( "Advanced Notifications", "Advanced Notifications",4,'edit.php?post_type=advnote_notifications', plugins_url('advanced-notifications/images/ico_horn.png'), '300');
		add_submenu_page('edit.php?post_type=notifications', 'Go Pro!', 'Go Pro!', 'edit_posts', __FILE__, 'advnote_settings');
	}
	else if ($global_permission==1)
	{
			add_submenu_page('edit.php?post_type=notifications', 'License Information!', 'License Information', 'edit_posts', __FILE__, 'advnote_settings');
	}
	//print_r($menu);
}

add_action('admin_menu', 'advnote_add_menu');



function advnote_admin_init() {
	$pluginfolder = get_bloginfo('url') . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__).'');
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-datepicker', $pluginfolder . '/js/jquery.ui.datepicker.min.js', array('jquery', 'jquery-ui-core') );
	wp_deregister_style( 'jquery.ui.theme' );
	wp_enqueue_style('jquery.ui.theme', $pluginfolder . '/themes/peach-top-light-background/jquery.ui.all.css');
}

add_action('admin_init','advnote_admin_init');
//register ADVANCEDNOTIFICATIONS Templates
function advnote_init_my_template() {

	register_taxonomy('notification_cat','notifications', array(
            'hierarchical' => true,
            'show_ui' => true,
            'query_var' => true,
    ));
	
	$labels = array(
		'name' => _x('Advanced Notifications', 'post type general name'),
		'singular_name' => _x('Notifications', 'post type singular name'),
		'add_new' => _x('Add New Notification', 'notification'),
		'add_new_item' => __('Add New Notification'),
		'edit_item' => __('Edit Notification'),
		'new_item' => __('New Notification'),
		'all_items' => __('All Notifications'),
		'view_item' => __('View Notification'),
		'search_items' => __('Search Notifications'),
		'not_found' =>  __('No Notifications found'),
		'not_found_in_trash' => __('No Notifications found in Trash'),
		'parent_item_colon' => '',
		'menu_name' => 'Notifications'
		);

	$args = array(
		'labels' => $labels,
        //'capabilities' => array('nothing'),
        'capability_type' => 'post',
		'public' => true,
		'public_queryable' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'message'),
		'has_archive' => true,
		'hierarchical' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_admin_bar' => true,
		'menu_position' => null,
		'taxonomies' => array('notification_cat'),
		'menu_icon' => ADVANCEDNOTIFICATIONS_URLPATH.'images/notify.png',
		'supports' => array('editor', 'title')
		);
    register_post_type('notifications',$args);
}


add_action( 'init', 'advnote_init_my_template' );

function advnote_template_include($incFile) {
  global $wp;
  global $wp_query;
	//echo 1; exit;
  if ($wp->query_vars['post_type'] == 'advnote_templates') {

    if (have_posts()) {
		$file = ADVANCEDNOTIFICATIONS_PATH.'single-template.php';
		$incFile = $file;
    } else {
		//echo 1; exit;
      $wp_query->is_404 = true;
    }
  }

  //echo $incFile;
  return $incFile;
}
//add_filter('template_include', 'advnote_template_include');

add_action('before_delete_post','advnote_delete_post');
add_action('trash_post','advnote_delete_post');

function advnote_delete_post($post_id)
{
	global $table_prefix;
	$post_type = get_post_type( $post_id );
	
	if ($post_type=='notifications')
	{
		$query = "UPDATE {$table_prefix}advnote_notifications SET status='0' WHERE post_id='$post_id' ";
		$result = mysql_query($query);
		if (!$result){ echo $query; echo mysql_error(); exit;}
	}
}
?>