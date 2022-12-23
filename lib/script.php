<?php
function captcha_common_script(): void {
	global $wpdb;
	$table_name = $wpdb->prefix."captcha_setting";
	$client_id = $wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='client_id'");
	$login = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='login'"));
	$reset = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='reset'"));
	$register = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='register'"));
    if (((!isset($_GET["action"]) || $_GET["action"] == "login") && $login) || ($_GET["action"] == "lostpassword" && $reset) || ($_GET["action"] == "register" && $register)) {
        ?>
	    <div id="captcha"></div>
	    <script src="https://cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js" crossorigin="anonymous"></script>
	    <script>
            let captcha = _dx.Captcha(document.getElementById('captcha'), {
                appId: "<?php echo $client_id?>",
                style: "popup",
                apiServer: "https://cap.dingxiang-inc.com",
                success: function (token) {
                    document.getElementById("captcha_token").value = token
                    setTimeout(function () {
                        document.getElementsByTagName("form")[0].submit()
                    }, 500)
                }
            })
            captcha.on("show", function () {
                document.getElementById("wp-submit").disabled = true
            })
            captcha.on("hide", function () {
                document.getElementById("wp-submit").disabled = false
            })
            document.getElementsByTagName("form")[0].onsubmit = function (ev) {
                ev.preventDefault()
                captcha.show()
            }
	    </script>
        <?php
    }
}
