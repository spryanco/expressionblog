<?php
if(empty($_GET['email']) || empty($_GET['key'])) {
    //confirm both strings parsed to our confirmation
    
    die();
}

session_start();

$mysqli = connect();

$email = filter_var($_GET["email"], FILTER_SANITIZE_STRING);
$key = filter_var($_GET["key"], FILTER_SANITIZE_STRING);

if($mysqli->connect_error) {
    error_msg("Error Occured - Connection failed: " . $mysqli->connect_error);
}

//check our key matches a key & email combination in the confirm db
$db_key = $mysqli->query("SELECT * FROM `confirmation` WHERE `user_email` = '$email' AND `confirm` = '$key' LIMIT 1") or die(mysql_error());

if($db_key->num_rows != 0) {
    $row = $db_key->fetch_assoc();

    $sql = "INSERT INTO `capture_data` (user_firstname, user_surname, user_email) VALUES (?, ?, ?)";
    $statement = $mysqli->prepare($sql);
    $statement->bind_param('sss', $row["user_firstname"], $row["user_surname"], $email);

    if($statement->execute()) {
        //success
        $_SESSION["RESPONSE"] = "confirm-success";
        header('Location: /ee/');

        //User is confirmed so delete the confirm row for their email/key pair
        $delete = $mysqli->query("DELETE FROM `confirmation` WHERE `user_email` = '$email' AND `confirm` = '$key'");
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

function connect() {
    static $connection;

    if(!isset($connection)) {
        //Store credentials in and parse from an external config for security 
        $config = parse_ini_file("C:/xampp/private/config.ini");
        $connection = new mysqli($config['servername'], $config['username'], $config['password'], $config["dbname"]);
    }

    if($connection == false) {
        error_msg("Database Error: " .$mysqli->connect_error);
    }

    return $connection;
}
?>