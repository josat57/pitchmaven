/*
    jshint esversion: 6
*/

"use strict";

$(document).ajaxStart($.blockUI()).ajaxStop($.unblockUI());
	
$(document).ready(function() {
    $('#signout_link').click(function(e) {
        signOutUser(e);
    });

    $("#dashboard-link").click(function(e) {
        window.location.reload(true);
    });

    $('#signout_btn').click(function(e) {
        signOutUser(e);
    });

    $('#btn_create_platform').click(function(e) {
        createPlatform(e);
    });
    
    getUserProfile();
});

function signOutUser(e) {
    e.preventDefault();
    $.ajax({
        url: '/polljota/service/api/index.php',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'action': 'sign_out_admin',
            'token': $.cookie('polljotter_jwt')
        },
        headers: {
            Authorization: `Bearer ${$.cookie('polljotter_jwt')}`,
        },
        success: function(json) {
            if (json.statuscode == 0) {
                window.location.href = '../units/signin.html';
            } else {
                Swal.fire({
                    title: "error",
                    html: json.status,
                    type: "error",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then(function() {
                    CookieStorage.clear();
                    window.location.href = '../units/signin.html';
                });
            }
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText);
            Swal.fire({
                title: "error",
                html: xhr.responseText,
                type: "error",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                    CookieStorage.clear();
                    window.location.href = '../units/signin.html';
                });
            }
        });
}
    
function getUserProfile() {
    $.ajax({
        url: '/polljota/service/api/index.php',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'action': 'get_current_user_admin',
            'token': $.cookie('polljotter_jwt')
        },
        headers: {
            Authorization: `Bearer ${$.cookie('polljotter_jwt')}`,
        },
        success: function(json) {				
            if (json.statuscode === 0) {
                console.log(json);
                const full_name = json.data.first_name + " " + json.data.last_name;
                window.localStorage.setItem('fullName', full_name);
                $(".user_full_name").text(json.data.first_name);
                $("#user_email").text(json.data.email);
                $("#header-title").text(full_name);
                $("#user_full_name").addClass("text-truncated");
                $("#user_full_name").attr("title", full_name);
                if (json.data.role == 'admin') {
                    $("#user_role").text("Election Controller");
                } else {
                    $("#user_role").text(json.data.role);
                }
                const img = new Image();
                img.src = "data:image/jpg;base64," + json.data.profile_photo;
                $(".profile_photo").attr("src", img.src);
            } else {              
                document.cookie = "polljotter_jwt=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/polljota/entry/electroller/workdesk.html;";								
                window.location.href = '../units/signin.html';
            }
        },
        error: function(xhr, status, error) {			
            window.location.href = '../units/signin.html';
            console.log(xhr.responseText);
        }
    });
}

function createPlatform(e) {
    e.preventDefault();
    let formData = new FormData(signupForm);
    formData.append('action', 'create_platform_admin');      
    $.ajax({
        url: "/polljota/service/api/index.php",
        type: "POST",
        data: formData,
        headers: {
            Authorization: `Bearer ${$.cookie('polljotter_jwt')}`,
        },
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'JSON',
        success: function(json){
            console.log(json);
            if (json.statuscode === 0) {
                Swal.fire({
                    title: "Success",
                    html: json.status,
                    type: "success",
                    icon: "success",
                    confirmButtonText: "Ok"
                }).then(function(){
                    showAjax('', 'desk/apps/subscriptions/add.html', 'kt_content_container');
                });
            } else {
                Swal.fire({
                    title: "Error",
                    html: json.status,
                    type: "error",
                    icon: "error",
                    confirmButtonText: "Ok"
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
            console.log(jqXHR);
            Swal.fire({
                title: "Error",
                html: jqXHR.responseText,
                type: "error",
                confirmButtonText: "Ok"
            });
        }
    });
}