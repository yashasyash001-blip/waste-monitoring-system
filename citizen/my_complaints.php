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


$complaints = [];


/* Get logged-in citizen details */

$citizen_id = $_SESSION["citizen_id"] ?? "";
$citizen_email = $_SESSION["citizen_email"] ?? "";


try {

    /*
     * IMPORTANT:
     * Only show complaints belonging to
     * the currently logged-in citizen.
     *
     * We check both citizen_id and email
     * to handle existing complaints safely.
     */

    $conditions = [];


    if ($citizen_id !== "") {

        $conditions[] = [
            "citizen_id" => $citizen_id
        ];

        $conditions[] = [
            "citizen_id" => (string) $citizen_id
        ];

    }


    if ($citizen_email !== "") {

        $conditions[] = [
            "email" => $citizen_email
        ];

    }


    if (count($conditions) > 0) {

        $cursor = $database->complaints->find(
            [
                '$or' => $conditions
            ],
            [
                "sort" => [
                    "created_at" => -1
                ]
            ]
        );


        foreach ($cursor as $item) {

            $complaints[] = $item;

        }

    }

} catch (Exception $e) {

    $complaints = [];

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

    <title>My Complaints | Waste Monitor</title>


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
        }


        header {
            background: #176b3a;
            color: white;

            padding: 18px 7%;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .logo {
            font-size: 21px;
            font-weight: bold;
        }


        .back {
            color: white;
            text-decoration: none;
        }


        .container {
            width: 92%;
            max-width: 1100px;

            margin: 35px auto;

            padding-bottom: 40px;
        }


        .title {
            margin-bottom: 25px;
        }


        .title h1 {
            color: #176b3a;
            margin-bottom: 8px;
        }


        .title p {
            color: #666;
        }


        .complaint {
            background: white;

            border-radius: 12px;

            padding: 25px;

            margin-bottom: 20px;

            box-shadow:
                0 3px 12px rgba(0, 0, 0, 0.08);
        }


        .complaint-top {
            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;

            margin-bottom: 20px;

            flex-wrap: wrap;
        }


        .complaint-id {
            color: #176b3a;

            font-weight: bold;

            font-size: 17px;
        }


        .status {
            padding: 7px 13px;

            border-radius: 20px;

            font-size: 13px;

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


        .details {
            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 15px;
        }


        .detail {
            background: #f7faf7;

            padding: 14px;

            border-radius: 8px;
        }


        .full {
            grid-column: 1 / -1;
        }


        .label {
            color: #777;

            font-size: 13px;

            margin-bottom: 5px;
        }


        .value {
            font-weight: bold;

            line-height: 1.5;
        }


        .waste-image {
            width: 180px;
            height: 130px;

            object-fit: cover;

            border-radius: 8px;

            border: 1px solid #ddd;

            margin-top: 8px;
        }


        .buttons {
            margin-top: 20px;

            display: flex;

            gap: 10px;

            flex-wrap: wrap;
        }


        .button {
            display: inline-block;

            padding: 10px 17px;

            border-radius: 7px;

            text-decoration: none;

            font-weight: bold;

            font-size: 14px;
        }


        .track {
            background: #176b3a;

            color: white;
        }


        .report {
            background: #e8f1eb;

            color: #176b3a;
        }


        .empty {
            background: white;

            text-align: center;

            padding: 50px 20px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0, 0, 0, 0.08);
        }


        .empty-icon {
            font-size: 50px;

            margin-bottom: 15px;
        }


        .empty h2 {
            color: #555;

            margin-bottom: 10px;
        }


        .empty p {
            color: #777;

            margin-bottom: 20px;
        }


        .report-main {
            display: inline-block;

            background: #176b3a;

            color: white;

            text-decoration: none;

            padding: 12px 20px;

            border-radius: 7px;

            font-weight: bold;
        }


        @media (max-width: 600px) {

            header {
                padding: 16px 5%;
            }


            .container {
                width: 94%;
            }


            .details {
                grid-template-columns: 1fr;
            }


            .full {
                grid-column: auto;
            }


            .complaint {
                padding: 20px;
            }

        }

    </style>

</head>


<body>


<header>

    <div class="logo">
        🌱 Waste Monitor
    </div>


    <a
        href="dashboard.php"
        class="back"
    >
        ← Dashboard
    </a>

</header>


<div class="container">


    <div class="title">

        <h1>
            My Complaints
        </h1>

        <p>
            View all the waste complaints you have submitted.
        </p>

    </div>


    <?php if (count($complaints) > 0): ?>


        <?php foreach ($complaints as $complaint): ?>


            <?php

            $status =
                $complaint["status"] ?? "Pending";


            $status_class = "pending";


            if ($status === "In Progress") {

                $status_class = "progress";

            }

            elseif ($status === "Resolved") {

                $status_class = "resolved";

            }

            elseif ($status === "Rejected") {

                $status_class = "rejected";

            }


            $date = "";


            if (
                isset($complaint["created_at"])
            ) {

                try {

                    $date =
                        $complaint["created_at"]
                        ->toDateTime()
                        ->format(
                            "d-m-Y h:i A"
                        );

                } catch (Exception $e) {

                    $date = "";

                }

            }

            ?>


            <div class="complaint">


                <div class="complaint-top">


                    <div class="complaint-id">

                        Complaint ID:

                        <?php

                        echo htmlspecialchars(
                            $complaint["complaint_id"]
                            ?? "N/A"
                        );

                        ?>

                    </div>


                    <div
                        class="status <?php
                            echo $status_class;
                        ?>"
                    >

                        <?php

                        echo htmlspecialchars(
                            $status
                        );

                        ?>

                    </div>


                </div>


                <div class="details">


                    <!-- REPORTER -->

                    <div class="detail">

                        <div class="label">
                            Reporter Name
                        </div>

                        <div class="value">

                            <?php

                            echo htmlspecialchars(
                                $complaint["reporter_name"]
                                ?? "N/A"
                            );

                            ?>

                        </div>

                    </div>


                    <!-- DATE -->

                    <div class="detail">

                        <div class="label">
                            Submitted On
                        </div>

                        <div class="value">

                            <?php

                            echo htmlspecialchars(
                                $date
                            );

                            ?>

                        </div>

                    </div>


                    <!-- LOCATION -->

                    <div class="detail full">

                        <div class="label">
                            Location
                        </div>

                        <div class="value">

                            <?php

                            echo htmlspecialchars(
                                $complaint["location"]
                                ?? "N/A"
                            );

                            ?>

                        </div>

                    </div>


                    <!-- WASTE TYPE -->

                    <div class="detail">

                        <div class="label">
                            Waste Type
                        </div>

                        <div class="value">

                            <?php

                            echo htmlspecialchars(
                                $complaint["waste_type"]
                                ?? "N/A"
                            );

                            ?>

                        </div>

                    </div>


                    <!-- DESCRIPTION -->

                    <div class="detail">

                        <div class="label">
                            Description
                        </div>

                        <div class="value">

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $complaint["description"]
                                    ?? "N/A"
                                )
                            );

                            ?>

                        </div>

                    </div>


                    <!-- IMAGE -->

                    <div class="detail full">

                        <div class="label">
                            Waste Photograph
                        </div>


                        <?php

                        $image =
                            $complaint["image"] ?? "";


                        if ($image !== ""):

                        ?>

                            <a
                                href="../<?php
                                    echo htmlspecialchars($image);
                                ?>"
                                target="_blank"
                            >

                                <img
                                    src="../<?php
                                        echo htmlspecialchars($image);
                                    ?>"
                                    class="waste-image"
                                    alt="Waste Photograph"
                                >

                            </a>


                        <?php else: ?>


                            <div>
                                No photograph available.
                            </div>


                        <?php endif; ?>


                    </div>


                </div>


                <div class="buttons">


                    <a
                        href="track.php"
                        class="button track"
                    >
                        Track Complaint
                    </a>


                    <a
                        href="report.php"
                        class="button report"
                    >
                        Report Another Waste
                    </a>


                </div>


            </div>


        <?php endforeach; ?>


    <?php else: ?>


        <div class="empty">


            <div class="empty-icon">
                📋
            </div>


            <h2>
                No Complaints Yet
            </h2>


            <p>
                You have not submitted any waste complaints yet.
            </p>


            <a
                href="report.php"
                class="report-main"
            >
                Report Uncollected Waste
            </a>


        </div>


    <?php endif; ?>


</div>


</body>

</html>