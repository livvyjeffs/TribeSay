$(document).ready(function() {

    get_email_notification_state();

    create_modals();

    $('[option="1"] .option-button').click(function() {
        frenetic.modal.change_profile.open();
    });

    $('[option="2"] .option-button').click(function() {
        frenetic.modal.change_pw.open();
    });

    $('#change_password_form .submit-button').click(function() {
        change_password();
    });

    $('#change_password_form input').keypress(function(e) {
        if (e.keyCode === 13) {
            $('#change_password_form .submit-button').trigger('click');
            e.stopPropogation();
        }
    });

    $('#email_notification_state').click(function() {
        change_email_notification_state();
    });

});

function get_email_notification_state() {
    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/email_activation.php");

    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            if (ajax.responseText === "activated") {
                $('#email_notification_state').attr('checked', 'checked');
            } else if (ajax.responseText === "deactivated") {
                $('#email_notification_state').removeAttr('checked');
            } else {
                alert(ajax.responseText);
            }
        }
    };
    ajax.send("get_activation_state=" + frenetic.user.username);
}

function change_email_notification_state() {

    var new_state;

    if ($('#email_notification_state').prop('checked')) {
        new_state = 'activate';
    } else {
        new_state = 'deactivate';
    }

    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/email_activation.php");

    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {

            if (ajax.responseText === "success") {

                if (new_state === 'activate') {
                    alert("You are now subscribed to email notifications.");
                } else {
                    alert('You are unsubscribed from email notifications.');
                }

            } else {
                alert(ajax.responseText);
            }
        }
    };
    ajax.send("change_activation_state=" + new_state);

}

var changing = false;

function change_password() {
    
    console.log('chaing')
    
    if(changing){
        return;
    }
    
    changing = true;    
        
    var old_password = $('#change_password_form input.old').val();
    var new_password = $('#change_password_form input.new_1').val();
    var new_confirm = $('#change_password_form input.new_2').val();
    if (new_password !== new_confirm) {
        alert("The new passwords do not match.");
        return;
    }
    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/change_pw.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            if (ajax.responseText === "success") {
                alert("Your password has been successfully changed.");
                $('#change_password_form input.old').val("");
                $('#change_password_form input.new_1').val("");
                $('#change_password_form input.new_2').val("");
                frenetic.modal.change_pw.close();
            } else {
                alert(ajax.responseText);
            }
        }
    };
    ajax.send("current_pass=" + old_password + "&new_pass1=" + new_password + "&new_pass2=" + new_confirm);
}