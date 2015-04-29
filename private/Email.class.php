<?php
/**
 * Email.class.php
 * 
 * This file runs all emails that are sent through To_BE
 * Types of emails:
 *
 * sendWelcomeEmail - When a new user signs up for the first time
 * sendWelcomeEmail($receiverFirstName, $receiverEmail, $confirmationCode)
 *
 *
 * sendNewPassword - Sends user his new password when they reset it
 * sendNewPassword($receiverFirstName, $receiverEmail, $newPassword)
 *
 *
 * 
 * @author Kirk Davies
 * 
 */

class email
{
	public $message = null;
	public $receiverEmail = null;
	public $messageContent = null;
	public $subject = null;
	public $headers = null;
	public $newWidth = null;
	public $newHeight = null;
	# set the logo URL
	public $logo = 'http://kirkdavies.me/img/mtc_logo.svg';
	public $messageStyle = 
	'<style>
        /* -------------------------------------
    GLOBAL
------------------------------------- */
* {
  margin: 0;
  padding: 0;
  font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
  box-sizing: border-box;
  font-size: 14px;
}

img {
  max-width: 100%;
}

body {
  -webkit-font-smoothing: antialiased;
  -webkit-text-size-adjust: none;
  width: 100% !important;
  height: 100%;
  line-height: 1.6;
}

/* Lets make sure all tables have defaults */
table td {
  vertical-align: top;
}

/* -------------------------------------
    BODY & CONTAINER
------------------------------------- */
body {
  background-color: #f6f6f6;
}

.body-wrap {
  background-color: #f6f6f6;
  width: 100%;
}

.container {
  display: block !important;
  max-width: 600px !important;
  margin: 0 auto !important;
  /* makes it centered */
  clear: both !important;
}

.content {
  max-width: 600px;
  margin: 0 auto;
  display: block;
  padding: 20px;
}

/* -------------------------------------
    HEADER, FOOTER, MAIN
------------------------------------- */
.main {
  background: #fff;
  border: 1px solid #e9e9e9;
  border-radius: 3px;
}

.content-wrap {
  padding: 20px;
}

.content-block {
  padding: 0 0 20px;
}

.header {
  width: 100%;
  margin-bottom: 20px;
}

.footer {
  width: 100%;
  clear: both;
  color: #999;
  padding: 20px;
}
.footer a {
  color: #999;
}
.footer p, .footer a, .footer unsubscribe, .footer td {
  font-size: 12px;
}

/* -------------------------------------
    GRID AND COLUMNS
------------------------------------- */
.column-left {
  float: left;
  width: 50%;
}

.column-right {
  float: left;
  width: 50%;
}

/* -------------------------------------
    TYPOGRAPHY
------------------------------------- */
h1, h2, h3 {
  font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
  color: #000;
  margin: 40px 0 0;
  line-height: 1.2;
  font-weight: 400;
}

h1 {
  font-size: 32px;
  font-weight: 500;
}

h2 {
  font-size: 24px;
}

h3 {
  font-size: 18px;
}

h4 {
  font-size: 14px;
  font-weight: 600;
}

p, ul, ol {
  margin-bottom: 10px;
  font-weight: normal;
}
p li, ul li, ol li {
  margin-left: 5px;
  list-style-position: inside;
}

/* -------------------------------------
    LINKS & BUTTONS
------------------------------------- */
a {
  color: #348eda;
  text-decoration: underline;
}

.btn-primary {
  text-decoration: none;
  color: #FFF;
  background-color: #348eda;
  border: solid #348eda;
  border-width: 10px 20px;
  line-height: 2;
  font-weight: bold;
  text-align: center;
  cursor: pointer;
  display: inline-block;
  border-radius: 5px;
  text-transform: capitalize;
}

/* -------------------------------------
    OTHER STYLES THAT MIGHT BE USEFUL
------------------------------------- */
.last {
  margin-bottom: 0;
}

.first {
  margin-top: 0;
}

.padding {
  padding: 10px 0;
}

.aligncenter {
  text-align: center;
}

.alignright {
  text-align: right;
}

.alignleft {
  text-align: left;
}

.clear {
  clear: both;
}

/* -------------------------------------
    Alerts
------------------------------------- */
.alert {
  font-size: 16px;
  color: #fff;
  font-weight: 500;
  padding: 20px;
  text-align: center;
  border-radius: 3px 3px 0 0;
}
.alert a {
  color: #fff;
  text-decoration: none;
  font-weight: 500;
  font-size: 16px;
}
.alert.alert-warning {
  background: #ff9f00;
}
.alert.alert-bad {
  background: #d0021b;
}
.alert.alert-good {
  background: #68b90f;
}

/* -------------------------------------
    INVOICE
------------------------------------- */
.invoice {
  margin: 40px auto;
  text-align: left;
  width: 80%;
}
.invoice td {
  padding: 5px 0;
}
.invoice .invoice-items {
  width: 100%;
}
.invoice .invoice-items td {
  border-top: #eee 1px solid;
}
.invoice .invoice-items .total td {
  border-top: 2px solid #333;
  border-bottom: 2px solid #333;
  font-weight: 700;
}

/* -------------------------------------
    RESPONSIVE AND MOBILE FRIENDLY STYLES
------------------------------------- */
@media only screen and (max-width: 640px) {
  h1, h2, h3, h4 {
    font-weight: 600 !important;
    margin: 20px 0 5px !important;
  }

  h1 {
    font-size: 22px !important;
  }

  h2 {
    font-size: 18px !important;
  }

  h3 {
    font-size: 16px !important;
  }

  .container {
    width: 100% !important;
  }

  .content, .content-wrapper {
    padding: 10px !important;
  }

  .invoice {
    width: 100% !important;
  }
}
        </style>';

	//To avoid an error if a script attempts to output email object as a string use this method
	public function __toString()
	{
		return "Error: Attempting to output object as a string.";
	}

	
/**
 		sets header/footer of emails that go to end users
 */
	public function setupMessage()
	{
		$this->message =
		'
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Welcome to the March Tennis Club</title>' .
$this->messageStyle .

'</head>

<body>'.

$this->messageContent .

'
		<div class="footer">
			<table width="100%">
				<tr>
					<td class="content-block">Follow <a href="http://twitter.com/marchtennisclub">@MarchTennisClub</a> on Twitter.</td>
				</tr>
				<tr>
					<td class="content-block">Like <a href="http://facebook.com/marchtennisclub">March Tennis Club</a> on Facebook.</td>
				</tr>
			</table>
		</div></div>
		</td>
		<td></td>
	</tr>
</table>

</body>
</html>
';
	}

	public function sendWelcomeEmail($receiverFirstName, $receiverEmail, $confirmationCode)
	{
		
		$this->subject = "Welcome to the March Tennis Club";
		$this->receiverEmail = $receiverEmail;
		
		$this->messageContent = 
			    '<table class="body-wrap">
	<tr>
		<td></td>
		<td class="container" width="600">
			<div class="content">
				<table class="main" width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td class="content-wrap">
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td class="content-block">
										Thank you for becoming a March Tennis Club Member.  We are looking forward to a great tennis season.  Please make a cheque payable to <strong>"March Tennis Club"</strong> for the amount of <strong>$169</strong> to complete your registration process.
									</td>
								</tr>
								
								<tr>
									<td class="content-block">
										Please confirm your email address by clicking the link below.
									</td>
								</tr>
								<tr>
									<td class="content-block">
										It is important to us that we have an accurate email address.  Once membership payment has been made and your email address has been confirmed you will have access to our new online court reservation system.
									</td>
								</tr>
								<tr>
									<td class="content-block">
										<a href="http://www.marchtennisclub.com/confirmemail.php" class="btn-primary">Confirm email address</a>
									</td>
								</tr>
								<tr>
									<td class="content-block">
										&mdash; The March Tennis Club
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>';
		
	    #Send the email
	    return $this->sendEmail();
	}

	public function sendNewPassword($receiverFirstName, $receiverEmail, $newPassword)
	{
		$this->subject = "Password Reset";
		$this->receiverEmail = $receiverEmail;
		$this->messageContent = 
			"<div id='content'>
				<h1>Hi $receiverFirstName, your password has been reset</h1>
		        <h3>You can now log in using: <strong>$newPassword</strong></h3>
		        <p>To change your password, log in and click the top right menu button and go to Settings -> Account -> Change Password</p>
			</div>";

		#Send the email
	    return $this->sendEmail();
	}

	private function sendEmail()
	{
		#Before we send any email, set the headers
		$this->headers .= "MIME-Version: 1.0\r\n";
    	$this->headers .= "Content-type: text/html; charset=utf8\r\n";
    	$this->headers .= "From: admin@marchtennisclub.com\r\n";
    	$this->headers .= "Reply-To: admin@marchtennisclub.com\r\n" . 'X-Mailer: PHP/' . phpversion();
    	$this->headers .= "Return-Path: admin@marchtennisclub.com\r\n";
    	#setup Message
	    $this->setupMessage();
    	#Send email
		$send = mail($this->receiverEmail, $this->subject, $this->message, $this->headers);
		
		
		
		
		return $send;
	}
}

$email = new email();
$email->sendWelcomeEmail('Tester', 'kirkdavies@rogers.com', '123');

?>
