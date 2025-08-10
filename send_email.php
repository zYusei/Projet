<?php
// send_email.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/lib/PHPMailer/src/Exception.php';
require __DIR__ . '/lib/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/lib/PHPMailer/src/SMTP.php';

/**
 * Envoie l'email de vérification via SMTP.
 * @return bool true si envoyé, false sinon (erreur logguée)
 */
function sendVerificationEmail(string $toEmail, string $toName, string $verifyUrl): bool {
    $mail = new PHPMailer(true);
    try {
        // === SMTP (exemple Gmail - conseillé en dev) ===
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'yuseiezzz6@gmail.com';        // <-- CHANGE
        $mail->Password   = 'pfvz ktwm weqi cojh';  // <-- CHANGE (mot de passe d’application)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // === Entêtes ===
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('yuseiezzz6@gmail.com', 'FunCodeLab');     // adresse existante
        $mail->addAddress($toEmail, $toName);
        $mail->addReplyTo('support@funcodelab.com', 'Support');    // optionnel

        // === Contenu ===
        $mail->isHTML(true);
        $mail->Subject = "Vérifie ton email — FunCodeLab";
        $mail->Body = "
            <h2>Bienvenue $toName !</h2>
            <p>Confirme ton adresse en cliquant ci-dessous (valide 24h) :</p>
            <p><a href=\"$verifyUrl\">$verifyUrl</a></p>
            <p>Si tu n'es pas à l'origine de cette création de compte, ignore ce message.</p>
        ";
        $mail->AltBody = "Bienvenue $toName !\nConfirme ton adresse : $verifyUrl\n(Lien valide 24h)";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('PHPMailer error: ' . $mail->ErrorInfo);
        return false;
    }
}
