
$(document).ready(function(){

var confirmation = $("#confirmation");

    $("input[name='membership_category']").click(function() {
        if ($("input[name='membership_category']:checked").val() == 1 || $("input[name='membership_category']:checked").val() == 2) {
            $('.dependantLabel').removeClass("hide");
            $('.dependantName').removeClass("hide");
            $('.dependantDOB').removeClass("hide");
        }else{
            $('.dependantLabel').addClass("hide");
            $('.dependantName').addClass("hide");
            $('.dependantDOB').addClass("hide");
        }
    });

$('.error-message').hide();
confirmation.hide();

$("#submit").click(function(ev){
ev.preventDefault();

$('.error-message').hide();
confirmation.hide();

var validTerms = $('#termsAgree').prop('checked')  === true ? 1 : 0;

if (validTerms) {
	
var validEmail = checkEmail($('#email').val(), 'emailControl');
var validPassword1 = checkPassword($('#password1').val(), 'password1Control');
var validPassword2 = checkPassword($('#password2').val(), 'password2Control');
var validFirstName = checkFirstName($('#firstName').val(), 'firstNameControl');
var validLastName = checkLastName($('#lastName').val(), 'lastNameControl');
var validAddress = checkAddress($('#address').val(), 'addressControl');
var validCity = checkCity($('#city').val(), 'cityControl');
var validPostalCode = checkPostalCode($('#postalCode').val(), 'postalCodeControl');
var validHomePhone = checkHomePhone($('#homePhone').val(), 'homePhoneControl');
var validBusinessPhone = checkBusinessPhone($('#businessPhone').val(), 'businessPhoneControl');
var validPasswordHintQuestion = checkPasswordHintQuestion($('#passwordHintQuestion').val(), 'passwordHintQuestionControl');
var validPasswordHintAnswer = checkPasswordHintAnswer($('#passwordHintAnswer').val(), 'passwordHintAnswerControl');
var validMembershipCategory = checkMembershipCategory('membershipCategoryControl');





	
if (validEmail && validPassword1 && validPassword2 && validFirstName && validLastName && validAddress && validCity && validPostalCode && validHomePhone && validBusinessPhone && validPasswordHintQuestion && validPasswordHintAnswer && validMembershipCategory) {
			
			var password1 = $('#password1').val();
			var password2 = $('#password2').val();
			
			if (password1 === password2) {
				var email = $('#email').val();
				var password = $('#password1').val();
				var firstName = $('#firstName').val();
				var lastName = $('#lastName').val();
				var address = $('#address').val();
				var city = $('#city').val();
				var postalCode = $('#postalCode').val();
				var homePhone = $('#homePhone').val();
				var businessPhone = $('#businessPhone').val();
				var passwordHintQuestion = $('#passwordHintQuestion').val();
				var passwordHintAnswer = $('#passwordHintAnswer').val();
				var membershipCategory = $("input[name='membership_category']:checked").val();
				var membershipAmount = $("input[name='membership_category']:checked").data("price");
				var donation = $('#donation').prop('checked')  === true ? 1 : 0;
				var donationAmount = donation  === 1 ? 10 : 0;
				var newMember = $('#newMember').prop('checked')  === true ? 60 : 0;
				var familyMembers = $('.dn1').val() + " " + $('.ddob1').val() + " , " + $('.dn2').val() + " " + $('.ddob2').val() + " , " + $('.dn3').val() + " " + $('.ddob3').val() + " , " + $('.dn4').val() + " " + $('.ddob4').val() + " , " + $('.dn5').val() + " " + $('.ddob5').val() + " , " + $('.dn6').val() + " " + $('.ddob6').val() + " , ";
                

				$.post("http://marchtennisclub.com/json-api/", {
                    "member_id":"NULL",
						"last_name" :lastName,
						"first_name": firstName,
						"address": address,
						"city" : city,
						"postal_code" : postalCode,
						"home_phone" : homePhone,
						"business_phone" : businessPhone,
						"email" : email,
						"password" : password,
						"password_hint" : passwordHintQuestion,
						"password_hint_answer" : passwordHintAnswer,
						"family_members": familyMembers,
						"membership_category_id":membershipCategory,
						"donate": donation,
						"amount_enclosed": "$" + (membershipAmount + newMember + donationAmount),
						"method":"MtcMemberSecure.create"   
                },
                function(data) {
                    if (data.status === "SUCCESS") {
						console.log(data);
						confirmation.fadeIn();
						confirmation.css("color", "#000000");
						confirmation.html("Congratulations registration was successfull");
						document.querySelector('#RegistrationForm').reset();
                  
                    } else {
							confirmation.fadeIn();
							confirmation.css("color", "#e75967");
							confirmation.html("Form Error: " + data.errMsg);
						   console.log (data);
                    }
                }, "json");
				
			}else {
				$('#password2Control').fadeIn();
       			$('#password2Control').html("Your passwords do not match");
			}
			
			}else {
				confirmation.fadeIn();
				confirmation.css("color", "#e75967");
				confirmation.html("Please correct the errors in the form");
				//document.getElementById("RegistrationForm").reset();		
			}
}else {
	confirmation.fadeIn();
	confirmation.css("color", "#e75967");
	confirmation.html("You must agree to the terms and conditions");
}
		}); 
});

function checkMembershipCategory(control){
	if ($("input[name='membership_category']:checked").length === 1) {
		$('#' + control).hide();
    	return true;
	}else {
		$('#' + control).fadeIn();
       $('#' + control).html("You must select one membership category");
		return false;	
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

function checkFirstName(name, control) {
	name = $.trim(name);
	if (name === "") {
		$('#' + control).fadeIn();
		$('#' + control).html("First name is required");
		return false;
	}
	else if (name.length > 64) {
		$('#' + control).fadeIn();
		$('#' + control).html("Sorry, your first name is too long");
		return false;
	} else {
		$('#' + control).hide();
		return true;
	}
}

function checkLastName(name, control) {
	name = $.trim(name);
	if (name === "") {
		$('#' + control).fadeIn();
		$('#' + control).html("Last name is required");
		return false;
	}
	else if (name.length > 64) {
		$('#' + control).fadeIn();
		$('#' + control).html("Sorry, your last name is too long");
		return false;
	} else {
		$('#' + control).hide();
		return true;
	}
}

function checkAddress(name, control) {
	name = $.trim(name);
	if (name === "") {
		$('#' + control).fadeIn();
		$('#' + control).html("Address is required");
		return false;
	}
	else if (name.length > 64) {
		$('#' + control).fadeIn();
		$('#' + control).html("Sorry, your address is too long");
		return false;
	} else {
		$('#' + control).hide();
		return true;
	}
}

function checkCity(name, control) {
	name = $.trim(name);
	if (name === "") {
		$('#' + control).fadeIn();
		$('#' + control).html("City is required");
		return false;
	}
	else if (name.length > 45) {
		$('#' + control).fadeIn();
		$('#' + control).html("Sorry, your city is too long");
		return false;
	} else {
		$('#' + control).hide();
		return true;
	}
}
function checkPostalCode(name, control) {
	name = $.trim(name);
	if (name === "") {
		$('#' + control).fadeIn();
		$('#' + control).html("Postal code is required");
		return false;
	}
	else if (name.length > 7) {
		$('#' + control).fadeIn();
		$('#' + control).html("Sorry, your postal code can only contain 6 characters (ex: K2K 0A4");
		return false;
	} else {
		$('#' + control).hide();
		return true;
	}
}
function checkHomePhone(name, control) {
	name = $.trim(name);
	if (name === "") {
		$('#' + control).fadeIn();
		$('#' + control).html("Home phone number is required");
		return false;
	}
	else if (name.length > 16) {
		$('#' + control).fadeIn();
		$('#' + control).html("Sorry, your home phone number cannot contain more then 16 numbers");
		return false;
	} else {
		$('#' + control).hide();
		return true;
	}
}
function checkBusinessPhone(name, control) {
	name = $.trim(name);
	if (name.length > 16) {
		$('#' + control).fadeIn();
		$('#' + control).html("Sorry, your business phone number cannot contain more then 16 numbers");
		return false;
	} else {
		$('#' + control).hide();
		return true;
	}
}
function checkPasswordHintQuestion(name, control) {
	name = $.trim(name);
	if (name === "") {
		$('#' + control).fadeIn();
		$('#' + control).html("Password hint question is required");
		return false;
	}
	else if (name.length > 64) {
		$('#' + control).fadeIn();
		$('#' + control).html("Sorry, your password hint question is too long 64 character maximum");
		return false;
	} else {
		$('#' + control).hide();
		return true;
	}
}
function checkPasswordHintAnswer(name, control) {
	name = $.trim(name);
	if (name === "") {
		$('#' + control).fadeIn();
		$('#' + control).html("Password hint answer is required");
		return false;
	}
	else if (name.length > 64) {
		$('#' + control).fadeIn();
		$('#' + control).html("Sorry, password hint answer is too long 64 character maximum");
		return false;
	} else {
		$('#' + control).hide();
		return true;
	}
}