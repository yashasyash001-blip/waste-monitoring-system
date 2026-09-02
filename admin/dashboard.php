<?php

session_start();

require_once __DIR__ . '/../config/db.php';


/* Check authority login */

if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
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
$recent_complaints = [];


try {

    /* Total complaints */

    $total_complaints =
        $database->complaints->countDocuments();


    /* Pending */

    $pending =
        $database->complaints->countDocuments([
            "status" => "Pending"
        ]);


    /* In Progress */

    $in_progress =
        $database->complaints->countDocuments([
            "status" => "In Progress"
        ]);


    /* Resolved */

    $resolved =
        $database->complaints->countDocuments([
            "status" => "Resolved"
        ]);


    /* Rejected */

    $rejected =
        $database->complaints->countDocuments([
            "status" => "Rejected"
        ]);


    /* Recent complaints */

    $cursor =
        $database->complaints->find(
            [],
            [
                "sort" => [
                    "created_at" => -1
                ],
                "limit" => 5
            ]
        );


    foreach ($cursor as $complaint) {

        $recent_complaints[] = $complaint;

    }

} catch (Exception $e) {

    /*
     * Keep dashboard working even if
     * there is a temporary database error.
     */

    $total_complaints = 0;
    $pending = 0;
    $in_progress = 0;
    $resolved = 0;
    $rejected = 0;
    $recent_complaints = [];

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

    <title>Authority Dashboard | Waste Monitor</title>


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


        /* HEADER */

        header {
            background: #176b3a;
            color: white;

            padding: 18px 7%;

            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 20px;
        }


        .logo {
            font-size: 21px;
            font-weight: bold;
        }


        .header-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }


        .header-right a {
            color: white;
            text-decoration: none;

            padding: 9px 15px;

            border-radius: 6px;
        }


        .logout {
            background: #c62828;
        }


        /* CONTAINER */

        .container {
            width: 92%;
            max-width: 1200px;

            margin: 40px auto;

            padding-bottom: 50px;
        }


        /* WELCOME */

        .welcome {
            margin-bottom: 30px;
        }


        .welcome h1 {
            color: #176b3a;

            margin-bottom: 8px;
        }


        .welcome p {
            color: #666;
        }


        /* STAT CARDS */

        .stats {
            display: grid;

            grid-template-columns:
                repeat(5, 1fr);

            gap: 18px;

            margin-bottom: 35px;
        }


        .stat-card {
            background: white;

            border-radius: 12px;

            padding: 22px 18px;

            text-align: center;

            box-shadow:
                0 3px 12px rgba(
                    0,
                    0,
                    0,
                    0.08
                );

            border-top: 4px solid #176b3a;
        }


        .stat-title {
            color: #666;

            font-size: 14px;

            margin-bottom: 10px;
        }


        .stat-number {
            color: #176b3a;

            font-size: 32px;

            font-weight: bold;
        }


        /* DIFFERENT STATUS CARDS */

        .pending-card {
            border-top-color: #d69e00;
        }


        .pending-card .stat-number {
            color: #b77900;
        }


        .progress-card {
            border-top-color: #2563eb;
        }


        .progress-card .stat-number {
            color: #2563eb;
        }


        .resolved-card {
            border-top-color: #15803d;
        }


        .resolved-card .stat-number {
            color: #15803d;
        }


        .rejected-card {
            border-top-color: #dc2626;
        }


        .rejected-card .stat-number {
            color: #dc2626;
        }


        /* ACTIONS */

        .actions {
            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 20px;

            margin-bottom: 35px;
        }


        .action-card {
            background: white;

            border-radius: 12px;

            padding: 25px;

            box-shadow:
                0 3px 12px rgba(
                    0,
                    0,
                    0,
                    0.08
                );
        }


        .action-card h2 {
            color: #176b3a;

            font-size: 20px;

            margin-bottom: 8px;
        }


        .action-card p {
            color: #666;

            line-height: 1.5;

            margin-bottom: 18px;
        }


        .action-btn {
            display: inline-block;

            background: #176b3a;

            color: white;

            text-decoration: none;

            padding: 11px 18px;

            border-radius: 7px;

            font-weight: bold;
        }


        /* RECENT COMPLAINTS */

        .recent-box {
            background: white;

            border-radius: 12px;

            padding: 25px;

            box-shadow:
                0 3px 12px rgba(
                    0,
                    0,
                    0,
                    0.08
                );

            overflow-x: auto;
        }


        .recent-box h2 {
            color: #176b3a;

            margin-bottom: 20px;
        }


        table {
            width: 100%;

            border-collapse: collapse;

            min-width: 700px;
        }


        th {
            background: #176b3a;

            color: white;

            padding: 12px;

            text-align: left;

            font-size: 14px;
        }


        td {
            padding: 12px;

            border-bottom: 1px solid #ddd;

            font-size: 14px;
        }


        tr:hover {
            background: #f7faf7;
        }


        .complaint-id {
            color: #176b3a;

            font-weight: bold;
        }


        .status {
            display: inline-block;

            padding: 6px 10px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: bold;
        }


        .pending {
            background: #fff3cd;

            color: #856404;
        }


        .progress {
            background: #dbeafe;

            color: #1e40af;
        }


        .resolved {
            background: #d1fae5;

            color: #065f46;
        }


        .rejected {
            background: #fee2e2;

            color: #991b1b;
        }


        .no-data {
            text-align: center;

            padding: 30px;

            color: #777;
        }


        /* MOBILE */

        @media (max-width: 900px) {

            .stats {
                grid-template-columns:
                    repeat(3, 1fr);
            }

        }


        @media (max-width: 650px) {

            header {
                padding: 16px 5%;
            }


            .logo {
                font-size: 17px;
            }


            .stats {
                grid-template-columns:
                    repeat(2, 1fr);
            }


            .actions {
                grid-template-columns: 1fr;
            }


            .container {
                width: 94%;
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


<!-- HEADER -->

<header>


    <div class="logo">
        🏛️ Waste Monitor Authority
    </div>


    <div class="header-right">

        <a
            href="logout.php"
            class="logout"
        >
            Logout
        </a>

    </div>


</header>



<!-- MAIN -->

<div class="container">


    <!-- WELCOME -->

    <div class="welcome">

        <h1>
            Authority Dashboard
        </h1>

        <p>
            Monitor and manage citizen waste complaints.
        </p>

    </div>



    <!-- STATISTICS -->

    <div class="stats">


        <!-- TOTAL -->

        <div class="stat-card">

            <div class="stat-title">
                Total Complaints
            </div>

            <div class="stat-number">

                <?php
                echo $total_complaints;
                ?>

            </div>

        </div>


        <!-- PENDING -->

        <div class="stat-card pending-card">

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

        <div class="stat-card progress-card">

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

        <div class="stat-card resolved-card">

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

        <div class="stat-card rejected-card">

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



    <!-- ACTIONS -->

    <div class="actions">


        <div class="action-card">

            <h2>
                Manage Complaints
            </h2>

            <p>
                View citizen complaints, photographs,
                locations and update their status.
            </p>

            <a
                href="complaints.php"
                class="action-btn"
            >
                View Complaints
            </a>

        </div>


        <div class="action-card">

            <h2>
                Complaint Tracking
            </h2>

            <p>
                Monitor the progress of complaints
                from submission to resolution.
            </p>

            <a
                href="complaints.php"
                class="action-btn"
            >
                Track Complaints
            </a>

        </div>


    </div>



    <!-- RECENT COMPLAINTS -->

    <div class="recent-box">


        <h2>
            Recent Complaints
        </h2>


        <?php if (count($recent_complaints) > 0): ?>


            <table>


                <thead>

                    <tr>

                        <th>
                            Complaint ID
                        </th>

                        <th>
                            Reporter
                        </th>

                        <th>
                            Location
                        </th>

                        <th>
                            Waste Type
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php foreach (
                    $recent_complaints
                    as $complaint
                ): ?>


                    <?php

                    $status =
                        $complaint["status"]
                        ?? "Pending";


                    $status_class = "pending";


                    if (
                        $status === "In Progress"
                    ) {

                        $status_class =
                            "progress";

                    }

                    elseif (
                        $status === "Resolved"
                    ) {

                        $status_class =
                            "resolved";

                    }

                    elseif (
                        $status === "Rejected"
                    ) {

                        $status_class =
                            "rejected";

                    }

                    ?>


                    <tr>


                        <td>

                            <span
                                class="complaint-id"
                            >

                                <?php

                                echo htmlspecialchars(
                                    $complaint[
                                        "complaint_id"
                                    ] ?? "N/A"
                                );

                                ?>

                            </span>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $complaint[
                                    "reporter_name"
                                ] ?? "N/A"
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $complaint[
                                    "location"
                                ] ?? "N/A"
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $complaint[
                                    "waste_type"
                                ] ?? "N/A"
                            );

                            ?>

                        </td>


                        <td>

                            <span
                                class="status <?php
                                    echo $status_class;
                                ?>"
                            >

                                <?php

                                echo htmlspecialchars(
                                    $status
                                );

                                ?>

                            </span>

                        </td>


                    </tr>


                <?php endforeach; ?>


                </tbody>


            </table>


        <?php else: ?>


            <div class="no-data">

                No complaints have been submitted yet.

            </div>


        <?php endif; ?>


    </div>


</div>


</body>

</html>