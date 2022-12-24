<?php
/*
 * 为压缩代码，前端JS源码已经压缩混淆
 * 前端JS源码请查阅 https://github.com/250king/dingxiang-captcha/blob/master/asset/javascript/frontend.js
 */
function captcha_common_script($submit, $form): void {
	global $wpdb;
	$table_name = $wpdb->prefix."captcha_setting";
	$client_id = $wpdb->get_var("SELECT `value` FROM $table_name WHERE `key`='client_id'");
    ?>
    <script src="https://cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js" crossorigin="anonymous"></script>
    <script>let element=document.createElement("div");element.id="dx_captcha_div";document.body.appendChild(element);let captcha=_dx.Captcha(document.getElementById('dx_captcha_div'),{appId:"<?php echo $client_id?>",style:"popup",apiServer:"https://cap.dingxiang-inc.com",success:function(a){document.getElementById("captcha_token").value=a;setTimeout(function(){let form=document.getElementById("<?php echo $form?>");let exc=Object.getPrototypeOf(form).submit;exc.call(form)},500)}});captcha.on("show",function(){document.getElementById("<?php echo $submit?>").disabled=true});captcha.on("hide",function(){document.getElementById("<?php echo $submit?>").disabled=false});document.getElementById("<?php echo $form?>").addEventListener("submit",function(a){a.preventDefault();captcha.show()});</script>
    <?php
}
