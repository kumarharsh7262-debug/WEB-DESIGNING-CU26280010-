<?php
use PHPMailer\PHPMailer\PHPMailer;

/** Q14: send e-mail through SMTP using PHPMailer (composer require phpmailer/phpmailer). */
class Mailer
{
    public static function send(string $to, string $subject, string $htmlBody): bool
    {
        if (!class_exists(PHPMailer::class)) {
            error_log('Mailer: PHPMailer not installed. Run "composer install".');
            return false;
        }
        $c = config('mail');
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $c['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $c['user'];
            $mail->Password   = $c['pass'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $c['port'];
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($c['from'], $c['from_name']);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);
            $mail->send();
            return true;
        } catch (Throwable $e) {
            error_log('Mailer error: ' . $e->getMessage());   // logged, never shown to visitors
            return false;
        }
    }

    public static function welcome(string $to, string $name): bool
    {
        $n = e($name);
        return self::send($to, 'Welcome to ' . config('app.name'),
            "<h2>Welcome, $n!</h2><p>Your registration was successful. You can now log in.</p>");
    }

    public static function passwordReset(string $to, string $link): bool
    {
        $mins = (int)config('security.reset_minutes');
        $l = e($link);
        return self::send($to, 'Reset your password',
            "<p>We received a request to reset your password.</p>"
          . "<p><a href=\"$l\">Click here to choose a new password</a> (valid for $mins minutes).</p>"
          . "<p>If you did not request this, ignore this e-mail.</p>");
    }
}
