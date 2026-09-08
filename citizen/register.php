<?php

session_start();

require_once __DIR__ . '/../config/db.php';

$message = "";
$message_type = "";


/* =========================
   REGISTER CITIZEN
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    /* =========================
       NAME VALIDATION
    ========================= */

    if ($name === "") {

        $message = "Please enter your name.";
        $message_type = "error";

    }

    elseif (!preg_match("/^[A-Za-z ]+$/", $name)) {

        $message =
            "Name can contain only letters and spaces.";

        $message_type = "error";

    }


    /* =========================
       EMAIL VALIDATION
    ========================= */

    elseif (!preg_match(
        "/^[A-Za-z0-9._%+-]+@gmail\.com$/i",
        $email
    )) {

        $message =
            "Please enter a valid Gmail address ending with @gmail.com.";

        $message_type = "error";

    }


    /* =========================
       MOBILE VALIDATION
    ========================= */

    elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {

        $message =
            "Mobile number must contain exactly 10 digits.";

        $message_type = "error";

    }


    /* =========================
       PASSWORD VALIDATION
    ========================= */

    elseif (strlen($password) < 8) {

        $message =
            "Password must contain at least 8 characters.";

        $message_type = "error";

    }

    elseif (!preg_match("/[A-Z]/", $password)) {

        $message =
            "Password must contain at least one uppercase letter.";

        $message_type = "error";

    }

    elseif (!preg_match("/[a-z]/", $password)) {

        $message =
            "Password must contain at least one lowercase letter.";

        $message_type = "error";

    }

    elseif (!preg_match("/[0-9]/", $password)) {

        $message =
            "Password must contain at least one number.";

        $message_type = "error";

    }

    elseif (!preg_match("/[^A-Za-z0-9]/", $password)) {

        $message =
            "Password must contain at least one special character.";

        $message_type = "error";

    }


    /* =========================
       CONFIRM PASSWORD
    ========================= */

    elseif ($password !== $confirm_password) {

        $message =
            "Passwords do not match.";

        $message_type = "error";

    }


    /* =========================
       SAVE TO MONGODB
    ========================= */

    else {

        try {

            /* Check existing email */

            $existing_email =
                $database->citizens->findOne([
                    "email" => $email
                ]);


            if ($existing_email) {

                $message =
                    "An account with this email already exists.";

                $message_type = "error";

            }

            else {

                /* Check existing mobile */

                $existing_mobile =
                    $database->citizens->findOne([
                        "mobile" => $mobile
                    ]);


                if ($existing_mobile) {

                    $message =
                        "An account with this mobile number already exists.";

                    $message_type = "error";

                }

                else {

                    /* Hash password */

                    $hashed_password =
                        password_hash(
                            $password,
                            PASSWORD_DEFAULT
                        );


                    /* Create citizen */

                    $citizen = [

                        "name" =>
                            $name,

                        "email" =>
                            $email,

                        "mobile" =>
                            $mobile,

                        "password" =>
                            $hashed_password,

                        "created_at" =>
                            new MongoDB\BSON\UTCDateTime()

                    ];


                    $database
                        ->citizens
                        ->insertOne($citizen);


                    $message =
                        "Registration successful! You can now login.";

                    $message_type = "success";


                    /* Clear form */

                    $_POST = [];

                }

            }

        }

catch (\Throwable $e) {

    error_log(
        "REGISTRATION ERROR: " .
        $e->getMessage()
    );

    $message =
        "Registration error: " .
        $e->getMessage();

    $message_type = "error";
}

    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Citizen Registration | Waste Monitor</title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }


        body {

            background: #f4f8f4;

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 25px;

        }


        .register-box {

            width: 100%;

            max-width: 500px;

            background: white;

            padding: 35px;

            border-radius: 15px;

            box-shadow:
                0 4px 20px rgba(
                    0,
                    0,
                    0,
                    0.10
                );

        }


        .logo {

            text-align: center;

            color: #176b3a;

            font-size: 24px;

            font-weight: bold;

            margin-bottom: 8px;

        }


        h1 {

            text-align: center;

            color: #176b3a;

            margin-bottom: 8px;

        }


        .subtitle {

            text-align: center;

            color: #666;

            margin-bottom: 25px;

            line-height: 1.5;

        }


        .message {

            padding: 13px;

            border-radius: 7px;

            margin-bottom: 20px;

            text-align: center;

            font-size: 14px;

            line-height: 1.5;

        }


        .success {

            background: #e8f7ed;

            color: #176b3a;

            border: 1px solid #a9d8b8;

        }


        .error {

            background: #fdeaea;

            color: #b42318;

            border: 1px solid #f1b5b0;

        }


        .form-group {

            margin-bottom: 18px;

        }


        label {

            display: block;

            font-weight: bold;

            color: #333;

            margin-bottom: 8px;

        }


        .required {

            color: red;

        }


        input {

            width: 100%;

            padding: 13px;

            border: 1px solid #ccc;

            border-radius: 7px;

            font-size: 15px;

            outline: none;

        }


        input:focus {

            border-color: #176b3a;

        }


        input:invalid:not(:placeholder-shown) {

            border-color: #d9534f;

        }


        .password-rules {

            background: #f7faf7;

            border-left: 4px solid #176b3a;

            padding: 12px;

            margin-top: 8px;

            border-radius: 5px;

            font-size: 13px;

            color: #666;

            line-height: 1.7;

        }


        .password-rules strong {

            color: #176b3a;

        }


        .register-btn {

            width: 100%;

            padding: 14px;

            border: none;

            border-radius: 7px;

            background: #176b3a;

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            margin-top: 5px;

        }


        .register-btn:hover {

            background: #0f4e2a;

        }


        .login-link {

            text-align: center;

            margin-top: 20px;

            color: #666;

            font-size: 14px;

        }


        .login-link a {

            color: #176b3a;

            text-decoration: none;

            font-weight: bold;

        }


        .login-link a:hover {

            text-decoration: underline;

        }


        .home-link {

            text-align: center;

            margin-top: 12px;

        }


        .home-link a {

            color: #176b3a;

            text-decoration: none;

            font-size: 14px;

        }


        @media (max-width: 600px) {

            body {

                padding: 15px;

            }


            .register-box {

                padding: 25px 20px;

            }

        }

    </style>

</head>


<body>


<div class="register-box">


    <div class="logo">
        🌱 Waste Monitor
    </div>


    <h1>
        Citizen Registration
    </h1>


    <p class="subtitle">
        Create your account to report and track waste complaints.
    </p>


    <?php if ($message !== ""): ?>

        <div class="message <?php echo $message_type; ?>">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>


    <form
        method="POST"
        action=""
        id="registerForm"
    >


        <!-- NAME -->

        <div class="form-group">

            <label for="name">

                Full Name
                <span class="required">*</span>

            </label>


            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your full name"
                value="<?php
                    echo htmlspecialchars(
                        $_POST["name"] ?? ""
                    );
                ?>"
                pattern="[A-Za-z ]+"
                title="Name can contain only letters and spaces."
                required
            >

        </div>


        <!-- EMAIL -->

        <div class="form-group">

            <label for="email">

                Gmail Address
                <span class="required">*</span>

            </label>


            <input
                type="email"
                id="email"
                name="email"
                placeholder="example@gmail.com"
                value="<?php
                    echo htmlspecialchars(
                        $_POST["email"] ?? ""
                    );
                ?>"
                pattern="[A-Za-z0-9._%+-]+@gmail\.com"
                title="Please enter a valid Gmail address ending with @gmail.com."
                required
            >

        </div>


        <!-- MOBILE -->

        <div class="form-group">

            <label for="mobile">

                Mobile Number
                <span class="required">*</span>

            </label>


            <input
                type="tel"
                id="mobile"
                name="mobile"
                placeholder="Enter 10 digit mobile number"
                value="<?php
                    echo htmlspecialchars(
                        $_POST["mobile"] ?? ""
                    );
                ?>"
                pattern="[0-9]{10}"
                maxlength="10"
                inputmode="numeric"
                title="Mobile number must contain exactly 10 digits."
                required
            >

        </div>


        <!-- PASSWORD -->

        <div class="form-group">

            <label for="password">

                Password
                <span class="required">*</span>

            </label>


            <input
                type="password"
                id="password"
                name="password"
                placeholder="Create a strong password"
                minlength="8"
                required
            >


            <div class="password-rules">

                <strong>Password must contain:</strong>

                <br>

                ✓ At least 8 characters

                <br>

                ✓ At least one uppercase letter (A-Z)

                <br>

                ✓ At least one lowercase letter (a-z)

                <br>

                ✓ At least one number (0-9)

                <br>

                ✓ At least one special character (@, #, $, %, etc.)

            </div>

        </div>


        <!-- CONFIRM PASSWORD -->

        <div class="form-group">

            <label for="confirm_password">

                Confirm Password
                <span class="required">*</span>

            </label>


            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Re-enter your password"
                required
            >

        </div>


        <!-- SUBMIT -->

        <button
            type="submit"
            class="register-btn"
        >
            Create Account
        </button>


    </form>


    <div class="login-link">

        Already have an account?

        <a href="login.php">
            Login here
        </a>

    </div>


    <div class="home-link">

        <a href="../index.php">
            ← Back to Home
        </a>

    </div>


</div>



<script>

/* =========================
   NAME - LETTERS ONLY
========================= */

const nameInput =
    document.getElementById("name");

nameInput.addEventListener(
    "input",
    function () {

        this.value =
            this.value.replace(
                /[^A-Za-z ]/g,
                ""
            );

    }
);


/* =========================
   MOBILE - NUMBERS ONLY
========================= */

const mobileInput =
    document.getElementById("mobile");

mobileInput.addEventListener(
    "input",
    function () {

        this.value =
            this.value.replace(
                /[^0-9]/g,
                ""
            );

        this.value =
            this.value.substring(
                0,
                10
            );

    }
);


/* =========================
   PASSWORD CONFIRMATION
========================= */

const form =
    document.getElementById("registerForm");

const password =
    document.getElementById("password");

const confirmPassword =
    document.getElementById("confirm_password");


form.addEventListener(
    "submit",
    function (event) {

        const passwordValue =
            password.value;


        /* Password rules */

        const strongPassword =
            passwordValue.length >= 8 &&
            /[A-Z]/.test(passwordValue) &&
            /[a-z]/.test(passwordValue) &&
            /[0-9]/.test(passwordValue) &&
            /[^A-Za-z0-9]/.test(passwordValue);


        if (!strongPassword) {

            alert(
                "Password must contain at least 8 characters, one uppercase letter, one lowercase letter, one number and one special character."
            );

            event.preventDefault();

            return;

        }


        /* Confirm password */

        if (
            password.value !==
            confirmPassword.value
        ) {

            alert(
                "Passwords do not match."
            );

            event.preventDefault();

        }

    }
);

</script>


</body>

</html>