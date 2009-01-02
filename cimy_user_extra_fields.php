<?php
/*
Plugin Name: Cimy User Extra Fields
Plugin URI: http://www.marcocimmino.net/cimy-wordpress-plugins/cimy-user-extra-fields/
Plugin Description: Add some useful fields to registration and user's info
Version: 1.3.0
Author: Marco Cimmino
Author URI: mailto:cimmino.marco@gmail.com
*/

/*

Cimy User Extra Fields - Allows adding mySQL Data fields to store/add more user info
Copyright (c) 2006-2008 Marco Cimmino

Code for drop-down support is in part from Raymond Elferink raymond@raycom.com

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.


The full copy of the GNU General Public License is available here: http://www.gnu.org/licenses/gpl.txt

*/

// added for WordPress >=2.5 compatibility
global $wpdb, $old_wpdb_data_table, $wpdb_data_table, $old_wpdb_fields_table, $wpdb_fields_table, $wpdb_wp_fields_table, $cimy_uef_options, $cimy_uef_version, $is_mu, $cuef_upload_path;

if (!stristr($wp_version, "mu") === FALSE) {
	$is_mu = true;

	$old_wpdb_data_table = $wpmuBaseTablePrefix."cimy_data";
	$old_wpdb_fields_table = $wpmuBaseTablePrefix."cimy_fields";
	
	$wpdb_data_table = $wpmuBaseTablePrefix."cimy_uef_data";
	$wpdb_fields_table = $wpmuBaseTablePrefix."cimy_uef_fields";
	$wpdb_wp_fields_table = $wpmuBaseTablePrefix."cimy_uef_wp_fields";
}
else {
	$is_mu = false;

	$old_wpdb_data_table = $wpdb->prefix."cimy_data";
	$old_wpdb_fields_table = $wpdb->prefix."cimy_fields";

	$wpdb_data_table = $wpdb->prefix."cimy_uef_data";
	$wpdb_fields_table = $wpdb->prefix."cimy_uef_fields";
	$wpdb_wp_fields_table = $wpdb->prefix."cimy_uef_wp_fields";
}

$cimy_uef_options = "cimy_uef_options";
$cimy_uef_options_descr = "Cimy User Extra Fields options are stored here and modified only by admin";

/*

RULES (stored into an associative array and serialized):

- 'min_length':			[int]		=> specify min length
[only for text, textarea, textarea-rich, password, picture, picture-url, avatar]

- 'exact_length':		[int]		=> specify exact length
[only for text, textarea, textarea-rich, password, picture, picture-url, avatar]

- 'max_length':			[int]		=> specify max length
[only for text, textarea, textarea-rich, password, picture, picture-url, avatar]

- 'email':			[true | false]	=> check or not for email syntax
[only for text, textarea, textarea-rich, password]

- 'can_be_empty':		[true | false]	=> field can or cannot be empty
[only for text, textarea, textarea-rich, password, picture, picture-url, dropdown, avatar]

- 'edit':
	'ok_edit' 				=> field can be modified
	'edit_only_if_empty' 			=> field can be modified if it's still empty
	'edit_only_by_admin' 			=> field can be modified only by administrator
	'edit_only_by_admin_or_if_empty' 	=> field can be modified only by administrator or if it's still empty
	'no_edit' 				=> field cannot be modified
[only for text, textarea, textarea-rich, password, picture, picture-url, checkbox, radio, dropdown, avatar]
[for radio and checkbox 'edit_only_if_empty' has no effects and 'edit_only_by_admin_or_if_empty' has the same effect as edit_only_by_admin]

- 'equal_to':			[string] => field should be equal to a specify string
[all except avatar]

- 'equal_to_case_sensitive':	[true | false] => equal_to if selected can be case sensitive or not
[only for text, textarea, password, dropdown]

- 'show_in_reg':		[true | false]	=> field is visible or not in the registration
[all]

- 'show_in_profile':		[true | false]	=> field is visible or not in user's profile
[all]

- 'show_in_aeu':		[true | false]	=> field is visible or not in A&U Extended page
[all]

TYPE can be:
- 'text'
- 'textarea'
- 'textarea-rich'
- 'password'
- 'checkbox'
- 'radio'
- 'dropdown'
- 'picture'
- 'picture-url'
- 'registration-date'
- 'avatar'

*/

// pre 2.6 compatibility or if not defined
if (!defined("WP_CONTENT_URL"))
	define("WP_CONTENT_URL", get_option("siteurl")."/wp_content");
	
if (!defined("WP_CONTENT_DIR"))
	define("WP_CONTENT_DIR", ABSPATH."/wp_content");

$cuef_plugin_name = basename(__FILE__);
$cuef_plugin_path = plugin_basename(dirname(__FILE__))."/";
$cuef_upload_path = WP_CONTENT_DIR."/".$cuef_plugin_path;
$cuef_upload_webpath = WP_CONTENT_URL."/".$cuef_plugin_path;

if ($is_mu) {
	$cuef_plugin_dir = WP_CONTENT_DIR."/mu-plugins/".$cuef_plugin_path;
	$cuef_css_webpath = WP_CONTENT_URL."/mu-plugins/".$cuef_plugin_path."css/";
	$cuef_js_webpath = WP_CONTENT_URL."/mu-plugins/".$cuef_plugin_path."js/";
}
else {
	$cuef_plugin_dir = WP_CONTENT_DIR."/plugins/".$cuef_plugin_path;
	$cuef_css_webpath = WP_CONTENT_URL."/plugins/".$cuef_plugin_path."css/";
	$cuef_js_webpath = WP_CONTENT_URL."/plugins/".$cuef_plugin_path."js/";
}

wp_register_script("cimy_uef_upload_pic", $cuef_js_webpath."upload_pic.js", false, false);
wp_register_script("cimy_uef_invert_sel", $cuef_js_webpath."invert_sel.js", false, false);
wp_register_style("cimy_uef_register", $cuef_css_webpath."cimy_uef_register.css", false, false);

require_once($cuef_plugin_dir.'/cimy_uef_db.php');
require_once($cuef_plugin_dir.'/cimy_uef_register.php');
require_once($cuef_plugin_dir.'/cimy_uef_profile.php');
require_once($cuef_plugin_dir.'/cimy_uef_functions.php');
require_once($cuef_plugin_dir.'/cimy_uef_options.php');
require_once($cuef_plugin_dir.'/cimy_uef_admin.php');

$cimy_uef_name = "Cimy User Extra Fields";
$cimy_uef_version = "1.3.0";
$cimy_uef_url = "http://www.marcocimmino.net/cimy-wordpress-plugins/cimy-user-extra-fields/";

$start_cimy_uef_comment = "<!--\n";
$start_cimy_uef_comment .= "\tStart code from ".$cimy_uef_name." ".$cimy_uef_version."\n";
$start_cimy_uef_comment .= "\tCopyright (c) 2006-2008 Marco Cimmino\n";
$start_cimy_uef_comment .= "\t".$cimy_uef_url."\n";
$start_cimy_uef_comment .= "-->\n";

$end_cimy_uef_comment = "\n<!--\n";
$end_cimy_uef_comment .= "\tEnd of code from ".$cimy_uef_name."\n";
$end_cimy_uef_comment .= "-->\n";

$wp_hidden_fields = array(
			'password' => array(
						'name' => "PASSWORD",
						'post_name' => "user_pass",
						'type' => "password",
						'label' => "Password:",
						'desc' => __('<strong>Note:</strong> this website let you personalize your password; after the registration you will receive an e-mail with another password, do not care about that!', $cimy_uef_domain),
						'value' => '',
						'store_rule' => array(
								'max_length' => 100,
								'can_be_empty' => false,
								'edit' => 'ok_edit',
								'email' => false,
								'show_in_reg' => true,
								'show_in_profile' => false,
								'show_in_aeu' => false,
								),
					),
			'firstname' => array(
						'name' => "FIRSTNAME",
						'post_name' => "first_name",
						'type' => "text",
						'label' => "First name:",
						'desc' => '',
						'value' => '',
						'store_rule' => array(
								'max_length' => 100,
								'can_be_empty' => true,
								'edit' => 'ok_edit',
								'email' => false,
								'show_in_reg' => true,
								'show_in_profile' => true,
								'show_in_aeu' => true,
								),
					),
			'lastname' => array(
						'name' => "LASTNAME",
						'post_name' => "last_name",
						'type' => "text",
						'label' => "Last name:",
						'desc' => '',
						'value' => '',
						'store_rule' => array(
								'max_length' => 100,
								'can_be_empty' => true,
								'edit' => 'ok_edit',
								'email' => false,
								'show_in_reg' => true,
								'show_in_profile' => true,
								'show_in_aeu' => true,
								),
					),
			'nickname' => array(
						'name' => "NICKNAME",
						'post_name' => "nickname",
						'type' => "text",
						'label' => "Nickname:",
						'desc' => '',
						'value' => '',
						'store_rule' => array(
								'max_length' => 100,
								'can_be_empty' => true,
								'edit' => 'ok_edit',
								'email' => false,
								'show_in_reg' => true,
								'show_in_profile' => true,
								'show_in_aeu' => true,
								),
					),
			'website' => array(
						'name' => "WEBSITE",
						'post_name' => "user_url",
						'type' => "text",
						'label' => "Website:",
						'desc' => '',
						'value' => '',
						'store_rule' => array(
								'max_length' => 100,
								'can_be_empty' => true,
								'edit' => 'ok_edit',
								'email' => false,
								'show_in_reg' => true,
								'show_in_profile' => true,
								'show_in_aeu' => true,
								),
					),
			'aim' => array(
						'name' => "AIM",
						'post_name' => "aim",
						'type' => "text",
						'label' => "AIM:",
						'desc' => '',
						'value' => '',
						'store_rule' => array(
								'max_length' => 100,
								'can_be_empty' => true,
								'edit' => 'ok_edit',
								'email' => false,
								'show_in_reg' => true,
								'show_in_profile' => true,
								'show_in_aeu' => true,
								),
					),
			'yahoo' => array(
						'name' => "YAHOO",
						'post_name' => "yim",
						'type' => "text",
						'label' => "Yahoo IM:",
						'desc' => '',
						'value' => '',
						'store_rule' => array(
								'max_length' => 100,
								'can_be_empty' => true,
								'edit' => 'ok_edit',
								'email' => false,
								'show_in_reg' => true,
								'show_in_profile' => true,
								'show_in_aeu' => true,
								),
					),
			'jgt' => array(
						'name' => "JGT",
						'post_name' => "jabber",
						'type' => "text",
						'label' => "Jabber / Google Talk:",
						'desc' => '',
						'value' => '',
						'store_rule' => array(
								'max_length' => 100,
								'can_be_empty' => true,
								'edit' => 'ok_edit',
								'email' => false,
								'show_in_reg' => true,
								'show_in_profile' => true,
								'show_in_aeu' => true,
								),
					),
			);

// strong illegal charset
$strong_illegal_chars = "/(\%27)|(\/)|(\\\)|(\[)|(\])|(\')|(\")|(\<)|(\>)|(\-\-)|(\%23)|(\#)/ix";

// light illegal charset
$light_illegal_chars = "/(\%27)|(\/)|(\\\)|(\[)|(\])|(\-\-)|(\%23)|(\#)/ix";

// all available types
$available_types = array("text", "textarea", "textarea-rich", "password", "checkbox", "radio", "dropdown", "picture", "picture-url", "registration-date", "avatar");

// types that should be pass registration check for equal to rule
$apply_equalto_rule = array("text", "textarea", "textarea-rich", "password", "checkbox", "radio", "dropdown");

// types that can have 'can be empty' rule
$rule_canbeempty = array("text", "textarea", "textarea-rich", "password", "picture", "picture-url", "dropdown", "avatar");

// common for min, exact and max length
$rule_maxlen = array("text", "password", "textarea", "textarea-rich", "picture", "picture-url", "avatar");

// common for min, exact and max length
$rule_maxlen_needed = array("text", "password", "picture", "picture-url", "avatar");

// types that can have 'check for email syntax' rule
$rule_email = array("text", "textarea", "textarea-rich", "password");

// types that can admit a default value if empty
$rule_profile_value = array("text", "textarea", "textarea-rich", "password", "picture", "picture-url", "avatar");

// types that can have 'equal to' rule
$rule_equalto = array("text", "textarea", "textarea-rich", "password", "checkbox", "radio", "dropdown", "picture", "picture-url", "registration-date");

// types that can have 'case (in)sensitive equal to' rule
$rule_equalto_case_sensitive = array("text", "textarea", "textarea-rich", "password", "dropdown");

// types that are file to be uploaded
$cimy_uef_file_types = array("picture", "avatar");

// type that are textarea and needs rows and cols attributes
$cimy_uef_textarea_types = array("textarea", "textarea-rich");

$max_length_name = 20;
$max_length_label = 5000;
$max_length_desc = 5000;
$max_length_value = 5000;
$max_length_fieldset_value = 1024;

// max size in KiloByte
$max_size_file = 20000;

$fields_name_prefix = "cimy_uef_";
$wp_fields_name_prefix = "cimy_uef_wp_";

// add checks for extra fields in the registration form
add_action('register_post', 'cimy_registration_check', 10, 3);

// add extra fields to registration form
add_action('register_form', 'cimy_registration_form');

// added for WordPress MU support
add_action('signup_extra_fields', 'cimy_registration_form');
add_action('preprocess_signup_form', 'cimy_registration_check');

// add extra fields to user's profile
add_action('show_user_profile', 'cimy_extract_ExtraFields');

// add extra fields in users edit profiles (for ADMIN)
add_action('edit_user_profile', 'cimy_extract_ExtraFields');

// this hook is no more used since the one below is enough for all
//add_action('personal_options_update', 'cimy_update_ExtraFields');

// add update engine for extra fields to users edit profiles
add_action('profile_update', 'cimy_update_ExtraFields');

// function that is executed during activation of the plug-in
add_action('activate_'.$cuef_plugin_path.$cuef_plugin_name,'cimy_plugin_install');

// add update engine for extra fields to user's registration
add_action('user_register', 'cimy_register_user_extra_fields');

// function that add all submenus
add_action('admin_menu', 'cimy_admin_menu_custom');

// delete user extra fields data when a user is deleted
add_action('delete_user', 'cimy_delete_user_info');

// add custom login/registration css
add_action('login_head', 'cimy_uef_register_css');

// add avatar filter
add_filter('get_avatar', 'cimy_uef_avatar_filter', 1, 5);

function cimy_uef_avatar_filter($avatar, $id_or_email, $size, $default, $alt="") {
	global $wpdb, $wpdb_data_table, $wpdb_fields_table, $cuef_upload_path;

	$sql = "SELECT ID,VALUE FROM $wpdb_fields_table WHERE TYPE='avatar' LIMIT 1";
	$res = $wpdb->get_results($sql);

	$field_id = $res[0]->ID;
	$overwrite_default = $res[0]->VALUE;

	// if there is no avatar field all the rest is totally cpu time wasted, returning...
	if (!isset($field_id))
		return $avatar;

	// ok we have one avatar field, who is requesting?
	$sql = sprintf("SELECT ID, user_login FROM wp_users WHERE user_email='%s' LIMIT 1", $wpdb->escape($id_or_email));
	$res = $wpdb->get_results($sql);

	$id = $res[0]->ID;
	$user_login = $res[0]->user_login;

	if (isset($id)) {
		$sql = "SELECT data.VALUE FROM $wpdb_data_table as data JOIN $wpdb_fields_table as efields ON efields.id=data.field_id WHERE (efields.TYPE='avatar' AND data.USER_ID=$id) LIMIT 1";

		$value = $wpdb->get_var($sql);

		if ( false === $alt)
			$safe_alt = '';
		else
			$safe_alt = attribute_escape( $alt );

		// max $size allowed is 512
		if (isset($value)) {
			if ($value == "")
				return $avatar;

			$thumb_value = cimy_get_thumb_path($value);
			$file_thumb = $cuef_upload_path.$user_login."/avatar/".cimy_get_thumb_path(basename($value));

			if (is_file($file_thumb))
				$value = $thumb_value;

			$avatar = "<img alt='{$safe_alt}' src='{$value}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
		}
		else {
			if (isset($overwrite_default)) {
				if ($overwrite_default == "")
					return $avatar;

				$avatar = "<img alt='{$safe_alt}' src='{$overwrite_default}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
			}
		}
	}

	return $avatar;
}

function cimy_uef_register_css() {
	wp_print_styles("cimy_uef_register");
}

$cimy_uef_domain = 'cimy_uef';
$cimy_uef_i18n_is_setup = 0;
cimy_uef_i18n_setup();

function cimy_uef_i18n_setup() {
	global $cimy_uef_domain, $cimy_uef_i18n_is_setup, $cuef_plugin_path;

	if ($cimy_uef_i18n_is_setup)
		return;

	load_plugin_textdomain($cimy_uef_domain, PLUGINDIR.'/'.$cuef_plugin_path.'langs', $cuef_plugin_path.'langs');
}

function cimy_admin_menu_custom() {
	global $cimy_uef_name, $cimy_uef_domain, $is_mu, $cimy_top_menu;
	
	if (!cimy_check_admin('level_10'))
		return;
	
	if (isset($cimy_top_menu)) {
		add_submenu_page('cimy_series.php', $cimy_uef_name.": ".__("Options"), "UEF: ".__("Options"), 10, "user_extra_fields_options", 'cimy_show_options_notembedded');
		add_submenu_page('cimy_series.php', $cimy_uef_name.": ".__("Fields", $cimy_uef_domain), "UEF: ".__("Fields", $cimy_uef_domain), 10, "user_extra_fields", 'cimy_admin_define_extra_fields');
		add_submenu_page('profile.php', __('Authors &amp; Users Extended', $cimy_uef_domain), __('A&amp;U Extended', $cimy_uef_domain), 10, "au_extended", 'cimy_admin_users_list_page');
	}
	else {
		add_options_page($cimy_uef_name, $cimy_uef_name, 10, "user_extra_fields", 'cimy_admin_define_extra_fields');
		add_submenu_page('profile.php', __('Authors &amp; Users Extended', $cimy_uef_domain), __('A&amp;U Extended', $cimy_uef_domain), 10, "au_extended", 'cimy_admin_users_list_page');
	}
}

function cimy_manage_upload($input_name, $user_login, $rules, $old_file=false, $delete_file=false, $is_avatar=false) {
	global $cuef_upload_path, $cuef_upload_webpath, $cuef_plugin_dir;

	if ($is_avatar)
		$avatar_path = "/avatar";
	else
		$avatar_path = "";

	$user_path = $cuef_upload_path.$user_login."/";
	$file_path = $cuef_upload_path.$user_login.$avatar_path."/";
	$file_name = $_FILES[$input_name]['name'];

	// protect from site traversing
	$file_name = str_replace('../', '', $file_name);
	$file_name = str_replace('/', '', $file_name);
	
	// delete old file if requested
	if ($delete_file) {
		if (is_file($file_path.$old_file))
			unlink($file_path.$old_file);
	
		$old_thumb_file = cimy_get_thumb_path($old_file);
		
		if (is_file($file_path.$old_thumb_file))
			unlink($file_path.$old_thumb_file);
	}

	// if $user_login is not present
	//	or there is no file to upload
	//	or dest dir is not writable
	// then everything else is useless
	if (($user_login == "") || (!isset($_FILES[$input_name]['name'])) || (!is_writable($cuef_upload_path)))
		return "";

	// create user subdir
	if (!is_dir($user_path)) {
		mkdir($user_path, 0777);
		chmod($user_path, 0777);
	}

	// create avatar subdir if needed
	if (($is_avatar) && (!is_dir($file_path))) {
		mkdir($file_path, 0777);
		chmod($file_path, 0777);
	}
	
	// picture filesystem path
	$file_full_path = $file_path.$file_name;
	
	// picture url to write in the DB
	$data = $cuef_upload_webpath.$user_login.$avatar_path."/".$file_name;
	
	// filesize in Byte transformed in KiloByte
	$file_size = $_FILES[$input_name]['size'] / 1024;
	$file_type = $_FILES[$input_name]['type'];
	$file_tmp_name = $_FILES[$input_name]['tmp_name'];
	$file_error = $_FILES[$input_name]['error'];

	// CHECK IF IT IS A REAL PICTURE
	if (stristr($file_type, "image/") === false)
		$file_error = 1;
	
	// MIN LENGTH
	if (isset($rules['min_length']))
		if ($file_size < (intval($rules['min_length'])))
			$file_error = 1;
	
	// EXACT LENGTH
	if (isset($rules['exact_length']))
		if ($file_size != (intval($rules['exact_length'])))
			$file_error = 1;

	// MAX LENGTH
	if (isset($rules['max_length']))
		if ($file_size > (intval($rules['max_length'])))
			$file_error = 1;
	
	// if there are no errors and filename is empty
	if (($file_error == 0) && ($file_name != "")) {
		if (move_uploaded_file($file_tmp_name, $file_full_path)) {
			// change file permissions for broken servers
			@chmod($file_full_path, 0644);
			
			// if there is an old file to delete
			if ($old_file) {
				// delete old file if the name is different, if equal NOPE because new file is already uploaded
				if ($file_name != $old_file)
					if (is_file($file_path.$old_file))
						unlink($file_path.$old_file);
				
				$old_thumb_file = cimy_get_thumb_path($old_file);
				
				if (is_file($file_path.$old_thumb_file))
					unlink($file_path.$old_thumb_file);
			}
			
			// should be stay AFTER DELETIONS
			if (isset($rules['equal_to'])) {
				if ($maxside = intval($rules['equal_to'])) {
					if (!function_exists(image_resize))
						require_once(ABSPATH . 'wp-includes/media.php');

					if (!function_exists(wp_load_image))
						require_once($cuef_plugin_dir.'/cimy_uef_missing_functions.php');

					image_resize($file_full_path, $maxside, $maxside, false, "thumbnail");
				}
			}
		}
	}
	else
		$data = "";
	
	return $data;
}

?>