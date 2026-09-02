<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Public Waste Collection Monitoring System</title>

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

        /* =========================
           HEADER
        ========================= */

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

        nav {
            display: flex;
            gap: 25px;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-size: 15px;
            transition: 0.3s;
        }

        nav a:hover {
            opacity: 0.8;
        }


        /* =========================
           HERO SECTION
        ========================= */

        .hero {
            width: 90%;
            max-width: 1200px;

            margin: 70px auto;

            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 50px;
        }

        .hero-content {
            width: 55%;
        }

        .hero-content h1 {
            font-size: 42px;
            color: #222;

            line-height: 1.15;

            margin-bottom: 20px;
        }

        .hero-content h1 span {
            color: #176b3a;
        }

        .hero-content p {
            color: #666;

            font-size: 17px;

            line-height: 1.7;

            margin-bottom: 28px;

            max-width: 650px;
        }


        /* =========================
           BUTTONS
        ========================= */

        .buttons {
            display: flex;

            gap: 12px;

            flex-wrap: wrap;
        }

        .btn {
            padding: 13px 20px;

            border-radius: 7px;

            text-decoration: none;

            font-weight: bold;

            display: inline-block;

            transition:
                transform 0.3s ease,
                box-shadow 0.3s ease,
                opacity 0.3s ease;
        }

        .citizen-login {
            background: #176b3a;
            color: white;
        }

        .citizen-register {
            background: #e59b55;
            color: white;
        }

        .authority-login {
            border: 2px solid #176b3a;

            color: #176b3a;

            background: white;
        }

        .btn:hover {
            transform: translateY(-3px);

            box-shadow:
                0 5px 12px rgba(0, 0, 0, 0.15);
        }


        /* =========================
           WASTE MANAGEMENT IMAGE
        ========================= */

        .hero-image {
            width: 40%;

            display: flex;

            justify-content: center;

            align-items: center;
        }

        .waste-image {
            width: 360px;
            max-width: 100%;

            transition: transform 0.4s ease;
        }

        .waste-image:hover {
            transform: translateY(-8px) scale(1.03);
        }


        /* =========================
           SERVICES
        ========================= */

        .services {
            background: white;

            padding: 55px 7%;

            text-align: center;
        }

        .services h2 {
            color: #176b3a;

            font-size: 30px;

            margin-bottom: 10px;
        }

        .services-subtitle {
            color: #666;

            margin-bottom: 35px;
        }

        .service-container {
            max-width: 1100px;

            margin: auto;

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 25px;
        }


        /* =========================
           SERVICE CARDS
        ========================= */

        .service-card {
            background: #f4f8f4;

            padding: 30px 20px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(
                    0,
                    0,
                    0,
                    0.08
                );

            transition:
                transform 0.3s ease,
                box-shadow 0.3s ease;
        }

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
            font-size: 45px;

            margin-bottom: 15px;

            transition:
                transform 0.3s ease;
        }

        .service-card:hover .service-icon {
            transform: scale(1.1);
        }

        .service-card h3 {
            color: #176b3a;

            margin-bottom: 10px;
        }

        .service-card p {
            color: #666;

            line-height: 1.5;
        }


        /* =========================
           ABOUT
        ========================= */

        .about {
            padding: 55px 7%;

            text-align: center;
        }

        .about h2 {
            color: #176b3a;

            margin-bottom: 15px;
        }

        .about p {
            max-width: 850px;

            margin: auto;

            color: #666;

            line-height: 1.7;
        }


        /* =========================
           FOOTER
        ========================= */

        footer {
            background: #176b3a;

            color: white;

            text-align: center;

            padding: 18px;

            font-size: 14px;
        }


        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 800px) {

            .hero {
                flex-direction: column;

                text-align: center;

                margin-top: 45px;
            }

            .hero-content {
                width: 100%;
            }

            .hero-content h1 {
                font-size: 34px;
            }

            .hero-image {
                width: 100%;
            }

            .waste-image {
                width: 300px;
            }

            .buttons {
                justify-content: center;
            }

            .service-container {
                grid-template-columns: 1fr;
            }

            header {
                padding: 16px 5%;
            }

            nav {
                gap: 12px;
            }

        }

    </style>

</head>


<body>


<!-- =========================
     HEADER
========================= -->

<header>

    <div class="logo">
        🌱 Waste Monitor
    </div>

    <nav>

        <a href="#home">
            Home
        </a>

        <a href="#services">
            Services
        </a>

        <a href="#about">
            About
        </a>

    </nav>

</header>



<!-- =========================
     HERO
========================= -->

<section
    class="hero"
    id="home"
>

    <div class="hero-content">

        <h1>

            Cleaner Communities,

            <br>

            <span>
                Better Living
            </span>

        </h1>


        <p>

            A Public Waste Collection Monitoring System
            that allows citizens to report uncollected
            garbage and helps municipal authorities
            monitor waste collection and sanitation services.

        </p>


        <div class="buttons">


            <a
                href="citizen/login.php"
                class="btn citizen-login"
            >

                Citizen Login

            </a>


            <a
                href="citizen/register.php"
                class="btn citizen-register"
            >

                Citizen Registration

            </a>


            <a
                href="admin/login.php"
                class="btn authority-login"
            >

                Authority Login

            </a>


        </div>

    </div>


    <!-- =========================
         WASTE MANAGEMENT IMAGE
         ========================= -->

    <div class="hero-image">

        <svg
            class="waste-image"
            viewBox="0 0 500 400"
            xmlns="http://www.w3.org/2000/svg"
        >

            <!-- Background Circle -->

            <circle
                cx="250"
                cy="200"
                r="175"
                fill="#e8f5ec"
            />


            <!-- Ground -->

            <ellipse
                cx="250"
                cy="335"
                rx="150"
                ry="20"
                fill="#c9dfcf"
            />


            <!-- Waste Bin -->

            <rect
                x="115"
                y="155"
                width="105"
                height="150"
                rx="12"
                fill="#176b3a"
            />

            <rect
                x="105"
                y="140"
                width="125"
                height="25"
                rx="8"
                fill="#0f512b"
            />

            <rect
                x="145"
                y="115"
                width="45"
                height="30"
                rx="8"
                fill="#0f512b"
            />

            <rect
                x="135"
                y="190"
                width="65"
                height="8"
                rx="4"
                fill="#65b87f"
            />

            <rect
                x="135"
                y="215"
                width="65"
                height="8"
                rx="4"
                fill="#65b87f"
            />

            <rect
                x="135"
                y="240"
                width="65"
                height="8"
                rx="4"
                fill="#65b87f"
            />


            <!-- Small Recycle Symbol -->

            <text
                x="151"
                y="285"
                font-size="30"
                fill="white"
            >
                ♻
            </text>


            <!-- Garbage Bag -->

            <path
                d="M285 150
                   Q300 130 325 150
                   L365 275
                   Q370 295 350 305
                   L270 305
                   Q250 295 255 275
                   Z"
                fill="#555"
            />

            <path
                d="M285 150
                   Q325 170 365 150"
                fill="none"
                stroke="#333"
                stroke-width="8"
            />


            <!-- Bottle -->

            <rect
                x="300"
                y="190"
                width="30"
                height="65"
                rx="8"
                fill="#75b9d4"
            />

            <rect
                x="307"
                y="177"
                width="16"
                height="18"
                rx="4"
                fill="#4d92ad"
            />


            <!-- Paper -->

            <rect
                x="335"
                y="220"
                width="45"
                height="55"
                rx="4"
                fill="white"
                stroke="#ddd"
                stroke-width="3"
            />

            <line
                x1="345"
                y1="235"
                x2="370"
                y2="235"
                stroke="#aaa"
                stroke-width="4"
            />

            <line
                x1="345"
                y1="248"
                x2="370"
                y2="248"
                stroke="#aaa"
                stroke-width="4"
            />

            <line
                x1="345"
                y1="261"
                x2="365"
                y2="261"
                stroke="#aaa"
                stroke-width="4"
            />


            <!-- Leaf -->

            <path
                d="M375 125
                   C410 95 440 110 425 145
                   C410 175 380 160 375 125Z"
                fill="#43a95f"
            />

            <path
                d="M380 145
                   C395 135 408 125 420 115"
                fill="none"
                stroke="#176b3a"
                stroke-width="4"
            />


            <!-- Small Sparkles -->

            <circle
                cx="105"
                cy="120"
                r="7"
                fill="#e59b55"
            />

            <circle
                cx="405"
                cy="195"
                r="6"
                fill="#e59b55"
            />

            <circle
                cx="255"
                cy="110"
                r="6"
                fill="#43a95f"
            />

        </svg>

    </div>

</section>



<!-- =========================
     SERVICES
========================= -->

<section
    class="services"
    id="services"
>

    <h2>
        Our Services
    </h2>


    <p class="services-subtitle">

        Making waste management easier through technology.

    </p>


    <div class="service-container">


        <!-- REPORT WASTE -->

        <div class="service-card">

            <div class="service-icon">
                🗑️
            </div>


            <h3>
                Report Waste
            </h3>


            <p>

                Citizens can report uncollected waste
                with location, description and photograph.

            </p>

        </div>



        <!-- TRACK COMPLAINTS -->

        <div class="service-card">

            <div class="service-icon">
                📍
            </div>


            <h3>
                Track Complaints
            </h3>


            <p>

                Citizens can track the current status
                of their submitted waste complaints.

            </p>

        </div>



        <!-- AUTHORITY MONITORING -->

        <div class="service-card">

            <div class="service-icon">
                🏛️
            </div>


            <h3>
                Authority Monitoring
            </h3>


            <p>

                Municipal authorities can view complaints
                and update their status.

            </p>

        </div>


    </div>

</section>



<!-- =========================
     ABOUT
========================= -->

<section
    class="about"
    id="about"
>

    <h2>
        About Waste Monitor
    </h2>


    <p>

        Waste Monitor is a digital platform designed
        to improve public waste collection monitoring.
        It connects citizens with municipal authorities
        and helps create cleaner and healthier communities
        through technology.

    </p>

</section>



<!-- =========================
     FOOTER
========================= -->

<footer>

    © 2026 Waste Monitor |
    Public Waste Collection Monitoring System

</footer>


</body>

</html>