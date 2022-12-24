<?php
function config_page(): void {
	global $wpdb;
	$table_name = $wpdb->prefix."captcha_setting";
    $client_id = $wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='client_id'");
    $client_secret = $wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='client_secret'");
    $login = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='login'"));
    $reset = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='reset'"));
    $register = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='register'"));
    $commit = boolval($wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='commit'"))
    ?>
	<div class="wrap">
		<h2>顶象™验证码</h2>
		<?php
        if (isset($_POST["submit"])) {
	        if (isset($_POST["client_id"], $_POST["client_secret"])) {
		        $client_id = $_POST["client_id"];
		        $client_secret = $_POST["client_secret"];
		        $wpdb->query("UPDATE $table_name SET `value`='$client_id' WHERE `key`='client_id'");
		        $wpdb->query("UPDATE $table_name SET `value`='$client_secret' WHERE `key`='client_secret'");
	        }
	        else {
		        $login = isset($_POST["login"])? 1: 0;
		        $reset = isset($_POST["reset"])? 1: 0;
		        $register = isset($_POST["register"])? 1: 0;
                $commit = isset($_POST["commit"])? 1: 0;
		        $wpdb->query("UPDATE $table_name SET `value`='$login' WHERE `key`='login'");
		        $wpdb->query("UPDATE $table_name SET `value`='$reset' WHERE `key`='reset'");
		        $wpdb->query("UPDATE $table_name SET `value`='$register' WHERE `key`='register'");
		        $wpdb->query("UPDATE $table_name SET `value`='$commit' WHERE `key`='commit'");
	        }
            ?>
            <div class="updated fade">
                <p>
                    <strong>设置更新成功！</strong>
                </p>
            </div>
            <?php
		}
        ?>
        <div id="poststuff">
            <div id="post-body">
                <div class="postbox">
                    <h3 class="hndle">基础设置</h3>
                    <div class="inside">
                        <form method="post" action="">
                            <table class="form-table">
                                <tr>
                                    <th>
                                        <label for="client_id">Client ID</label>
                                    </th>
                                    <td>
                                        <input type="text" id="client_id" name="client_id" value="<?php echo $client_id?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="client_secret">Client Secret</label>
                                    </th>
                                    <td>
                                        <input type="text" id="client_secret" name="client_secret" value="<?php echo $client_secret?>">
                                    </td>
                                </tr>
                            </table>
				            <?php submit_button()?>
                        </form>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle">保护区域</h3>
                    <div class="inside">
                        <form method="post" action="">
                            <table class="form-table">
                                <tr>
                                    <th>
                                        <label for="login">登录</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="login" name="login" <?php echo $login? "checked": ""?>>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="reset">重置密码</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="reset" name="reset" <?php echo $reset? "checked": ""?>>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="register">注册</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="register" name="register" <?php echo $register? "checked": ""?>>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="commit">评论区</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="commit" name="commit" <?php echo $commit? "checked": ""?>>
                                    </td>
                                </tr>
                            </table>
				            <?php submit_button()?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
	</div>
<?php
}
