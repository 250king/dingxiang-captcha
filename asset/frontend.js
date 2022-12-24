let c = _dx.Captcha(document.getElementById('captcha'), {
    appId: "<?php echo $client_id?>",
    style: "popup",
    apiServer: "https://cap.dingxiang-inc.com",
    success: function (token) {
        document.getElementById("captcha_token").value = token;
        setTimeout(function () {
            document.getElementsByTagName("form")[0].submit();
        }, 500);
    }
});
c.on("show", function () {
    document.getElementById("<?php echo $element?>").disabled = true;
});
c.on("hide", function () {
    document.getElementById("<?php echo $element?>").disabled = false;
});
document.getElementsByTagName("form")[0].addEventListener("submit", function (form) {
    form.preventDefault();
    c.show();
});
