<?php
error_reporting(-1);
ini_set('display_errors', 'On');
set_error_handler("var_dump");


if(!isset($_POST['email']) || empty($_POST['email'])){
    echo "Please enter the email";
    exit;
}


if(!isset($_POST['filename']) || empty($_POST['filename'])){
    echo "Please enter the filename";
    exit;
}


$name = $_FILES['filename']["name"];
$tmpName = $_FILES['filename']["tmp_name"];
$type = $_FILES['filename']["type"];
$size = $_FILES['filename']["size"];
$errorMsg = $_FILES['filename']["error"];
$explode = explode(".", $name);
$extension = end($explode);

if (!$tmpName) {
    echo "ERROR: Please choose file";
    exit();
} else if ($size > 5242880) {
    echo "ERROR: Please choose less than 5MB file for uploading";
    unlink($tmpName);
    exit();
} else if (!preg_match("/\.(jpg|png|jpeg)$/i", $name)) {
    echo "ERROR: Please choose the file only with the JPEG, PNG or JPG file format";
    unlink($tmpName);
    exit();
} else if ($errorMsg == 1) {
    echo "ERROR: An unexpected error occured while processing the file. Please try again.";
    exit();
}

$uploaddir = __DIR__ . '/uploads/';
$uploadfile = $uploaddir . basename($_FILES['filename']['name']);

if (!file_exists($uploaddir)) {
    mkdir($uploaddir, 0777, true);
}

$moveFile = move_uploaded_file($tmpName, $uploadfile);

if ($moveFile != true) {
    echo "ERROR: File not uploaded. Please try again";
    unlink($tmpName);
    exit();
}

include_once("upld_fn.php");
$target = "uploads/$name";
$resize = "uploads/resized_$name" . time();

$max_height = 360;
$max_width  = 640;

upld_fn($target, $resize, $max_width, $max_height, $extension);

// echo "<h2>Original image:-</h2> ";
// echo "<img src='uploads/$name' /> <br/>";
// echo "<h2>Resized image:-</h2> ";
$img_path = "uploads/resized_$name" . time();
// echo "<img src='$img_path' />";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";

//PHPMailer Object
$mail = new PHPMailer(true); //Argument true in constructor enables exceptions
$mail->From = $_POST['email'];
$mail->FromName = "Test";

$mail->addAddress($_POST['email']);
$mail->isHTML(true);

$mail->Subject = "Subject Text";
$mail->Body = "<i>Mail body in HTML</i>";
$mail->AltBody = "This is the plain text version of the email content";

try {
    $w = $mail->send();
    echo "Message has been sent successfully";
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}

$response = ["filepath"=>$img_path];
echo json_encode($response);


