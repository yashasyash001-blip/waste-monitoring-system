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


/* Get all complaints */

try {

    $complaints = $database->complaints->find(
        [],
        [
            "sort" => [
                "created_at" => -1
            ]
        ]
    );

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

    <title>Manage Complaints | Waste Monitor</title>


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

        .header-links {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .header-links a {
            color: white;
            text-decoration: none;
            padding: 9px 15px;
            border-radius: 6px;
        }

        .dashboard-link {
            background: #2d7d50;
        }

        .logout {
            background: #c62828;
        }

        .container {
            width: 94%;
            max-width: 1500px;
            margin: 35px auto;
        }

        .page-title {
            margin-bottom: 25px;
        }

        .page-title h1 {
            color: #145c34;
            margin-bottom: 7px;
        }

        .page-title p {
            color: #666;
        }

        .table-box {
            background: white;
            border-radius: 12px;
            padding: 20px;

            box-shadow:
                0 3px 12px rgba(0, 0, 0, 0.08);

            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1350px;
        }

        th {
            background: #176b3a;
            color: white;
            padding: 13px 10px;
            text-align: left;
            font-size: 14px;
        }

        td {
            padding: 13px 10px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
            font-size: 14px;
        }

        tr:hover {
            background: #f8fbf8;
        }

        .complaint-id {
            font-weight: bold;
            color: #176b3a;
        }

        .description {
            max-width: 230px;
            line-height: 1.5;
        }

        .waste-type {
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
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

        .complaint-image {
            width: 90px;
            height: 70px;
            object-fit: cover;
            border-radius: 7px;
            border: 1px solid #ddd;
            cursor: pointer;
        }

        .no-image {
            color: #888;
            font-size: 13px;
        }

        .status-form {
            min-width: 135px;
        }

        .status-select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background: white;
            margin-bottom: 7px;
        }

        .update-btn {
            width: 100%;
            padding: 8px;
            border: none;
            border-radius: 6px;
            background: #176b3a;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .update-btn:hover {
            background: #0f4e2a;
        }

        .updated-time {
            color: #666;
            font-size: 12px;
            line-height: 1.5;
        }

        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #777;
        }

        .empty-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .empty h2 {
            color: #555;
            margin-bottom: 8px;
        }

    </style>

</head>


<body>


<header>

    <div class="logo">
        🏛️ Waste Monitor Authority
    </div>


    <div class="header-links">

        <a
            href="dashboard.php"
            class="dashboard-link"
        >
            Dashboard
        </a>


        <a
            href="logout.php"
            class="logout"
        >
            Logout
        </a>

    </div>

</header>


<div class="container">


    <div class="page-title">

        <h1>
            Citizen Complaints
        </h1>

        <p>
            View and manage complaints submitted by citizens.
        </p>

    </div>


    <div class="table-box">


        <?php if ($database->complaints->countDocuments() > 0): ?>


            <table>

                <thead>

                    <tr>

                        <th>Complaint ID</th>

                        <th>Reporter</th>

                        <th>Email</th>

                        <th>Location</th>

                        <th>Waste Type</th>

                        <th>Description</th>

                        <th>Photo</th>

                        <th>Status</th>

                        <th>Update Status</th>

                        <th>Submitted</th>

                        <th>Last Updated</th>

                    </tr>

                </thead>


                <tbody>


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


                    /* Submitted date */

                    $date = "";


                    if (
                        isset($complaint["created_at"])
                    ) {

                        $date =
                            $complaint["created_at"]
                            ->toDateTime()
                            ->format(
                                "d-m-Y h:i A"
                            );

                    }


                    /* Last updated date */

                    $updated_date = "";


                    if (
                        isset($complaint["updated_at"])
                    ) {

                        $updated_date =
                            $complaint["updated_at"]
                            ->toDateTime()
                            ->format(
                                "d-m-Y h:i A"
                            );

                    }


                    ?>



                    <tr>


                        <!-- COMPLAINT ID -->

                        <td>

                            <span class="complaint-id">

                                <?php

                                echo htmlspecialchars(
                                    $complaint["complaint_id"]
                                    ?? "N/A"
                                );

                                ?>

                            </span>

                        </td>


                        <!-- REPORTER -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $complaint["reporter_name"]
                                ?? "N/A"
                            );

                            ?>

                        </td>


                        <!-- EMAIL -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $complaint["email"]
                                ?? "N/A"
                            );

                            ?>

                        </td>


                        <!-- LOCATION -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $complaint["location"]
                                ?? "N/A"
                            );

                            ?>

                        </td>


                        <!-- WASTE TYPE -->

                        <td>

                            <span class="waste-type">

                                <?php

                                echo htmlspecialchars(
                                    $complaint["waste_type"]
                                    ?? "N/A"
                                );

                                ?>

                            </span>

                        </td>


                        <!-- DESCRIPTION -->

                        <td>

                            <div class="description">

                                <?php

                                echo nl2br(
                                    htmlspecialchars(
                                        $complaint["description"]
                                        ?? "N/A"
                                    )
                                );

                                ?>

                            </div>

                        </td>


                        <!-- PHOTO -->

                        <td>

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
                                        class="complaint-image"
                                        alt="Waste Photo"
                                    >

                                </a>

                            <?php else: ?>

                                <span class="no-image">
                                    No image
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- CURRENT STATUS -->

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


                        <!-- UPDATE STATUS -->

                        <td>

                            <form
                                method="POST"
                                action="update_status.php"
                                class="status-form"
                            >


                                <input
                                    type="hidden"
                                    name="complaint_id"
                                    value="<?php

                                    echo htmlspecialchars(
                                        $complaint[
                                            "complaint_id"
                                        ] ?? ""
                                    );

                                    ?>"
                                >


                                <select
                                    name="status"
                                    class="status-select"
                                    required
                                >

                                    <option
                                        value="Pending"
                                        <?php

                                        echo $status === "Pending"
                                            ? "selected"
                                            : "";

                                        ?>
                                    >
                                        Pending
                                    </option>


                                    <option
                                        value="In Progress"
                                        <?php

                                        echo $status === "In Progress"
                                            ? "selected"
                                            : "";

                                        ?>
                                    >
                                        In Progress
                                    </option>


                                    <option
                                        value="Resolved"
                                        <?php

                                        echo $status === "Resolved"
                                            ? "selected"
                                            : "";

                                        ?>
                                    >
                                        Resolved
                                    </option>


                                    <option
                                        value="Rejected"
                                        <?php

                                        echo $status === "Rejected"
                                            ? "selected"
                                            : "";

                                        ?>
                                    >
                                        Rejected
                                    </option>

                                </select>


                                <button
                                    type="submit"
                                    class="update-btn"
                                >
                                    Update
                                </button>


                            </form>

                        </td>


                        <!-- SUBMITTED DATE -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $date
                            );

                            ?>

                        </td>


                        <!-- LAST UPDATED -->

                        <td>

                            <?php if ($updated_date !== ""): ?>

                                <div class="updated-time">

                                    <?php

                                    echo htmlspecialchars(
                                        $updated_date
                                    );

                                    ?>

                                </div>

                            <?php else: ?>

                                <div class="updated-time">
                                    Not updated yet
                                </div>

                            <?php endif; ?>

                        </td>


                    </tr>


                <?php endforeach; ?>


                </tbody>

            </table>


        <?php else: ?>


            <div class="empty">

                <div class="empty-icon">
                    📋
                </div>

                <h2>
                    No Complaints Found
                </h2>

                <p>
                    No citizen complaints have been submitted yet.
                </p>

            </div>


        <?php endif; ?>


    </div>


</div>


</body>

</html>