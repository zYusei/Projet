<?php
// send_email.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php'; // Charge PHPMailer et phpdotenv

// Charger les variables d'environnement depuis .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

/**
 * Envoie l'email de vérification via SMTP.
 * @return bool true si envoyé, false sinon (erreur logguée)
 */
function sendVerificationEmail(string $toEmail, string $toName, string $verifyUrl): bool {
    $mail = new PHPMailer(true);
    try {
        // === SMTP (config depuis .env) ===
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)$_ENV['SMTP_PORT'];

        // === Entêtes ===
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($toEmail, $toName);
        $mail->addReplyTo($_ENV['SMTP_FROM_EMAIL'], 'Support');

        // === Contenu ===
        $mail->isHTML(true);
        $mail->Subject = "Vérifie ton email — FunCodeLab";
        $mail->Body = "
            <h2>Bienvenue {$toName} !</h2>
            <p>Confirme ton adresse en cliquant ci-dessous (valide 24h) :</p>
            <p><a href=\"{$verifyUrl}\">{$verifyUrl}</a></p>
            <p>Si tu n'es pas à l'origine de cette création de compte, ignore ce message.</p>
        ";
        $mail->AltBody = "Bienvenue {$toName} !\nConfirme ton adresse : {$verifyUrl}\n(Lien valide 24h)";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('PHPMailer error: ' . $mail->ErrorInfo);
        return false;
    }
}
