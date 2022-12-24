<?php
function captcha_token_check($token) {
	global $wpdb;
	$table_name = $wpdb->prefix."captcha_setting";
	$client_id = $wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='client_id'");
	$client_secret = $wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='client_secret'");
	$param = explode(":", $token);
	$payload = [
		"appKey" => $client_id,
		"constId" => count($param) == 2? $param[1]: null,
		"token" => $param[0],
		"sign" => md5($client_secret.$param[0].$client_secret)
	];
	$response = wp_remote_get("https://cap.dingxiang-inc.com/api/tokenVerify?".http_build_query($payload), [
		"timeout" => 30
	]);
	return json_decode($response["body"], true);
}
function captcha_login_check($user) {
	if (isset($_POST["log"], $_POST["pwd"])) {
		$result = captcha_token_check($_POST["captcha_token"]);
		if ($result["success"]) {
			return $user;
		}
		else {
			wp_die("验证码token校验失败！", 403);
		}
	}
	else {
		return $user;
	}
}
function captcha_register_check($email) {
	$result = captcha_token_check($_POST["captcha_token"]);
	if ($result["success"]) {
		return $email;
	}
	else {
		wp_die("验证码token校验失败！", 403);
	}
}
function captcha_reset_check($user) {
	$result = captcha_token_check($_POST["captcha_token"]);
	if ($result["success"]) {
		return $user;
	}
	else {
		wp_die("验证码token校验失败！", 403);
	}
}
function captcha_commit_check($commit) {
	$result = captcha_token_check($_POST["captcha_token"]);
	if ($result["success"]) {
		return $commit;
	}
	else {
		wp_die("验证码token校验失败！", 403);
	}
}
