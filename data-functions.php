<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
//mailing list data capture functions
if($_SERVER["REQUEST_METHOD"] == "POST") {
    //Sanitize strings to prevent SQL injection
    $name = $_POST["user-firstname"];
    $surname = $_POST["user-surname"];
    $email = $_POST["user-email"];
    

    //Sanitize strings to prevent SQL injection
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $surname = filter_var($surname, FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_SANITIZE_STRING);

    //Double existing entry validation incase Bootstrap fails
    if(empty($name)) {
        error_msg("Please provide your name.");
    }

    if(empty($surname)) {
        error_msg("Please provide your surname.");
    }

    //Also double check email is a valid email format
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_msg("Please provide your email address.");
    }

    $mysqli = connect();

    if($mysqli->connect_error) {
        error_msg("Database Error: " .$mysqli->connect_error);
    }

    // ~~ send email confirmation (double opt-in) ~~

    //generate confirmation key
    $confirmation_key = $name . $email . date('mY');
    $confirmation_key = md5($confirmation_key);
    
    $sql = "INSERT INTO `confirmation` (user_firstname, user_surname, user_email, confirm) VALUES (?, ?, ?, ?)";
    //Store our confirmation key and user details so we can access this later for confirming
    if($statement = $mysqli->prepare($sql)) {
        $statement->bind_param('ssss', $name, $surname, $email, $confirmation_key);
        //todo: actually return responses 

        if($statement->execute()) {
            //send confirmation email
            send_confirm_mail($email, $confirmation_key);
        } else {
            error_msg("Database Error: " . $mysqli->error);
        }
    } else {
        error_msg("Database Error: " . $mysqli->error);
    }
}

function error_msg($msg) {
    $_SESSION["RESPONSE"] = "register-failed";
    $_SESSION["ERROR_MSG"] = $msg;
    header('Location: /ee/');
    die();
}

function send_confirm_mail($email, $confirmation_key) {
    $mail_subject = 'Confirm Your Email';
    
    $mail_msg = "

    Thanks for signing up for our mailing list.

    Please click this link to verify your email address:
    http://localhost/ee/confirm.php?email=" . $email . "&key=" . $confirmation_key
    . "
    Not you? Click this link to cancel this registration:
    http://localhost/ee/cancel.php?email=" . $email . "&key=" . $confirmation_key;
    
    $mail_headers = 'From:sean.ryan@aceville.co.uk';

    if(mail($email, $mail_subject, $mail_msg, $mail_headers)) {
        $_SESSION["RESPONSE"] = "register-success";
        header('Location: /ee/');
    } else {
        error_msg("Failed to send email.");
    }
}

function connect() {
    static $connection;

    if(!isset($connection)) {
        //Store credentials in and parse from an external config for security 
        $config = parse_ini_file("C:/xampp/private/config.ini");
        $connection = new mysqli($config['servername'], $config['username'], $config['password'], $config["dbname"]);
    }

    if($connection == false) {
        error_msg("Database Error: " . $mysqli->connect_error);
    }

    return $connection;
}
?>