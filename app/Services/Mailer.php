<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

final class Mailer
{
  public static function sendWithPdf(string $toEmail, string $subject, string $htmlBody, string $pdfBytes, string $pdfName): void
  {
    $mail = new PHPMailer(true);

    $host = $_ENV['SMTP_HOST'] ?? '127.0.0.1';
    $port = (int)($_ENV['SMTP_PORT'] ?? 25);
    $user = $_ENV['SMTP_USER'] ?? '';
    $pass = $_ENV['SMTP_PASS'] ?? '';
    $secure = $_ENV['SMTP_SECURE'] ?? ''; // tls|ssl|''

    $fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? 'no-reply@localhost';
    $fromName  = $_ENV['SMTP_FROM_NAME'] ?? 'Libro de Reclamaciones';

    try {
      $mail->CharSet = 'UTF-8';
      $mail->isSMTP();
      $mail->Host = $host;
      $mail->Port = $port;

      if ($secure !== '') {
        $mail->SMTPSecure = $secure;
      }

      if ($user !== '') {
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pass;
      } else {
        $mail->SMTPAuth = false;
      }

      $mail->setFrom($fromEmail, $fromName);
      $mail->addAddress($toEmail);

      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $htmlBody;
      $mail->AltBody = strip_tags($htmlBody);

      $mail->addStringAttachment($pdfBytes, $pdfName, 'base64', 'application/pdf');

      $mail->send();
    } catch (Exception $e) {
      throw new \RuntimeException('No se pudo enviar email: ' . $mail->ErrorInfo, 0, $e);
    }
  }

  public static function sendHtml(array $toEmails, string $subject, string $htmlBody, array $attachments = []): void
  {
    $mail = new PHPMailer(true);

    $host = $_ENV['SMTP_HOST'] ?? '127.0.0.1';
    $port = (int)($_ENV['SMTP_PORT'] ?? 25);
    $user = $_ENV['SMTP_USER'] ?? '';
    $pass = $_ENV['SMTP_PASS'] ?? '';
    $secure = $_ENV['SMTP_SECURE'] ?? ''; // tls|ssl|''

    $fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? 'no-reply@localhost';
    $fromName  = $_ENV['SMTP_FROM_NAME'] ?? 'Libro de Reclamaciones';

    try {
      
      $mail->CharSet = 'UTF-8';
      $mail->isSMTP();
      $mail->Host = $host;
      $mail->Port = $port;

      if ($secure !== '') {
        $mail->SMTPSecure = $secure;
      }

      if ($user !== '') {
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pass;
      } else {
        $mail->SMTPAuth = false;
      }

      $mail->setFrom($fromEmail, $fromName);

      foreach ($toEmails as $email) {
        $email = trim((string)$email);
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $mail->addAddress($email);
        }
      }

      if (count($mail->getToAddresses()) === 0) {
        throw new \RuntimeException('Mailer: no hay destinatarios válidos.');
      }

      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $htmlBody;
      $mail->AltBody = strip_tags($htmlBody);

      // Adjuntos: array de ['path' => '/ruta/archivo.pdf', 'name' => 'archivo.pdf']
      foreach ($attachments as $a) {
        if (!is_array($a)) continue;
        $path = $a['path'] ?? '';
        $name = $a['name'] ?? '';
        if ($path && is_file($path)) {
          $mail->addAttachment($path, $name ?: basename($path));
        }
      }

      $mail->send();
    } catch (Exception $e) {
      throw new \RuntimeException('No se pudo enviar email: ' . $mail->ErrorInfo, 0, $e);
    }
  }
}
