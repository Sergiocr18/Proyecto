<?php
//mysqli("localhost", "root", "", "pbd");
//mysqli("sql4.freemysqlhosting.net","sql4499633","TdBZd7YWCv","sql4499633");
require_once $_SERVER["DOCUMENT_ROOT"] . "/conf/contraseñas.php";
date_default_timezone_set('Europe/Madrid');
// define("RECAPTCHA_V3_SECRET_KEY", '6LelHlMgAAAAAHjZutAKd5uDPZNbKiCDlAZyDRXy');
$myObj = new stdClass();
switch ($_POST['api']) {

    case "checkEmail":
        $email = sanitize($_POST['email']);
        checkEmail($email, $myObj);

        if (isset($myObj->success)) {
            checkCaptcha($email, sanitize($_POST['name']), sanitize($_POST['apellidos']), sanitize($_POST['c_autonoma']), sanitize($_POST['años']), sanitize($_POST['phone']), sanitize($_POST['checkbox1']), sanitize($_POST['checkbox2']), sanitize($_POST['checkbox3']), sanitize($_POST['captcha']), $myObj);
        }
        break;
    default:
        $myObj->error = "error en el switchCase";
        break;
}
// echo json_encode($myObj);
function sanitize($texto)
{
    return htmlentities(strip_tags($texto), ENT_QUOTES, 'UTF-8');
}
function checkEmail($email, $myObj)
{
    $email = strtolower(str_replace(" ", "", trim($email)));
    if ($email == "" || is_numeric($email)) {
        $myObj->error = "el email esta vacio o es un numero";
    }
    //if(email is email(reg)){}sql4499633
    //
    $conn = new mysqli(DB_URL, DB_USER, DB_PASSWORD, DB_DATABASE);
    $sql = "SELECT email FROM usuarios WHERE email='" . $email . "' ;";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        while ($row = $result->fetch_assoc()) {
            $myObj->error = "Ya existe el email: " . $row['email'];
        }
    } else {
        $myObj->success = "email is OK";
        // echo "Error:<br>";
        // print_r($result);
        // echo "<br>" . $sql . "<br>";
    }
    $conn->close();
}

function checkCaptcha($email, $nombre, $apellidos, $c_autonoma, $años, $phone, $checkbox1, $checkbox2, $checkbox3, $captcha, $myObj)
{
    $response = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=" . RECAPTCHA_V3_SECRET_KEY . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']
    );
    $response = json_decode($response);
    if ($response->success === false) {
        //Do something with error
        $myObj->error = "Error captcha";
    } else {
        if ($response->success == true && $response->score > 0.5) {
            $myObj->success = " Captcha Ok";
            insertUser($email, $nombre, $apellidos, $c_autonoma, $años, $phone, $checkbox1, $checkbox2, $checkbox3, $myObj);
        } else if ($response->success == true && $response->score <= 0.5) {
            $myObj->error = "eres humano?";
        } else {
            $myObj->error = "NO eres humano?";
        }
    }
}

function insertUser($email, $nombre, $apellidos, $c_autonoma, $años, $phone, $checkbox1, $checkbox2, $checkbox3, $myObj)
{

    $conn = new mysqli(DB_URL, DB_USER, DB_PASSWORD, DB_DATABASE);
    $sql = "INSERT INTO usuarios_temp (email, nombre, apellidos, c_autonoma, age, phone, checkbox1, checkbox2, checkbox3) VALUES ('" . $email . "','" . $nombre . "','" . $apellidos . "','" . $c_autonoma . "'," . $años . "," . $phone . "," . $checkbox1 . "," . $checkbox2 . "," . $checkbox3 . ")";
    //echo $sql;
    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        echo "insert table \"usuarios\"<br>";
        echo $last_id;
        enviarmail($email, $myObj);
    } else {
        echo "Error: insert table \"usuarios\" " . $conn->error . " <br>" . $sql . "<br>";
        $myObj->error = "Usuario NO guardado en la DB";
    }
    $conn->close();
};

function enviarmail($email, $myObj)
{
    $usuario = new stdClass();
    $conn = new mysqli(DB_URL, DB_USER, DB_PASSWORD, DB_DATABASE);
    $sql = "SELECT * FROM usuarios_temp  WHERE email='" . $email . "' ORDER BY id DESC LIMIT 1;";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        while ($row = $result->fetch_assoc()) {
            $usuario->id = $row['id'];
            $usuario->email = $row['email'];
            $usuario->nombre = $row['nombre'];
            $usuario->apellidos = $row['apellidos'];
            $usuario->c_autonoma = $row['c_autonoma'];
            $usuario->age = $row['age'];
            $usuario->phone = $row['phone'];
            $usuario->checkbox1 = $row['checkbox1'];
            $usuario->checkbox2 = $row['checkbox2'];
            $usuario->checkbox3 = $row['checkbox3'];
            $usuario->reg_date = $row['reg_date'];
            break;
        }
        $conn->close();
        $xstring = $usuario->id . "-" . $usuario->email . "-" . $usuario->nombre . "-" . $usuario->apellidos . "-" . $usuario->c_autonoma . "-" . $usuario->age . "-" . $usuario->phone . "-" . $usuario->checkbox1 . "-" . $usuario->checkbox2 . "-" . $usuario->checkbox3 . "-" . $usuario->reg_date;
        $sha1 = sha1($xstring);
        //echo $sha1;
        sendMail($usuario, $sha1, $myObj);
    } else {
        $conn->close();
        echo "Error:<br>";
        print_r($result);
        echo "<br>" . $sql . "<br>";
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($usuario, $sha1, $myObj)
{
    $HostSMTP = 'smtp.gmail.com'; // Set the SMTP server to send through
    $ContrasenaDelCorreo = 'ilvwgutnjnoseklo'; // SMTP password
    $SendFromEMAIL = 'sergioencinedo18@gmail.com'; // SMTP username
    $QuienLoEnviaNAME = 'moderator';
    $SendFromEMAILreply = 'sergioencinedo18@gmail.com';
    $QuienResponderNAME = 'moderator';
    $PortSMTP = 465; // TCP port to connect to
    //$PortSMTP = 587; // TCP port to connect to
    //
    $SentToEmail = $usuario->email;
    $Asunto = "ninguno";
    $BodyHTML = '<h1> Ya estas registrado </h1>  <br /><a href="http://' . $_SERVER['HTTP_HOST'] . '/new_user.php?id=' . $usuario->id . '&clave=' . $sha1 . '"><b>' . $sha1 . '</b></a>';
    $BodyNOHTML = "hola que tal?";


    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function


    //Load Composer's autoloader
    require realpath($_SERVER["DOCUMENT_ROOT"]) . '/vendor/autoload.php';

    //Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_CONNECTION;                      //Enable verbose debug output
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $HostSMTP;                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $SendFromEMAIL;                     //SMTP username
        $mail->Password   = $ContrasenaDelCorreo;                               //SMTP password
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $PortSMTP;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom($SendFromEMAIL, $QuienLoEnviaNAME);
        //$mail->addAddress($SentToEmail, 'Joe User');     //Add a recipient
        $mail->addAddress($SentToEmail);               //Name is optional
        $mail->addReplyTo($SendFromEMAIL, $QuienLoEnviaNAME);
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $Asunto;
        $mail->Body    = $BodyHTML;
        $mail->AltBody = $BodyNOHTML;

        $mail->send();

        $myObj->success = 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
