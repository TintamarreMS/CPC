$(document).ready(function() {

    $("form#login-form").submit(function(e) {
        e.preventDefault();

        let username = $("input#login-username.input-material").val();
        let password = $("input#login-password.input-material").val();

        $.ajax({
            url: "/login",
            method: "POST",
            data: { client_id: username, client_secret: password, grant_type: "admin_credentials" }
        }).done(function(response) 
        {
            if (!response.error || access_token != null) 
            {
                
                console.log(response);
                document.cookie = "token=" + response.access_token + "; expires=" + timeConverter(response.token_refresh) + "; path=/";
                localStorage.setItem("user", response);
                
                if(username == 'clicrdv')   document.location.href = "/register-list";
                else                        document.location.href = "/dashboard";
            } 
            else 
            {
                $("p#errorLogin").text(response.message);
            }
        }).fail(function(response) {
            console.log(response);
        });
    })

    $("input#children").change(function(e) {
        if ($(this).val() == 1)
            $("div#categoriesSwitch.form-group.row").show();
        else
            $("div#categoriesSwitch.form-group.row").hide();
    })

    $("button#modalTransaction.btn.btn-primary").click(function(e) {
        $.ajax({
            url: "/transactions/" + $(this).data('id'),
            method: "POST",
        }).done(function(response) {
            $("tr#transaction" + $(this).data('id')).hide();
        }).fail(function(response) {
            console.log(response);
        });
    })

    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1);
            if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
        }
        return null;
    }

    function timeConverter(UNIX_timestamp) {
        var a = new Date(UNIX_timestamp * 1000);
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var year = a.getFullYear();
        var month = months[a.getMonth()];
        var date = a.getDate();
        var hour = a.getHours();
        var min = a.getMinutes();
        var sec = a.getSeconds();
        var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec;
        return time;
    }

});