<?php
header('Content-Type: text/html; charset=UTF-8');

function contient_liens($texte) {
    $patternLien = "/https?:\/\/[^\s]+|<a\s+href\s*=\s*['\"]?[^\s>]+['\"]?/i";
    $patternScript = "/<script[^>]*>[\s\S]*?<\/script>/i";
    return preg_match($patternLien, $texte) || preg_match($patternScript, $texte);
}

function est_vide($champ) {
    return !isset($champ) || trim($champ) === '';
}

function contient_cyrillique($texte) {
    return preg_match("/[\p{Cyrillic}]/u", $texte);
}

$nom = htmlspecialchars($_POST['nom'], ENT_QUOTES, 'UTF-8');
$telephone = htmlspecialchars($_POST['telephone'], ENT_QUOTES, 'UTF-8');
$services = htmlspecialchars($_POST['services'], ENT_QUOTES, 'UTF-8');
$commentaires = htmlspecialchars($_POST['commentaires'], ENT_QUOTES, 'UTF-8');

if (est_vide($nom) || est_vide($telephone) || est_vide($services) || est_vide($commentaires)) {
    echo "Tous les champs sont obligatoires.";
    exit();
}

if (contient_liens($commentaires) || contient_cyrillique($commentaires)) {
    echo "Pas autorisés.";
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
    $mail->addAddress('webprime91@hotmail.com');
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
