let element = document.createElement("div");
element.id = "dx_captcha_div";
document.body.appendChild(element);
let captcha = _dx.Captcha(document.getElementById('dx_captcha_div'), {
    appId: "<?php echo $client_id?>",
    style: "popup",
    apiServer: "https://cap.dingxiang-inc.com",
    success: function (token) {
        document.getElementById("captcha_token").value = token;
        setTimeout(function () {
            let form = document.getElementById("<?php echo $form?>");
            let exc = Object.getPrototypeOf(form).submit;
            exc.call(form);
        }, 500);
    }
});
captcha.on("show", function () {
    document.getElementById("<?php echo $submit?>").disabled = true;
});
captcha.on("hide", function () {
    document.getElementById("<?php echo $submit?>").disabled = false;
});
document.getElementById("<?php echo $form?>").addEventListener("submit", function (form) {
    form.preventDefault();
    captcha.show();
});