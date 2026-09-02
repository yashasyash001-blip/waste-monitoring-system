<?php

session_start();

require_once __DIR__ . '/../config/db.php';

$message = "";


/* If already logged in */

if (
    isset($_SESSION["citizen_logged_in"]) &&
    $_SESSION["citizen_logged_in"] === true
) {

    header("Location: dashboard.php");
    exit;

}


/* Login */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    if ($email === "" || $password === "") {

        $message = "Please enter email and password.";

    } else {

        try {

            /* Find citizen */

            $citizen = $database->citizens->findOne([
                "email" => $email
            ]);


            if ($citizen && password_verify(
                $password,
                $citizen["password"]
            )) {

                /* Create login session */

                $_SESSION["citizen_logged_in"] = true;

                $_SESSION["citizen_id"] =
                    (string)$citizen["_id"];

                $_SESSION["citizen_name"] =
                    $citizen["name"];

                $_SESSION["citizen_email"] =
                    $citizen["email"];


                /* Go to citizen dashboard */

                header("Location: dashboard.php");

                exit;

            } else {

                $message =
                    "Invalid email or password.";

            }

        } catch (Exception $e) {

            $message =
                "Unable to login. Please try again.";

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

    <title>Citizen Login | Waste Monitor</title>


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

            justify-content: center;

            align-items: center;

            padding: 30px 15px;

        }


        .login-box {

            width: 100%;

            max-width: 450px;

            background: white;

            padding: 40px 35px;

            border-radius: 15px;

            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.12);

        }


        .logo {

            text-align: center;

            font-size: 50px;

            margin-bottom: 10px;

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

            background: #fdeaea;

            color: #b42318;

            border: 1px solid #f1b5b0;

            padding: 12px;

            border-radius: 7px;

            margin-bottom: 20px;

            text-align: center;

            font-size: 14px;

        }


        .form-group {

            margin-bottom: 20px;

        }


        label {

            display: block;

            font-weight: bold;

            color: #333;

            margin-bottom: 8px;

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


        .login-btn {

            width: 100%;

            padding: 14px;

            border: none;

            border-radius: 7px;

            background: #176b3a;

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

        }


        .login-btn:hover {

            background: #0f4e2a;

        }


        .register-link {

            text-align: center;

            margin-top: 22px;

            color: #666;

            font-size: 14px;

        }


        .register-link a {

            color: #176b3a;

            text-decoration: none;

            font-weight: bold;

        }


        .register-link a:hover {

            text-decoration: underline;

        }


        .home-link {

            display: block;

            text-align: center;

            margin-top: 18px;

            color: #777;

            text-decoration: none;

            font-size: 14px;

        }


        .home-link:hover {

            color: #176b3a;

        }


    </style>

</head>


<body>


<div class="login-box">


    <div class="logo">
        🌱
    </div>


    <h1>
        Citizen Login
    </h1>


    <p class="subtitle">
        Login to report and track waste complaints.
    </p>


    <?php if ($message !== ""): ?>

        <div class="message">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>


    <form method="POST" action="">


        <!-- EMAIL -->

        <div class="form-group">

            <label for="email">
                Email Address
            </label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email address"
                required
            >

        </div>


        <!-- PASSWORD -->

        <div class="form-group">

            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
            >

        </div>


        <!-- LOGIN BUTTON -->

        <button
            type="submit"
            class="login-btn"
        >
            Login
        </button>


    </form>


    <!-- REGISTER -->

    <div class="register-link">

        Don't have an account?

        <a href="register.php">
            Register here
        </a>

    </div>


    <!-- HOME -->

    <a
        href="../index.php"
        class="home-link"
    >
        ← Back to Home
    </a>


</div>


</body>

</html>