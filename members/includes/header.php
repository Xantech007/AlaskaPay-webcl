<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlaskaCash • Member Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #001f3f;
            --primary-light: #003366;
            --accent: #00aaff;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --gray: #f8f9fa;
            --dark: #2c3e50;
            --light: #ecf0f1;
            --shadow: 0 10px 30px rgba(0,31,63,0.15);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            overflow-x: hidden; /* ✅ prevents horizontal scroll */
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            color: var(--dark);
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            padding: 30px 20px;
            text-align: center;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        header h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .nav-tabs {
            display: flex;
            justify-content: center;
            align-items: center;
            background: white;
            padding: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            flex-wrap: wrap;
            gap: 8px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-tabs button {
            padding: 14px 22px;
            background: transparent;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            cursor: pointer;
            border-radius: 50px;
            transition: var(--transition);
            min-width: 120px;
        }

        .nav-tabs button.active,
        .nav-tabs button:hover {
            background: var(--primary);
            color: white;
            box-shadow: 0 5px 15px rgba(0,31,63,0.3);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
        }

        .section {
            display: none;
            animation: fadeIn 0.6s ease;
        }

        .section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity:0; transform:translateY(20px); }
            to { opacity:1; transform:translateY(0); }
        }

        /* ================= CARDS GRID (IMPORTANT FIX) ================= */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-10px);
        }

        .card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .card.balance i { color: var(--accent); }
        .card.loans i { color: var(--success); }
        .card.pending i { color: var(--warning); }
        .card.approved i { color: var(--success); }

        .card h3 {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 2rem;
            font-weight: 700;
        }

        /* ================= FORMS ================= */
        .loan-form {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: var(--shadow);
            max-width: 700px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--primary);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 16px;
            border: 2px solid #ddd;
            border-radius: 12px;
            font-size: 1rem;
        }

        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
        }

        /* ================= ALERTS ================= */
        .alert-success,
        .alert-error {
            padding: 15px;
            border-radius: 12px;
            margin: 20px 0;
            text-align: center;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }

        /* ================= FOOTER ================= */
        .footer {
            text-align: center;
            padding: 15px;
            background: #fff;
            color: #666;
            font-size: 14px;
            margin-top: auto;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 1024px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            header h1 { font-size: 2rem; }
            header p { font-size: 1rem; }

            .nav-tabs {
                justify-content: flex-start;
                overflow-x: auto;
                flex-wrap: nowrap;
                padding: 10px;
            }

            .nav-tabs button {
                flex: 0 0 auto;
                min-width: 120px;
                padding: 12px 16px;
                font-size: 0.9rem;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .loan-form {
                padding: 25px;
            }
        }
    </style>
</head>

<body>
