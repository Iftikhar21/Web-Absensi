<?php
session_start();
include "db.php"; // Koneksi database
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Pastikan PHPMailer terinstal
require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';


// Email tujuan (ganti sesuai kebutuhan)
$email = "iftikharazharchaudhry@gmail.com";

// Fungsi untuk generate OTP (6 digit)
function generateOTP($length = 6) {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

$otp = generateOTP();
$_SESSION['otp'] = $otp;
$_SESSION['otp_expiry'] = time() + 60; // Berlaku 1 menit

// Simpan ke database (Tabel `otp_codes`)
$sql = "INSERT INTO otp_codes (email, otp, expiry) 
        VALUES ('$email', '$otp', NOW() + INTERVAL 1 MINUTE) 
        ON DUPLICATE KEY UPDATE otp='$otp', expiry=NOW() + INTERVAL 1 MINUTE";

$conn->query($sql);

// Kirim OTP via Email
$mail = new PHPMailer(true);
try {
    // Konfigurasi SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Ganti sesuai penyedia email
    $mail->SMTPAuth   = true;
    $mail->Username   = 'recodeofficiall@gmail.com'; // Ganti dengan email pengirim
    $mail->Password   = 'ldisdnyipcaomfbu'; // Ganti dengan password email pengirim
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Pengirim & Penerima
    $mail->setFrom('recodeofficiall@gmail.com', 'OTP System');
    $mail->addAddress($email);

    // Konten Email
    $mail->isHTML(true);
    $mail->Subject = 'Kode OTP Anda';
    $mail->Body    = "Kode OTP Anda adalah: <b>$otp</b><br>OTP ini berlaku selama 1 menit.";

    $mail->send();
    echo "✅ OTP berhasil dikirim ke $email";
} catch (Exception $e) {
    echo "❌ Gagal mengirim OTP: {$mail->ErrorInfo}";
}
?>
