// JavaScript Document
document.addEventListener("DOMContentLoaded", init, false); 

var forgotPassword = {email: null, answer: null, password1: null, password2: null};
var incorrectAnswers = 0;

function init() {
	var emailForm = document.querySelector('#forgotPassword-form');
	emailForm.addEventListener('submit', emailSubmit, false);
	var secretForm = document.querySelector('#secretAnswer-form');
	secretForm.addEventListener('submit', secretAnswer, false);
	var newPasswordForm = document.querySelector('#newPassword-form');
	newPasswordForm.addEventListener('submit', newPassword, false);
	$('#email').focus();
}

function newPassword (ev) {
	ev.preventDefault();
	$('#password2Control').hide();
	$('#password1Control').hide();
	
	var validPassword1 = checkPassword($('#password1').val(), 'password1Control');
	var validPassword2 = checkPassword($('#password2').val(), 'password2Control');
	
	if (validPassword1 && validPassword2) {
		var password1 = $('#password1').val();
		var password2 = $('#password2').val();
		if (password1 == password2) {
			
			$.post("http://marchtennisclub.com/security/server.php", {
							"func": 'forgotPassword3',
							"email": forgotPassword.email,
							  "answer": forgotPassword.answer,
							  "password": password1
			},
			function(data) {
				console.log (data);
		
				if(data.status === 1) {
					forgotPassword.answer = answer;
					$('#newPassword-form').hide();
					$('#forgotYourPassword').html("Your Password Has Been Changed Successfully");
				  	setTimeout(function(){ window.location = "courts.php"; }, 1500);
					 //Login form should go to homepage
				//Either email has already been used or pwd is invalid
				}else {
					$('#password2Control').html(data.message);
					$('#password2Control').fadeIn();
				}
			}, "json");  
			
		}else {
			$('#password2Control').html("Passwords don't match");
			$('#password2Control').fadeIn();
		}
	}
		
}
function secretAnswer (ev) {
	ev.preventDefault();
	$('#answerControl').hide();
	var answer = $('#answer').val();
	$.post("http://marchtennisclub.com/security/server.php", {
                    "func": 'forgotPassword2',
                    "email": forgotPassword.email,
					  "answer": answer
	},
	function(data) {
		console.log (data);

		if(data.status === 1) {
			forgotPassword.answer = answer;
			$('#secretAnswer-form').hide();
			$('#newPassword-form').fadeIn();
		    $('#answer').val('');
			$('#password1').focus();
			//window.location = "index.php"; //Login form should go to homepage
		//Either email has already been used or pwd is invalid
		}else if (data.status === 0) {
			incorrectAnswers++;
			if (incorrectAnswers > 4) {
				alert ("You have exceeded the number of attempts allowed, Please Re-Try Later");
				window.location = "courts.php";
			}
			$('#answerControl').html(data.message);
			$('#answerControl').fadeIn();
		}
	}, "json");  
				
}
function emailSubmit(ev) {
	ev.preventDefault();
	$('#emailControl').html("");
    $('#emailControl').hide();	 
        var validEmail = checkEmail($('#email').val(), 'emailControl');
        
        if (validEmail) {
            var email = $('#email').val();
                $.post("http://marchtennisclub.com/security/server.php", {
                    "func": 'forgotPassword1',
                    "email": email
                },
                function(data) {
                    console.log (data);

                    if(data.status === 1) {
						  forgotPassword.email = email;
						  $('#email').val("");
                        $('#forgotPassword-form').hide();
                        $('#secretQuestion').html(data.passwordHint);
                        $('#secretAnswer-form').fadeIn();
						$('#answer').focus();
                 
                    }
                    else {
                        $('#email').val("");
						$('#email').focus();
                        $('#emailControl').html("That email is invalid");
                        $('#emailControl').fadeIn();
                    }
                }, "json");  
            }else {
				$('#email').val("");
						$('#email').focus();
                $('#emailControl').html('That email is invalid');
                $('#emailControl').fadeIn();
            }
}

function checkPassword(password, control) {
    password = $.trim(password);
    if (password === "") {
        $('#' + control).fadeIn();
        $('#' + control).html("Password is required");
        return false;
    } else if (password.length < 6) {
        $('#' + control).fadeIn();
        $('#' + control).html("Password must be at least 6 characters");
        return false;
    } else {
        $('#' + control).hide();
        return true;
    }
}

function checkEmail(email, control) {
    email = $.trim(email);
    var reg = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if (email === "") {
        $('#' + control).fadeIn();
        $('#' + control).html("Email address is required");
        return false;
    } else if (!reg.test(email)) {
        $('#' + control).fadeIn();
        $('#' + control).html("Invalid email address");
        return false;
    } else if (email.length > 100) {
        $('#' + control).fadeIn();
        $('#' + control).html("Sorry your email is too long");
        return false;
    } else {
        $('#' + control).hide();
        return true;
    }
}