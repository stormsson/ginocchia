function statusChangeCallback(response) {
    if (response.status === 'connected') {
        checkServerAuth(response.authResponse.accessToken);
    }
}

function checkLoginState() {
    FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
    });
}

window.fbAsyncInit = function() {
    FB.init({
        appId      : '1433159156980399',
        cookie     : true,  // enable cookies to allow the server to access
                            // the session
        xfbml      : true,  // parse social plugins on this page
        version    : 'v2.2' // use version 2.2
    });
};

// Load the SDK asynchronously
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// effettua il check dell'autenticazione lato server. I casi sono i seguenti:
// 1) l'utente esiste già autenticato nell'app
// 2) l'utente non è stato autenticato
function checkServerAuth(accessToken) {
    FB.api('/me', function(response) {
        jQuery.ajax({
            url: "/fb-login",
            type: 'POST',
            data: 'facebook_access_token='+accessToken,
            success: function (results) {
                if (results) {
                    window.location.href = '/';
                }
            }
        });
    });
}

function pad(n, width, z) {
    z = z || '0';
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

function doAnimation(count) {
    var supercounter_str = pad(count,4);

    for (var i = 0; i < supercounter_str.length; i++) {
        var altezza = -(160 * supercounter_str.charAt(i));
        $('#digit_' + i).animate({'top': altezza + 'px'},1000);
    }
}

$(function () {
    doAnimation(GINO.count);

    $('#ahi').on('click', function(e) {
        e.preventDefault();

        $.ajax(GINO.post_count_url, {
            type: 'POST',
            success: function (data, textStatus, jqXHR) {
                //counter per utente
                $('#user-counter').html(data['user_count']);

                //counter globale
                $('#counter').html(data['count']);
                doAnimation(data['count']);
            }
        });
    });
});