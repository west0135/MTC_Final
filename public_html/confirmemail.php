<!DOCTYPE html>
<html>
<head>
    <title>March Tennis Club - Verify Email</title>
</head>
<body>

<?php

require "../../private/Db.class.php";

$db = new Db();

# get GET variables
$confirmation_code = !empty($_GET["confirmation_code"]) ? $_GET["confirmation_code"] : NULL;
$email = !empty($_GET["email"]) ? strtolower($_GET["email"]) : NULL;

if (isset($confirmation_code) && isset($email)) {
	
    $db->bindMore(array("email" => $email, "confirmation_code" => $confirmation_code));
    $record_exists = $db->single("SELECT EXISTS (SELECT 1 FROM mtc_email_confirm WHERE email = :email AND confirmation_code = :confirmation_code)");

    # the email + confirmation code combination exists in the DB
    if (!empty($record_exists)) {

        $db->bindMore(array("email" => $email, "confirmation_code" => $confirmation_code));
        $delete = $db->query("DELETE FROM mtc_email_confirm WHERE email = :email AND confirmation_code = :confirmation_code");

        if ($delete > 0) {

            echo "
                <div style='text-align: center;'>
                    <img style='width: 100%; max-width: 350px;' alt='Campusgrids' src='img/mtc_logo.png' />
                    <h1 style='font-size: 36px;margin: 15px;text-align: center;color: #666;vertical-align:baseline;'>The March Tennis Club</h1>
                    <p style='font-size: 1.3em;vertical-align:baseline;background:transparent;'>Thanks! The email <strong>$email</strong> has been verified.</p>
                </div>
            ";

        }

    }
    else {

            echo "
                <div style='text-align: center;'>
                    <img style='width: 100%; max-width: 350px;' alt='March Tennis Club' src='img/mtc_logo.png' />
                    <h1 style='font-size: 36px;margin: 15px;text-align: center;color: #666;vertical-align:baseline;'>The March Tennis Club</h1>
                    <p style='font-size: 1.3em;vertical-align:baseline;background:transparent;'>Sorry, the email <strong>$email</strong> is not registered or the confirmation code is incorrect.
                    <br />Please sign up on the website</p>
                </div>
            ";

        

    }

}

# close the db connection
$db->CloseConnection();

?>

</body>
</html>