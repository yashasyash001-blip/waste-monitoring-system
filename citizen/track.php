<?php

session_start();

require_once __DIR__ . '/../config/db.php';


/* ==========================================
   CHECK CITIZEN LOGIN
========================================== */

if (
    !isset($_SESSION["citizen_logged_in"]) ||
    $_SESSION["citizen_logged_in"] !== true
) {
    header("Location: login.php");
    exit;
}


/* ==========================================
   VARIABLES
========================================== */

$complaint = null;
$message = "";

$email = trim($_SESSION["citizen_email"] ?? "");
$citizen_id = trim($_SESSION["citizen_id"] ?? "");


/* ==========================================
   SEARCH COMPLAINT
========================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $complaint_id = trim(
        $_POST["complaint_id"] ?? ""
    );


    if ($complaint_id === "") {

        $message = "Please enter your Complaint ID.";

    } else {

        try {

            /*
             * Build citizen ownership condition.
             *
             * The system supports both:
             * 1. citizen_id
             * 2. email
             */

            $ownerConditions = [];


            if ($citizen_id !== "") {

                $ownerConditions[] = [
                    "citizen_id" => $citizen_id
                ];

            }


            if ($email !== "") {

                $ownerConditions[] = [
                    "email" => $email
                ];

            }


            /*
             * Search complaint.
             */

            if (!empty($ownerConditions)) {

                $complaint =
                    $database->complaints->findOne(
                        [
                            "complaint_id" => $complaint_id,
                            '$or' => $ownerConditions
                        ]
                    );

            }


            /*
             * If complaint was not found using
             * citizen information, try email
             * case-insensitively.
             *
             * This helps if the email was saved
             * with different capital letters.
             */

            if (!$complaint && $email !== "") {

                $complaint =
                    $database->complaints->findOne(
                        [
                            "complaint_id" => $complaint_id,
                            "email" => [
                                '$regex' => "^" .
                                    preg_quote($email, "/") .
                                    "$",
                                '$options' => "i"
                            ]
                        ]
                    );

            }


            if (!$complaint) {

                $message =
                    "Complaint not found. Please check your Complaint ID.";

            }

        } catch (Throwable $e) {

            $message =
                "Unable to search complaint. Please try again.";

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

    <title>
        Track Complaint | Waste Monitor
    </title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }


        body {

            background:
                linear-gradient(
                    135deg,
                    #eef8f1,
                    #f8fbf8
                );

            color: #222;

            min-height: 100vh;
        }


        /* =================================
           HEADER
        ================================= */

        header {

            background: #176b3a;

            color: white;

            padding: 18px 7%;

            display: flex;

            justify-content: space-between;

            align-items: center;

            box-shadow:
                0 2px 10px rgba(
                    0,
                    0,
                    0,
                    0.15
                );
        }


        .logo {

            font-size: 21px;

            font-weight: bold;
        }


        .back {

            color: white;

            text-decoration: none;

            font-size: 15px;

            transition: 0.3s;
        }


        .back:hover {

            opacity: 0.8;

        }


        /* =================================
           MAIN CONTAINER
        ================================= */

        .container {

            width: 92%;

            max-width: 850px;

            margin: 45px auto;

            padding-bottom: 60px;
        }


        /*
        =================================
           SEARCH BOX
        ================================= */

        .search-box {

            background: white;

            padding: 32px;

            border-radius: 16px;

            box-shadow:
                0 6px 25px rgba(
                    0,
                    0,
                    0,
                    0.10
                );

            margin-bottom: 25px;
        }


        h1 {

            text-align: center;

            color: #176b3a;

            margin-bottom: 10px;

            font-size: 32px;
        }


        .subtitle {

            text-align: center;

            color: #666;

            margin-bottom: 28px;

            line-height: 1.5;
        }


        .form-group {

            margin-bottom: 17px;
        }


        label {

            display: block;

            font-weight: bold;

            margin-bottom: 8px;

            color: #333;
        }


        input {

            width: 100%;

            padding: 14px;

            border: 1px solid #ccc;

            border-radius: 8px;

            font-size: 15px;

            outline: none;

            transition: 0.3s;
        }


        input:focus {

            border-color: #176b3a;

            box-shadow:
                0 0 0 3px
                rgba(
                    23,
                    107,
                    58,
                    0.10
                );
        }


        .search-btn {

            width: 100%;

            padding: 14px;

            border: none;

            border-radius: 8px;

            background: #176b3a;

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.3s;
        }


        .search-btn:hover {

            background: #0f4e2a;

            transform: translateY(-1px);
        }


        /* =================================
           MESSAGE
        ================================= */

        .message {

            padding: 13px;

            border-radius: 8px;

            margin-bottom: 20px;

            text-align: center;

            background: #fdeaea;

            color: #b42318;

            border: 1px solid #f1b5b0;

            font-size: 14px;
        }


        /* =================================
           COMPLAINT BOX
        ================================= */

        .complaint-box {

            background: white;

            padding: 32px;

            border-radius: 16px;

            box-shadow:
                0 6px 25px rgba(
                    0,
                    0,
                    0,
                    0.10
                );
        }


        .complaint-heading {

            color: #176b3a;

            margin-bottom: 22px;

            font-size: 25px;
        }


        /* =================================
           STATUS
        ================================= */

        .status-box {

            text-align: center;

            padding: 20px;

            margin-bottom: 32px;

            border-radius: 10px;

            background: #fff3cd;

            color: #856404;

            border: 1px solid #f1df9c;
        }


        .status-box.progress {

            background: #dbeafe;

            color: #1e40af;

            border-color: #b9d7ff;
        }


        .status-box.resolved {

            background: #d1fae5;

            color: #065f46;

            border-color: #a7eacb;
        }


        .status-box.rejected {

            background: #fee2e2;

            color: #991b1b;

            border-color: #f6bcbc;
        }


        .status-title {

            font-size: 13px;

            margin-bottom: 6px;
        }


        .status-value {

            font-size: 23px;

            font-weight: bold;
        }


        /* =================================
           TIMELINE
        ================================= */

        .timeline-title {

            color: #176b3a;

            margin-bottom: 20px;
        }


        .timeline {

            position: relative;

            margin: 10px 0 35px;

            padding-left: 38px;
        }


        .timeline::before {

            content: "";

            position: absolute;

            left: 12px;

            top: 12px;

            bottom: 12px;

            width: 3px;

            background: #d8e3dc;
        }


        .step {

            position: relative;

            margin-bottom: 27px;

            min-height: 38px;
        }


        .step:last-child {

            margin-bottom: 0;
        }


        .circle {

            position: absolute;

            left: -38px;

            top: 0;

            width: 28px;

            height: 28px;

            border-radius: 50%;

            background: #d8e3dc;

            color: #777;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 13px;

            font-weight: bold;

            z-index: 2;
        }


        .step.completed .circle,
        .step.current .circle {

            background: #176b3a;

            color: white;
        }


        .step.current .circle {

            box-shadow:
                0 0 0 5px #d9ecdf;
        }


        .step-title {

            font-weight: bold;

            color: #777;

            margin-bottom: 5px;
        }


        .step.completed .step-title,
        .step.current .step-title {

            color: #176b3a;
        }


        .step-text {

            color: #888;

            font-size: 13px;

            line-height: 1.5;
        }


        /* =================================
           REJECTED
        ================================= */

        .step.rejected-step .circle {

            background: #b42318;

            color: white;
        }


        .step.rejected-step .step-title {

            color: #b42318;
        }


        /* =================================
           DETAILS
        ================================= */

        .details {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 15px;

            margin-top: 20px;
        }


        .detail {

            background: #f7faf7;

            padding: 15px;

            border-radius: 9px;

            border: 1px solid #edf2ed;
        }


        .detail.full {

            grid-column: 1 / -1;
        }


        .detail-label {

            color: #777;

            font-size: 13px;

            margin-bottom: 6px;
        }


        .detail-value {

            color: #222;

            font-weight: bold;

            line-height: 1.5;

            word-break: break-word;
        }


        /* =================================
           IMAGE
        ================================= */

        .waste-image {

            width: 100%;

            max-width: 450px;

            max-height: 320px;

            object-fit: cover;

            border-radius: 10px;

            display: block;

            margin: 12px auto 0;

            border: 1px solid #ddd;
        }


        .no-image {

            color: #777;

            font-weight: normal;
        }


        /* =================================
           LINKS
        ================================= */

        .links {

            text-align: center;

            margin-top: 28px;
        }


        .links a {

            display: inline-block;

            color: #176b3a;

            text-decoration: none;

            font-weight: bold;

            margin: 5px 10px;

            padding: 8px 14px;

            border-radius: 6px;

            transition: 0.3s;
        }


        .links a:hover {

            background: #e8f3eb;
        }


        /* =================================
           MOBILE
        ================================= */

        @media (max-width: 600px) {

            .details {

                grid-template-columns: 1fr;
            }


            .detail.full {

                grid-column: auto;
            }


            .search-box,
            .complaint-box {

                padding: 22px;
            }


            h1 {

                font-size: 27px;
            }


            header {

                padding: 16px 5%;
            }


            .logo {

                font-size: 18px;
            }


            .back {

                font-size: 13px;
            }

        }

    </style>

</head>


<body>


<!--
=================================
     HEADER
================================= -->

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


    <!-- =================================
         SEARCH BOX
    ================================= -->

    <div class="search-box">


        <h1>

            Track Your Complaint

        </h1>


        <p class="subtitle">

            Enter your Complaint ID to check the current status.

        </p>


        <?php if ($message !== ""): ?>

            <div class="message">

                <?php

                echo htmlspecialchars(
                    $message
                );

                ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action=""
        >


            <div class="form-group">


                <label for="complaint_id">

                    Complaint ID

                </label>


                <input
                    type="text"
                    id="complaint_id"
                    name="complaint_id"
                    placeholder="Enter your Complaint ID"
                    value="<?php

                        echo htmlspecialchars(
                            $_POST["complaint_id"] ?? ""
                        );

                    ?>"
                    required
                >


            </div>


            <button
                type="submit"
                class="search-btn"
            >

                Track Complaint

            </button>


        </form>


    </div>



    <?php if ($complaint): ?>


        <?php

        /* =================================
           STATUS
        ================================= */

        $status =
            $complaint["status"] ?? "Pending";


        $status_class = "";


        if ($status === "In Progress") {

            $status_class = "progress";

        }

        elseif ($status === "Resolved") {

            $status_class = "resolved";

        }

        elseif ($status === "Rejected") {

            $status_class = "rejected";

        }


        /* =================================
           DATE
        ================================= */

        $date = "";


        if (
            isset($complaint["created_at"]) &&
            $complaint["created_at"]
                instanceof MongoDB\BSON\UTCDateTime
        ) {

            $date =
                $complaint["created_at"]
                ->toDateTime()
                ->format("d-m-Y h:i A");

        }

        elseif (
            isset($complaint["created_at"]) &&
            is_string($complaint["created_at"])
        ) {

            $date =
                $complaint["created_at"];

        }


        /* =================================
           STATUS CHECKS
        ================================= */

        $is_pending =
            ($status === "Pending");

        $is_progress =
            ($status === "In Progress");

        $is_resolved =
            ($status === "Resolved");

        $is_rejected =
            ($status === "Rejected");

        ?>


        <!-- =================================
             RESULT
        ================================= -->

        <div class="complaint-box">


            <h2 class="complaint-heading">

                Complaint Details

            </h2>



            <!-- CURRENT STATUS -->

            <div
                class="status-box <?php

                    echo htmlspecialchars(
                        $status_class
                    );

                ?>"
            >


                <div class="status-title">

                    Current Status

                </div>


                <div class="status-value">

                    <?php

                    echo htmlspecialchars(
                        $status
                    );

                    ?>

                </div>


            </div>



            <!-- =================================
                 TIMELINE
            ================================= -->

            <h3 class="timeline-title">

                Complaint Progress

            </h3>


            <div class="timeline">


                <!-- SUBMITTED -->

                <div class="step completed">


                    <div class="circle">

                        ✓

                    </div>


                    <div class="step-title">

                        Complaint Submitted

                    </div>


                    <div class="step-text">

                        Your complaint has been successfully submitted.

                    </div>


                </div>



                <!-- PENDING -->

                <div class="step <?php

                    if ($is_pending) {

                        echo "current";

                    }

                    elseif (
                        $is_progress ||
                        $is_resolved
                    ) {

                        echo "completed";

                    }

                ?>">


                    <div class="circle">


                        <?php

                        if (
                            $is_progress ||
                            $is_resolved
                        ) {

                            echo "✓";

                        }

                        else {

                            echo "2";

                        }

                        ?>


                    </div>


                    <div class="step-title">

                        Pending

                    </div>


                    <div class="step-text">

                        Complaint is waiting for authority action.

                    </div>


                </div>



                <!-- IN PROGRESS -->

                <div class="step <?php

                    if ($is_progress) {

                        echo "current";

                    }

                    elseif ($is_resolved) {

                        echo "completed";

                    }

                ?>">


                    <div class="circle">


                        <?php

                        if ($is_resolved) {

                            echo "✓";

                        }

                        else {

                            echo "3";

                        }

                        ?>


                    </div>


                    <div class="step-title">

                        In Progress

                    </div>


                    <div class="step-text">

                        Municipality is working on your complaint.

                    </div>


                </div>



                <!-- RESOLVED -->

                <div class="step <?php

                    if ($is_resolved) {

                        echo "current";

                    }

                ?>">


                    <div class="circle">


                        <?php

                        if ($is_resolved) {

                            echo "✓";

                        }

                        else {

                            echo "4";

                        }

                        ?>


                    </div>


                    <div class="step-title">

                        Resolved

                    </div>


                    <div class="step-text">

                        The reported waste issue has been resolved.

                    </div>


                </div>



                <!-- REJECTED -->

                <?php if ($is_rejected): ?>


                    <div class="step rejected-step current">


                        <div class="circle">

                            ✕

                        </div>


                        <div class="step-title">

                            Rejected

                        </div>


                        <div class="step-text">

                            Your complaint has been rejected by the authority.

                        </div>


                    </div>


                <?php endif; ?>


            </div>



            <!-- =================================
                 DETAILS
            ================================= -->

            <div class="details">


                <!-- COMPLAINT ID -->

                <div class="detail">


                    <div class="detail-label">

                        Complaint ID

                    </div>


                    <div class="detail-value">

                        <?php

                        echo htmlspecialchars(
                            $complaint["complaint_id"]
                            ?? "N/A"
                        );

                        ?>

                    </div>


                </div>



                <!-- REPORTER -->

                <div class="detail">


                    <div class="detail-label">

                        Reporter Name

                    </div>


                    <div class="detail-value">

                        <?php

                        echo htmlspecialchars(
                            $complaint["reporter_name"]
                            ?? "N/A"
                        );

                        ?>

                    </div>


                </div>



                <!-- EMAIL -->

                <div class="detail">


                    <div class="detail-label">

                        Email

                    </div>


                    <div class="detail-value">

                        <?php

                        echo htmlspecialchars(
                            $complaint["email"]
                            ?? "N/A"
                        );

                        ?>

                    </div>


                </div>



                <!-- LOCATION -->

                <div class="detail full">


                    <div class="detail-label">

                        Location

                    </div>


                    <div class="detail-value">

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


                    <div class="detail-label">

                        Waste Type

                    </div>


                    <div class="detail-value">

                        <?php

                        echo htmlspecialchars(
                            $complaint["waste_type"]
                            ?? "N/A"
                        );

                        ?>

                    </div>


                </div>



                <!-- DATE -->

                <div class="detail">


                    <div class="detail-label">

                        Submitted On

                    </div>


                    <div class="detail-value">

                        <?php

                        echo htmlspecialchars(
                            $date !== ""
                                ? $date
                                : "N/A"
                        );

                        ?>

                    </div>


                </div>



                <!-- DESCRIPTION -->

                <div class="detail full">


                    <div class="detail-label">

                        Description

                    </div>


                    <div class="detail-value">

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



                <!-- PHOTO -->

                <div class="detail full">


                    <div class="detail-label">

                        Waste Photograph

                    </div>


                    <?php

                    $image =
                        $complaint["image"] ?? "";


                    if ($image !== ""):

                        /*
                         * Convert Windows backslashes
                         * to web slashes.
                         */

                        $image =
                            str_replace(
                                "\\",
                                "/",
                                $image
                            );


                        /*
                         * If image path is saved
                         * starting with uploads/,
                         * go one folder up because
                         * this page is inside citizen/.
                         */

                        if (
                            strpos(
                                $image,
                                "uploads/"
                            ) === 0
                        ) {

                            $image =
                                "../" . $image;

                        }

                    ?>

                        <img
                            src="<?php

                                echo htmlspecialchars(
                                    $image
                                );

                            ?>"
                            alt="Waste Photograph"
                            class="waste-image"
                            onerror="this.style.display='none';"
                        >


                    <?php else: ?>


                        <div class="no-image">

                            No photograph available.

                        </div>


                    <?php endif; ?>


                </div>


            </div>



            <!-- =================================
                 LINKS
            ================================= -->

            <div class="links">


                <a href="dashboard.php">

                    ← Back to Dashboard

                </a>


                <a href="report.php">

                    + Report Another Waste

                </a>


                <a href="my_complaints.php">

                    View My Complaints

                </a>


            </div>


        </div>


    <?php endif; ?>


</div>


</body>

</html>
