<?php
if(empty($_GET['email']) || empty($_GET['key'])) {
    //confirm both strings parsed to our confirmation
    
    die();
}

$mysqli = connect();

$email = filter_var($_GET["email"], FILTER_SANITIZE_STRING);
$key = filter_var($_GET["key"], FILTER_SANITIZE_STRING);

if($mysqli->connect_error) {
    die("Error Occured - Connection failed: " . $mysqli->connect_error);
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
        print "success you have been added";

            //User is confirmed so delete the confirm row for their email/key pair
        $delete = $mysqli->query("DELETE FROM `confirmation` WHERE `user_email` = '$email' AND `confirm` = '$key'");
    } else {
        print $mysqli->error;
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