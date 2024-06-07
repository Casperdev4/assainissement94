<?php
header('Content-Type: text/html; charset=UTF-8');

function contains_links($text) {
    $linkPattern = "/https?:\/\/[^\s]+|<a\s+href\s*=\s*['\"]?[^\s>]+['\"]?/i";
    $scriptPattern = "/<script[^>]*>[\s\S]*?<\/script>/i";
    return preg_match($linkPattern, $text) || preg_match($scriptPattern, $text);
}

function is_empty($field) {
    return !isset($field) || trim($field) === '';
}

$nom = htmlspecialchars($_POST['nom'], ENT_QUOTES, 'UTF-8');
$telephone = htmlspecialchars($_POST['telephone'], ENT_QUOTES, 'UTF-8');
$services = htmlspecialchars($_POST['services'], ENT_QUOTES, 'UTF-8');
$commentaires = htmlspecialchars($_POST['commentaires'], ENT_QUOTES, 'UTF-8');

if (is_empty($nom) || is_empty($telephone) || is_empty($services) || is_empty($commentaires)) {
    echo "Tous les champs sont obligatoires.";
    exit();
}

if (contains_links($commentaires)) {
    echo "Les liens ne sont pas autorisés.";
    exit();
}

$message = "Nom : $nom \n";
$message .= "Téléphone : $telephone \n";
$message .= "Prestation : $services \n";
$message .= "Commentaires : $commentaires \n";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.ionos.fr';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'contact@webprime.fr';
    $mail->Password   = 'Allamalyjass912!';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    $mail->setFrom('contact@webprime.fr', 'Assainissement 94');
    $mail->addAddress('contact.aquaserv@gmail.com');
    $mail->addAddress('contact@webprime.fr');
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);
    $mail->Subject = 'Formulaire 94';
    $mail->Body    = nl2br($message);
    $mail->AltBody = $message;

    $mail->send();

    header('Location: index.html');
    exit();
} catch (Exception $e) {
    echo "Message non envoyé. Erreur Mailer: {$mail->ErrorInfo}";
}
?>
