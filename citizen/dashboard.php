<?php

session_start();

require_once __DIR__ . '/../config/db.php';


/* Check citizen login */

if (
    !isset($_SESSION["citizen_logged_in"]) ||
    $_SESSION["citizen_logged_in"] !== true
) {
    header("Location: login.php");
    exit;
}


/* Default values */

$total_complaints = 0;
$pending = 0;
$in_progress = 0;
$resolved = 0;
$rejected = 0;


try {

    /* Citizen ID */

    $citizen_id = $_SESSION["citizen_id"];


    /* Total complaints */

    $total_complaints =
        $database->complaints->countDocuments([
            "citizen_id" => $citizen_id
        ]);


    /* Pending */

    $pending =
        $database->complaints->countDocuments([
            "citizen_id" => $citizen_id,
            "status" => "Pending"
        ]);


    /* In Progress */

    $in_progress =
        $database->complaints->countDocuments([
            "citizen_id" => $citizen_id,
            "status" => "In Progress"
        ]);


    /* Resolved */

    $resolved =
        $database->complaints->countDocuments([
            "citizen_id" => $citizen_id,
            "status" => "Resolved"
        ]);


    /* Rejected */

    $rejected =
        $database->complaints->countDocuments([
            "citizen_id" => $citizen_id,
            "status" => "Rejected"
        ]);

} catch (Exception $e) {

    $total_complaints = 0;
    $pending = 0;
    $in_progress = 0;
    $resolved = 0;
    $rejected = 0;

}


/* Citizen email */

$citizen_email =
    $_SESSION["citizen_email"] ?? "Citizen";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Citizen Dashboard | Waste Monitor</title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }


        body {

            background: #f4f8f4;

            color: #222;

            min-height: 100vh;

        }


        /* =========================
           HEADER
        ========================= */

        header {

            background: #176b3a;

            color: white;

            height: 68px;

            padding: 0 7%;

            display: flex;

            align-items: center;

            justify-content: space-between;

            position: relative;

        }


        /* LEFT SIDE */

        .header-left {

            display: flex;

            align-items: center;

            gap: 15px;

        }


        /* HAMBURGER */

        .menu-btn {

            width: 40px;

            height: 40px;

            border: none;

            background: transparent;

            color: white;

            font-size: 28px;

            cursor: pointer;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 6px;

        }


        .menu-btn:hover {

            background: rgba(255,255,255,0.15);

        }


        /* LOGO */

        .logo {

            font-size: 21px;

            font-weight: bold;

        }


        /* RIGHT SIDE */

        .header-right {

            display: flex;

            align-items: center;

            gap: 12px;

        }


        /* ROUND PROFILE */

        .profile-icon {

            width: 38px;

            height: 38px;

            border-radius: 50%;

            background: white;

            color: #176b3a;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 21px;

            border: 2px solid rgba(255,255,255,0.8);

        }


        /* EMAIL */

        .email {

            font-size: 14px;

            color: white;

            max-width: 220px;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

        }


        /* LOGOUT */

        .logout {

            background: #c62828;

            color: white;

            text-decoration: none;

            padding: 9px 15px;

            border-radius: 6px;

            font-weight: bold;

            font-size: 14px;

        }


        .logout:hover {

            background: #a91f1f;

        }


        /* =========================
           SIDE MENU
        ========================= */

        .side-menu {

            position: fixed;

            top: 68px;

            left: -280px;

            width: 280px;

            height: calc(100vh - 68px);

            background: white;

            box-shadow:
                4px 0 15px rgba(0,0,0,0.12);

            z-index: 1000;

            transition: left 0.3s ease;

            padding: 25px 18px;

        }


        .side-menu.active {

            left: 0;

        }


        .side-menu h2 {

            color: #176b3a;

            font-size: 20px;

            margin-bottom: 20px;

            padding-left: 10px;

        }


        .menu-link {

            display: flex;

            align-items: center;

            gap: 12px;

            text-decoration: none;

            color: #333;

            padding: 14px 12px;

            border-radius: 8px;

            margin-bottom: 8px;

            font-weight: bold;

        }


        .menu-link:hover {

            background: #eaf5ed;

            color: #176b3a;

        }


        .menu-icon {

            font-size: 21px;

            width: 28px;

            text-align: center;

        }


        /* DARK OVERLAY */

        .menu-overlay {

            position: fixed;

            top: 68px;

            left: 0;

            width: 100%;

            height: calc(100vh - 68px);

            background: rgba(0,0,0,0.25);

            display: none;

            z-index: 900;

        }


        .menu-overlay.active {

            display: block;

        }


        /* =========================
           CONTAINER
        ========================= */

        .container {

            width: 90%;

            max-width: 1100px;

            margin: 35px auto;

            padding-bottom: 50px;

        }


        /* =========================
           WELCOME
        ========================= */

        .welcome {

            margin-bottom: 25px;

        }


        .welcome h1 {

            color: #176b3a;

            margin-bottom: 7px;

        }


        .welcome p {

            color: #666;

        }


        /* =========================
           STATISTICS
        ========================= */

        .stats {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 15px;

            margin-bottom: 30px;

        }


        .stat-card {

            background: white;

            border-radius: 12px;

            padding: 20px;

            text-align: center;

            box-shadow:
                0 3px 12px rgba(
                    0,
                    0,
                    0,
                    0.08
                );

            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease;

        }


        /* MOVE UP ON HOVER */

        .stat-card:hover {

            transform: translateY(-6px);

            box-shadow:
                0 8px 20px rgba(
                    0,
                    0,
                    0,
                    0.13
                );

        }


        .stat-title {

            color: #666;

            font-size: 14px;

            margin-bottom: 8px;

        }


        .stat-number {

            font-size: 28px;

            font-weight: bold;

            color: #176b3a;

        }


        .pending .stat-number {

            color: #b77900;

        }


        .progress .stat-number {

            color: #2563eb;

        }


        .resolved .stat-number {

            color: #15803d;

        }


        .rejected .stat-number {

            color: #dc2626;

        }


        /* =========================
           SERVICE CARDS
        ========================= */

        .services {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;

        }


        .service-card {

            background: white;

            border-radius: 12px;

            padding: 30px 22px;

            text-align: center;

            box-shadow:
                0 3px 12px rgba(
                    0,
                    0,
                    0,
                    0.08
                );

            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease;

        }


        /* MOVE UP WHEN POINTER IS ON BOX */

        .service-card:hover {

            transform: translateY(-10px);

            box-shadow:
                0 10px 25px rgba(
                    0,
                    0,
                    0,
                    0.15
                );

        }


        .service-icon {

            font-size: 42px;

            margin-bottom: 12px;

        }


        .service-card h2 {

            color: #176b3a;

            font-size: 20px;

            margin-bottom: 10px;

        }


        .service-card p {

            color: #666;

            line-height: 1.5;

            min-height: 48px;

            margin-bottom: 18px;

        }


        .service-btn {

            display: inline-block;

            background: #176b3a;

            color: white;

            text-decoration: none;

            padding: 10px 18px;

            border-radius: 7px;

            font-weight: bold;

        }


        .service-btn:hover {

            background: #0f4e2a;

        }


        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 900px) {

            .stats {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .services {

                grid-template-columns: 1fr;

            }

        }


        @media (max-width: 650px) {

            header {

                padding: 0 4%;

            }


            .logo {

                font-size: 17px;

            }


            .email {

                display: none;

            }


            .header-right {

                gap: 8px;

            }


            .profile-icon {

                width: 35px;

                height: 35px;

            }


            .logout {

                padding: 8px 11px;

            }


            .container {

                width: 94%;

            }


            .stats {

                grid-template-columns:
                    repeat(2, 1fr);

            }

        }


        @media (max-width: 400px) {

            .stats {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>


<body>


<!-- =========================
     HEADER
========================= -->

<header>


    <!-- LEFT SIDE -->

    <div class="header-left">


        <!-- THREE LINE MENU -->

        <button
            class="menu-btn"
            id="menuButton"
            onclick="toggleMenu()"
            aria-label="Open menu"
        >
            ☰
        </button>


        <div class="logo">

            🌱 Waste Monitor

        </div>


    </div>


    <!-- RIGHT SIDE -->

    <div class="header-right">


        <!-- ROUND PROFILE -->

        <div
            class="profile-icon"
            title="Citizen Profile"
        >
            👤
        </div>


        <!-- EMAIL -->

        <div class="email">

            <?php

            echo htmlspecialchars(
                $citizen_email
            );

            ?>

        </div>


        <!-- LOGOUT -->

        <a
            href="logout.php"
            class="logout"
        >
            Logout
        </a>


    </div>


</header>



<!-- =========================
     SIDE MENU
========================= -->

<div
    class="side-menu"
    id="sideMenu"
>


    <h2>
        Citizen Services
    </h2>


    <a
        href="report.php"
        class="menu-link"
    >

        <span class="menu-icon">
            📢
        </span>

        Report Waste

    </a>


    <a
        href="track.php"
        class="menu-link"
    >

        <span class="menu-icon">
            📍
        </span>

        Track Complaint

    </a>


    <a
        href="my_complaints.php"
        class="menu-link"
    >

        <span class="menu-icon">
            📋
        </span>

        My Complaints

    </a>


</div>


<!-- OVERLAY -->

<div
    class="menu-overlay"
    id="menuOverlay"
    onclick="toggleMenu()"
></div>



<!-- =========================
     MAIN
========================= -->

<div class="container">


    <!-- WELCOME -->

    <div class="welcome">

        <h1>
            Citizen Dashboard
        </h1>

        <p>
            Welcome! Use the services below to report
            and monitor waste collection complaints.
        </p>

    </div>



    <!-- =========================
         STATISTICS
    ========================= -->

    <div class="stats">


        <!-- MY COMPLAINTS -->

        <div class="stat-card">

            <div class="stat-title">
                My Complaints
            </div>

            <div class="stat-number">

                <?php

                echo $total_complaints;

                ?>

            </div>

        </div>


        <!-- PENDING -->

        <div class="stat-card pending">

            <div class="stat-title">
                Pending
            </div>

            <div class="stat-number">

                <?php

                echo $pending;

                ?>

            </div>

        </div>


        <!-- IN PROGRESS -->

        <div class="stat-card progress">

            <div class="stat-title">
                In Progress
            </div>

            <div class="stat-number">

                <?php

                echo $in_progress;

                ?>

            </div>

        </div>


        <!-- RESOLVED -->

        <div class="stat-card resolved">

            <div class="stat-title">
                Resolved
            </div>

            <div class="stat-number">

                <?php

                echo $resolved;

                ?>

            </div>

        </div>


        <!-- REJECTED -->

        <div class="stat-card rejected">

            <div class="stat-title">
                Rejected
            </div>

            <div class="stat-number">

                <?php

                echo $rejected;

                ?>

            </div>

        </div>


    </div>



    <!-- =========================
         SERVICES
    ========================= -->

    <div class="services">


        <!-- REPORT WASTE -->

        <div class="service-card">

            <div class="service-icon">
                📢
            </div>

            <h2>
                Report Waste
            </h2>

            <p>
                Report uncollected garbage in your locality
                and provide the required details.
            </p>

            <a
                href="report.php"
                class="service-btn"
            >
                Report Now
            </a>

        </div>



        <!-- TRACK COMPLAINT -->

        <div class="service-card">

            <div class="service-icon">
                📍
            </div>

            <h2>
                Track Complaint
            </h2>

            <p>
                Check the current status of your submitted
                waste collection complaint.
            </p>

            <a
                href="track.php"
                class="service-btn"
            >
                Track Complaint
            </a>

        </div>



        <!-- MY COMPLAINTS -->

        <div class="service-card">

            <div class="service-icon">
                📋
            </div>

            <h2>
                My Complaints
            </h2>

            <p>
                View the complaints you have submitted
                through the system.
            </p>

            <a
                href="my_complaints.php"
                class="service-btn"
            >
                View Complaints
            </a>

        </div>


    </div>


</div>



<!-- =========================
     JAVASCRIPT
========================= -->

<script>

function toggleMenu() {

    const menu =
        document.getElementById("sideMenu");

    const overlay =
        document.getElementById("menuOverlay");


    menu.classList.toggle("active");

    overlay.classList.toggle("active");

}


</script>


</body>

</html>