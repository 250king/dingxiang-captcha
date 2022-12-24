<?php

/*
Plugin Name: Dingxiang™ Captcha
Plugin URI: https://github.com/250king/dingxiang-captcha
Description: Dingxiang™验证码WordPress插件.
Version: 1.0.0
Author: 250king
Author URI: https://www.250king.top/
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

require "lib/config.php";
require "lib/form.php";
require "lib/script.php";
require "lib/check.php";

function captcha_plugin_enable(): void {
	if (empty(get_option("captcha_setting_table"))) {
		global $wpdb;
		$table_name = $wpdb->prefix."captcha_setting";
		$charset = $wpdb->get_charset_collate();
		$wpdb->query("CREATE TABLE IF NOT EXISTS $table_name (
    		ID bigint(20) NOT NULL AUTO_INCREMENT UNIQUE,
			`key` varchar(150) NOT NULL UNIQUE,
    		`value` varchar(150),
			PRIMARY KEY (ID)
		) $charset" );
		$wpdb->query("INSERT INTO $table_name (`key`) VALUES ('client_id')");
		$wpdb->query("INSERT INTO $table_name (`key`) VALUES ('client_secret')");
		$wpdb->query("INSERT INTO $table_name (`key`) VALUES ('login')");
		$wpdb->query("INSERT INTO $table_name (`key`) VALUES ('register')");
		$wpdb->query("INSERT INTO $table_name (`key`) VALUES ('reset')");
		$wpdb->query("INSERT INTO $table_name (`key`) VALUES ('commit')");
		update_option("captcha_setting_table", time());
	}
}
function captcha_plugin_remove(): void {
	global $wpdb;
	$table_name = $wpdb->prefix."captcha_setting";
	$wpdb->query("DROP TABLE IF EXISTS $table_name");
	delete_option("captcha_setting_table");
}
function captcha_add_config_page(): void {
	add_submenu_page("options-general.php", "顶象™验证码", "顶象™验证码", "manage_options", "dx-captcha", "config_page");
}
function captcha_add_action_link($links, $file) {
	if (plugin_basename(__FILE__) == $file) {
		array_unshift($links, '<a href="options-general.php?page=dx-captcha">设置</a>');
	}
	return $links;
}

register_activation_hook(__FILE__, "captcha_plugin_enable");
register_uninstall_hook(__FILE__, "captcha_plugin_remove");
add_action("admin_menu", "captcha_add_config_page");
add_filter("plugin_action_links", "captcha_add_action_link", 10, 2);
add_action("init", function () {
	global $wpdb;
	$table_name = $wpdb->prefix."captcha_setting";
	$login = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='login'"));
	$reset = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='reset'"));
	$register = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='register'"));
	$commit = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='commit'"));
	if ($login) {
		add_action("login_form", "captcha_common_form");
		add_filter("authenticate", "captcha_login_check");
	}
	if ($reset) {
		add_action("lostpassword_form", "captcha_common_form");
		add_filter("lostpassword_user_data", "captcha_reset_check");
	}
	if ($register) {
		add_action("register_form", "captcha_common_form");
		add_filter("user_registration_email", "captcha_register_check");
	}
	if ($login || $reset || $register) {
		$action = $_REQUEST['action'] ?? 'login';
		switch ($action) {
			case "lostpassword":
			case "retrievepassword":
				if ($reset) {
					add_action("login_footer", function () {
						captcha_common_script( "wp-submit", "lostpasswordform" );
					});
				}
				break;
			case "register":
				if ($register) {
					add_action("login_footer", function () {
						captcha_common_script( "wp-submit", "registerform" );
					});
				}
				break;
			case "login":
			default:
				if ($login) {
					add_action("login_footer", function () {
						captcha_common_script( "wp-submit", "loginform" );
					});
				}
				break;
		}
	}
	if ($commit && !is_user_logged_in()) {
		add_filter("preprocess_comment", "captcha_commit_check");
		add_action("comment_form", "captcha_common_form");
		add_action("comment_form_after_fields", function () {
			captcha_common_script("submit", "commentform");
		});
	}
});
