<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_otp = $_POST['otp'];
    $email = "iftikharazharchaudhry@gmail.com";

    $result = $conn->query("SELECT otp FROM otp_codes WHERE email='$email' AND expiry > NOW()");
    $row = $result->fetch_assoc();

    if ($row && $row['otp'] == $input_otp) {
        header("Location: dashboard.php");
        echo "✅ OTP Benar!";
    } else {
        echo "❌ OTP Salah atau Kadaluarsa!";
    }
}
?>