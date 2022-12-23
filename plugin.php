<?php

/*
Plugin Name: Dingxiang™ Captcha
Plugin URI: https://www.dingxiang-inc.com/
Description: Dingxiang™验证码WordPress插件.
Version: 1.0.0
Author: 250king
Author URI: https://250king.top/
License: GPL2
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
	add_menu_page("顶象™验证码", "顶象™验证码", "manage_options", "captcha_setting", "config_page");
}
function captcha_add_action_link($links, $file) {
	if (plugin_basename(__FILE__) == $file) {
		array_unshift($links, '<a href="admin.php?page=captcha_setting">设置</a>');
	}
	return $links;
}

register_activation_hook(__FILE__, "captcha_plugin_enable");
register_uninstall_hook(__FILE__, "captcha_plugin_remove");
add_action("admin_menu", "captcha_add_config_page");
add_filter("plugin_action_links", "captcha_add_action_link", 10, 2);
$table_name = $wpdb->prefix."captcha_setting";
$login = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='login'"));
$reset = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='reset'"));
$register = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='register'"));
if ($login) {
	add_action("login_form", "captcha_common_form");
	add_filter("wp_authenticate_user", "captcha_login_check");
}
if ($reset) {
	add_action("lostpassword_form", "captcha_common_form");
	add_filter("allow_password_reset", "captcha_reset_register_check");
}
if ($register) {
	add_action("register_form", "captcha_common_form");
	add_filter("registration_errors", "captcha_reset_register_check");
}
if ($login || $reset || $register) {
	add_action("login_footer", "captcha_common_script");
}
