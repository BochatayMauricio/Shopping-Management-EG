<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="2;url=public/Pages/Client Portal/clientPortal.php">
    <title>Shopping Rosario - Sistema de Promociones</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(45deg, #1a1a1a 0%, #4a4a4a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-container {
            text-align: center;
            color: white;
        }

        .loader {
            width: 200px;
            height: 6px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
            margin: 0 auto 20px;
            overflow: hidden;
            position: relative;
        }

        .loader::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, transparent, white, transparent);
            animation: loading 2s linear infinite;
        }

        @keyframes loading {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .loading-text {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .subtitle {
            margin-top: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="loader-container">
        <div class="loader"></div>
        <div class="loading-text">
            <i class="fas fa-store"></i> Shopping Rosario
        </div>
        <div class="subtitle">Cargando sistema de promociones...</div>
    </div>
</body>
</html>
