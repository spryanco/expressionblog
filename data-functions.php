<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//mailing list data capture functions

if($_SERVER["REQUEST_METHOD"] == "POST") {
    //Sanitize strings to prevent SQL injection
    $name = filter_var($_POST["user-firstname"], FILTER_SANITIZE_STRING);
    $surname = filter_var($_POST["user-surname"], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST["user-email"], FILTER_SANITIZE_STRING);

    //Double existing entry validation incase Bootstrap fails
    if(empty($name)) {
        die("Please provide your name.");
    }

    if(empty($surname)) {
        die("Please provide your surname.");
    }

    //Also double check email is a valid email format
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Please provide your email address.");
    }

    $mysqli = connect();

    if($mysqli->connect_error) {
        die("Error Occured - Connection: " . $mysqli->connect_error);
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
            print "success";

            //send confirmation email
            sendConfirmEmail($email, $confirmation_key);
        } else {
            print "error";
            die("Error Occured: " . $mysqli->error);
        }
    } else {
        print "error";
        die("Error Occured: " . $mysqli->error);
    }
}

function send_confirm_mail($email, $confirmation_key) {
    
}

//todo: move this to confirm.php & provide link in email
function confirm() {
    if(empty($_GET['email']) || empty($_GET['key'])) {
        //confirm both strings parsed to our confirmation
        
        die();
    }

    $email = mysql_real_escape_string($_GET['email']);
    $key = mysql_real_escape_string($_GET['key']);

    $mysqli = connect();

    if($mysqli->connect_error) {
        die("Error Occured - Connection failed: " . $mysqli->connect_error);
    }

    //check our key matches a key & email combination in the confirm db
    $db_key = mysql_query("SELECT * FROM `confirmation` WHERE `user_email` = '$email' AND `confirm` = '$key' LIMIT 1") or die(mysql_error());

    if(mysql_num_rows($db_key) != 0) {
        $row = $db_key->fetch_assoc();

        $statement = $mysqli->prepare("INSERT INTO 'capture_data' (user_firstname, user_surname, user_email) VALUES (?, ?, ?);");
        $statement->bind_param('sss', $name, $surname, $email);

        if($statement->execute()) {
            //success
            print "success";

             //User is confirmed so delete the confirm row for their email/key pair
            $delete = mysql_query("DELETE FROM `confirmation` WHERE `user_email` = '$email' AND `confirm` = '$key'");
        } else {
            print $mysqli->error;
        }
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
        print mysqli_connect_error(); 
    }

    return $connection;
}
?>