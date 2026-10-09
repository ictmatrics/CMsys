<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ htmlspecialchars($site_title, ENT_QUOTES, 'UTF-8') }} - Under Maintenance</title>
    <?php if (!empty($site_favicon)) { ?>
        <link rel="icon" type="image/x-icon" href="{{ pathto($site_favicon) }}">
    <?php } ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient-start: #0f172a;
            --bg-gradient-end: #020617;
            --card-bg: rgba(30, 41, 59, 0.4);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent-glow: rgba(99, 102, 241, 0.15);
            --accent-primary: #6366f1;
            --accent-secondary: #a855f7;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at 50% 50%, var(--bg-gradient-start), var(--bg-gradient-end));
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Abstract glowing background circles */
        .glow-orb {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--accent-glow) 0%, rgba(0,0,0,0) 70%);
            z-index: 1;
            filter: blur(40px);
            pointer-events: none;
        }
        
        .orb-1 {
            top: 10%;
            left: 15%;
            animation: float-slow 15s ease-in-out infinite alternate;
        }

        .orb-2 {
            bottom: 10%;
            right: 15%;
            animation: float-slow 20s ease-in-out infinite alternate-reverse;
        }

        @keyframes float-slow {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 30px) scale(1.1); }
        }

        .container {
            width: 100%;
            max-width: 580px;
            padding: 24px;
            z-index: 10;
            text-align: center;
        }

        .maintenance-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3),
                        inset 0 1px 0 rgba(255, 255, 255, 0.1);
            animation: fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
            overflow: hidden;
        }

        /* Glowing card accent line */
        .maintenance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-container {
            margin-bottom: 32px;
        }

        .logo-img {
            max-height: 70px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2));
        }

        .logo-text {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 30%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            color: var(--accent-primary);
            box-shadow: 0 0 25px rgba(99, 102, 241, 0.2);
            position: relative;
        }

        .icon-wrapper svg {
            width: 36px;
            height: 36px;
            animation: pulse-gear 4s linear infinite;
        }

        @keyframes pulse-gear {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff 40%, var(--text-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            color: var(--text-secondary);
            margin-bottom: 32px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(234, 179, 8, 0.1);
            border: 1px solid rgba(234, 179, 8, 0.2);
            color: #facc15;
            padding: 8px 16px;
            border-radius: 100px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #eab308;
            border-radius: 50%;
            animation: status-ping 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes status-ping {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        footer {
            margin-top: 40px;
            font-size: 13px;
            color: rgba(148, 163, 184, 0.5);
        }

        @media (max-width: 480px) {
            .maintenance-card {
                padding: 36px 24px;
            }
            h1 {
                font-size: 28px;
            }
            p {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="glow-orb orb-1"></div>
    <div class="glow-orb orb-2"></div>

    <div class="container">
        <div class="maintenance-card">
            <div class="logo-container">
                <?php if (!empty($site_logo)) { ?>
                    <img src="{{ pathto($site_logo) }}" alt="Logo" class="logo-img">
                <?php } else { ?>
                    <span class="logo-text">{{ htmlspecialchars($site_title, ENT_QUOTES, 'UTF-8') }}</span>
                <?php } ?>
            </div>

            <div class="icon-wrapper">
                <!-- Settings/Gear Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.43l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
            </div>

            <h1>Under Maintenance</h1>
            <p>We are currently updating our website to improve your experience. We will be back online shortly. Thank you for your patience!</p>

            <div class="status-badge">
                <span class="status-dot"></span>
                Scheduled Maintenance in Progress
            </div>
        </div>

        <footer>
            &copy; <?php echo date('Y'); ?> {{ htmlspecialchars($site_title, ENT_QUOTES, 'UTF-8') }}. All rights reserved.
        </footer>
    </div>
</body>
</html>
