<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Coming Soon</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
        }

        body {
            font-family: "Poppins", sans-serif;
            overflow: hidden;
        }

        /* =========================================
           MAIN COMING SOON AREA
        ========================================= */

        .coming-soon {
            position: relative;
            width: 100%;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            background-image: url("public/frontend/images/module-1.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            overflow: hidden;
        }

        /* White transparent layer like reference */
        .coming-soon::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.2);
            z-index: 1;
        }

        /* Subtle white gradient */
        .coming-soon::after {
            content: "";
            position: absolute;
            inset: 0;

            background:
                linear-gradient(
                    180deg,
                    rgba(255,255,255,0.98) 0%,
                    rgba(255,255,255,0.82) 40%,
                    rgba(255,255,255,0.60) 100%
                );

            z-index: 2;
            pointer-events: none;
        }

        .coming-content {
            position: relative;
            z-index: 5;

            width: 100%;
            max-width: 900px;

            padding: 40px 20px;

            text-align: center;
        }

        /* =========================================
           LOGO
        ========================================= */

        .coming-logo {
            margin-bottom: 70px;
        }

        .coming-logo img {
            max-width: 190px;
            width: auto;
            height: auto;

            display: inline-block;
        }

        /* =========================================
           TITLE
        ========================================= */

        .coming-title {
            margin: 0;

            font-size: clamp(40px, 5vw, 64px);
            line-height: 1.2;

            font-weight: 300;
            letter-spacing: -1.5px;

            color: #222;
        }

        .coming-title strong {
            font-weight: 700;
        }

        /* =========================================
           DESCRIPTION
        ========================================= */

        .coming-description {
            max-width: 650px;

            margin: 30px auto 0;

            font-size: 14px;
            line-height: 1.9;

            font-weight: 400;

            color: #555;
        }

        /* =========================================
           COUNTDOWN
        ========================================= */

        .countdown {
            margin-top: 65px;
        }

        .count-box {
            text-align: center;
            min-width: 110px;
        }

        .count-number {
            display: block;

            font-size: clamp(42px, 5vw, 58px);
            line-height: 1;

            font-weight: 300;

            letter-spacing: 1px;

            color: #171717;
        }

        .count-label {
            display: block;

            margin-top: 16px;

            font-size: 12px;
            line-height: 1;

            font-weight: 600;

            letter-spacing: 1px;

            text-transform: uppercase;

            color: #222;
        }

        /* =========================================
           SEPARATOR
        ========================================= */

        .count-separator {
            width: 1px;
            height: 55px;

            background: rgba(0, 0, 0, 0.15);

            margin: 0 15px;
        }

        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 767px) {

            .coming-content {
                padding: 30px 20px;
            }

            .coming-logo {
                margin-bottom: 45px;
            }

            .coming-logo img {
                max-width: 145px;
            }

            .coming-title {
                font-size: 40px;
            }

            .coming-description {
                margin-top: 20px;
                font-size: 13px;
                line-height: 1.8;
            }

            .countdown {
                margin-top: 45px;
            }

            .count-box {
                min-width: 65px;
            }

            .count-number {
                font-size: 32px;
            }

            .count-label {
                font-size: 9px;
                margin-top: 10px;
                letter-spacing: 0.5px;
            }

            .count-separator {
                display: none;
            }
        }

        @media (max-height: 700px) {

            .coming-logo {
                margin-bottom: 30px;
            }

            .coming-logo img {
                max-width: 130px;
            }

            .coming-description {
                margin-top: 15px;
            }

            .countdown {
                margin-top: 35px;
            }
        }

        .coming-logo-text{
            font-size: 3.5rem;
            font-weight: 600;
            color: #222;
        }
    </style>
</head>

<body>

<section class="coming-soon">

    <div class="container">

        <div class="coming-content mx-auto">

            <!-- LOGO -->
            <div class="coming-logo">

                <!-- Replace with your logo -->
                {{-- <img src="logo.png" alt="Company Logo"> --}}
                <h1 class="coming-logo-text">Pavan Auto Vehicles Services</h1>

            </div>


            <!-- TITLE -->
            <h1 class="coming-title">
                Coming back <strong>soon.</strong>
            </h1>


            <!-- DESCRIPTION -->
            <p class="coming-description">
                We're currently working on something amazing.
                Our new website is being prepared and will be
                available very soon. Stay tuned!
            </p>


            <!-- COUNTDOWN -->
            <div class="countdown">

                <div class="d-flex justify-content-center align-items-start">

                    <!-- DAYS -->
                    <div class="count-box">
                        <span id="days" class="count-number">00</span>
                        <span class="count-label">Days</span>
                    </div>


                    <div class="count-separator"></div>


                    <!-- HOURS -->
                    <div class="count-box">
                        <span id="hours" class="count-number">00</span>
                        <span class="count-label">Hours</span>
                    </div>


                    <div class="count-separator"></div>


                    <!-- MINUTES -->
                    <div class="count-box">
                        <span id="minutes" class="count-number">00</span>
                        <span class="count-label">Minutes</span>
                    </div>


                    <div class="count-separator"></div>


                    <!-- SECONDS -->
                    <div class="count-box">
                        <span id="seconds" class="count-number">00</span>
                        <span class="count-label">Seconds</span>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<script>
    /*
    |--------------------------------------------------------------------------
    | COUNTDOWN
    |--------------------------------------------------------------------------
    | Automatically counts down to 2 calendar months from today.
    |
    | Example:
    | 03 September 2026
    | +
    | 2 months
    | =
    | 03 November 2026
    |--------------------------------------------------------------------------
    */

    function getTwoMonthsFromNow() {

        const target = new Date();

        target.setMonth(target.getMonth() + 2);

        return target;
    }


    const targetDate = getTwoMonthsFromNow().getTime();


    function updateCountdown() {

        const now = new Date().getTime();

        const difference = targetDate - now;


        /* If countdown is finished */
        if (difference <= 0) {

            document.getElementById("days").textContent = "00";
            document.getElementById("hours").textContent = "00";
            document.getElementById("minutes").textContent = "00";
            document.getElementById("seconds").textContent = "00";

            return;
        }


        /* Calculate time */
        const days = Math.floor(
            difference / (1000 * 60 * 60 * 24)
        );

        const hours = Math.floor(
            (difference / (1000 * 60 * 60)) % 24
        );

        const minutes = Math.floor(
            (difference / (1000 * 60)) % 60
        );

        const seconds = Math.floor(
            (difference / 1000) % 60
        );


        /* Display */
        document.getElementById("days").textContent =
            String(days).padStart(2, "0");

        document.getElementById("hours").textContent =
            String(hours).padStart(2, "0");

        document.getElementById("minutes").textContent =
            String(minutes).padStart(2, "0");

        document.getElementById("seconds").textContent =
            String(seconds).padStart(2, "0");
    }


    /* Run immediately */
    updateCountdown();


    /* Update every second */
    setInterval(updateCountdown, 1000);
</script>

</body>
</html>