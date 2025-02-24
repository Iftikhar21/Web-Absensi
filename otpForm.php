<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        body {
            font-family: "Poppins";
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            margin: 5px;
            border-radius: 5px;
        }

        .logo {
            width: 72px;
            height: 72px;
        }

    </style>
</head>
<body>
    <div class="card p-5 shadow-sm" style="max-width: 800px;">
        <div class="text-center mb-3">
            <div class="row">
                <div class="col-12">
                    <img class="logo" src="img/recode_logo.png">
                </div>
                <div class="col-12 mt-3">
                    <h1>Re-Code</h1>
                </div>
            </div>
        </div>
        <h4 class="text-center mb-3">OTP Verification</h4>
        <p class="text-center">Enter the 6-digit code sent to your email</p>
        <form action="verify_otp.php" method="POST" onsubmit="combineOTP()">
            <div class="d-flex justify-content-center">
                <input type="text" class="otp-input form-control" maxlength="1" required>
                <input type="text" class="otp-input form-control" maxlength="1" required>
                <input type="text" class="otp-input form-control" maxlength="1" required>
                <input type="text" class="otp-input form-control" maxlength="1" required>
                <input type="text" class="otp-input form-control" maxlength="1" required>
                <input type="text" class="otp-input form-control" maxlength="1" required>
            </div>
            <input type="hidden" name="otp" id="otp_hidden">
            <button type="submit" class="btn btn-success w-100 mt-3">Verify</button>
        </form>

        <form action="generate_otp.php" method="POST" class="text-center mt-3">
            <button type="submit" class="btn btn-primary">Generate OTP</button>
        </form>
    </div>
    
    <script>
        document.querySelectorAll(".otp-input").forEach((input, index, inputs) => {
            input.addEventListener("input", (e) => {
                if (e.target.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
        });
    </script>
    <script>
        function combineOTP() {
            let otp = "";
            document.querySelectorAll(".otp-input").forEach(input => {
                otp += input.value;
            });
            document.getElementById("otp_hidden").value = otp;
        }

        // Auto-move to next input
        document.querySelectorAll(".otp-input").forEach((input, index, inputs) => {
            input.addEventListener("input", (e) => {
                if (e.target.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
        });
    </script>
    <script>
        document.querySelectorAll(".otp-input").forEach((input, index, inputs) => {
            // Memindahkan fokus ke input berikutnya saat mengetik
            input.addEventListener("input", (e) => {
                if (e.target.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            // Memindahkan fokus ke input sebelumnya saat menekan Backspace dalam keadaan kosong
            input.addEventListener("keydown", (e) => {
                if (e.key === "Backspace" && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
</body>
</html>
