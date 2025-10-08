<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> test تنزيل تطبيق المتجر</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to bottom, #87CEEB 0%, #fff3e6 50%);

            text-align: center;
            font-family: Arial, sans-serif;
            color: #333;
            overflow-x: hidden;
        }
        .download-container {
            padding: 30px;
            border-radius: 15px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            width: 90%;
            transition: transform 0.3s ease;
        }
        .download-container:hover {
            transform: translateY(-5px);
        }
        h1 {
            font-size: 2rem;
            color: #04319e;
            margin-bottom: 15px;
            font-weight: bold;
        }
        p {
            font-size: 1.1rem;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .store-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .store-button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #04319e;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .store-button:hover {
            background-color: #04319e;
            transform: scale(1.05);
        }
        .store-button i {
            font-size: 20px;
            vertical-align: middle;
        }
        .login-link {
            display: block;
            margin-top: 20px;
            color: #04319e;
            text-decoration: underline;
            transition: color 0.3s ease;
        }
        .login-link:hover {
            color: #04319e;
        }

        /* أنيميشن الموتور الواقعي */
        .road-container {
            margin-top: 30px;
            position: relative;
            width: 100%;
            height: 100px;
            perspective: 500px;
            overflow: hidden;
        }
        .road {
            width: 100%;
            height: 30px;
            background-color: #555;
            position: absolute;
            bottom: 0;
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.4);
            z-index: 1;
        }
        .motorcycle {
            position: absolute;
            bottom: 30px;
            left: -80px;
            animation: moveMotor 5s linear infinite;
            display: flex;
            align-items: center;
            z-index: 2;
            transform-origin: bottom center;
            animation: moveMotor 5s linear infinite, shakeMotor 0.2s ease-in-out infinite;
        }
        .motorcycle i {
            font-size: 50px;
            color: #04319e;
        }
        .rider {
            position: absolute;
            top: -20px;
            left: 10px;
            transform: rotate(-10deg); /* زاوية السائق ليبدو راكبًا */
        }
        .rider i {
            font-size: 30px;
            color: #333;
        }
        .smoke-container {
            position: absolute;
            bottom: 35px;
            left: 20px;
            z-index: 0;
        }
        .smoke {
            position: absolute;
            width: 15px;
            height: 15px;
            background: radial-gradient(circle, rgba(200, 200, 200, 0.8) 20%, transparent 70%);
            border-radius: 50%;
            animation: smokeAnimation 1.5s ease-out infinite;
            opacity: 0;
        }
        .smoke:nth-child(2) {
            animation-delay: 0.3s;
            left: 10px;
        }
        .smoke:nth-child(3) {
            animation-delay: 0.6s;
            left: 20px;
        }
        @keyframes moveMotor {
            0% {
                left: -80px;
                transform: rotateY(0deg);
            }
            50% {
                transform: rotateY(5deg);
            }
            100% {
                left: 100%;
                transform: rotateY(0deg);
            }
        }
        @keyframes shakeMotor {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-2px);
            }
        }
        @keyframes smokeAnimation {
            0% {
                transform: scale(0.5) translateY(0);
                opacity: 0.8;
            }
            50% {
                transform: scale(1.2) translateY(-10px);
                opacity: 0.5;
            }
            100% {
                transform: scale(1.8) translateY(-20px);
                opacity: 0;
            }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }
            p {
                font-size: 1rem;
            }
            .store-button {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            .store-button i {
                font-size: 18px;
            }
            .download-container {
                padding: 20px;
            }
            .motorcycle i {
                font-size: 40px;
            }
            .rider {
                top: -15px;
                left: 8px;
            }
            .rider i {
                font-size: 25px;
            }
            .smoke-container {
                bottom: 30px;
                left: 15px;
            }
            .smoke {
                width: 12px;
                height: 12px;
            }
            .road-container {
                height: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="download-container">
        <h1>تنزيل تطبيق المتجر الآن!</h1>
        <p>استمتع بأفضل تجربة تسوق مع تطبيقنا على أندرويد وiOS. قم بتنزيل التطبيق الآن!</p>
        <div class="store-buttons">
            <a href="#" class="store-button" id="google-play-link">
                <i class="fab fa-google-play"></i> تنزيل من Google Play
            </a>
            <a href="#" class="store-button" id="app-store-link">
                <i class="fab fa-apple"></i> تنزيل من App Store
            </a>
        </div>

        <div class="road-container">
            <div class="road"></div>
            <div class="motorcycle">
                <i class="fas fa-motorcycle"></i>
                <div class="rider"><i class="fas fa-user"></i></div>
                <div class="smoke-container">
                    <div class="smoke"></div>
                    <div class="smoke"></div>
                    <div class="smoke"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
