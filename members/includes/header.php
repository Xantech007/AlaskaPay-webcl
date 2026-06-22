<?php 

include __DIR__ . '/account_status_check.php';

$cs_number = $pdo->query("
    SELECT cs_number FROM admin LIMIT 1
")->fetchColumn();

$cs_number = preg_replace('/[^0-9]/', '', $cs_number);
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
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            color: var(--dark);

            /* ✅ FIX: footer layout */
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

        header::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,20 L100,100 L0,80 Z" fill="rgba(255,255,255,0.05)"/></svg>');
            background-size: cover;
        }

        header h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        header p {
            font-size: 1.2rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .nav-tabs {
            display: flex;
            justify-content: center;
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
            padding: 14px 28px;
            background: transparent;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            cursor: pointer;
            border-radius: 50px;
            transition: var(--transition);
            min-width: 140px;
        }

        .nav-tabs button.active,
        .nav-tabs button:hover {
            background: var(--primary);
            color: white;
            box-shadow: 0 5px 15px rgba(0,31,63,0.3);
        }

        .container {
            width: 80%;
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                margin: 20px auto;
                padding: 0 10px;
            }
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

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
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
            box-shadow: 0 20px 40px rgba(0,31,63,0.2);
        }

        .card i {
            font-size: 2.8rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .card.balance i { color: var(--accent); }
        .card.loans i { color: var(--success); }
        .card.pending i { color: var(--warning); }
        .card.approved i { color: var(--success); }

        .card h3 {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #555;
        }

        .card p {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark);
        }

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
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0,31,63,0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 8px 25px rgba(0,31,63,0.3);
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0,31,63,0.4);
        }

        .alert-success,
        .alert-error {
            padding: 15px;
            border-radius: 12px;
            margin: 20px 0;
            text-align: center;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* ================= FOOTER FIX ================= */
        .footer {
            text-align: center;
            padding: 15px;
            background: #fff;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;

            /* ✅ KEY FIX */
            margin-top: auto;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header h1 { font-size: 2.2rem; }
            .nav-tabs button { padding: 12px 20px; font-size: 0.95rem; min-width: 120px; }
            .cards-grid { grid-template-columns: 1fr; }
            .loan-form { padding: 30px; }
        }

        /* dropdown + top nav (unchanged) */
        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .logo img {
            height: 45px;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropbtn {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            min-width: 180px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 8px;
            overflow: hidden;
            z-index: 1000;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            transition: background 0.3s ease;
        }
        
        .dropdown-content a:hover {
            background: #f5f5f5;
        }
        
        .dropdown-content a i {
            width: 18px;
            text-align: center;
            color: #007bff;
        }


        /* ================= RESPONSIVE DASHBOARD GRID OVERRIDE ================= */
        
        /* Desktop (default already fine, but reinforced) */
        .cards-grid {
            grid-template-columns: repeat(4, 1fr);
        }
        
        /* Tablet */
        @media (max-width: 1024px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }
        
        /* Mobile */
        @media (max-width: 600px) {
            .cards-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        
            .card {
                padding: 22px;
            }
        
            .card p {
                font-size: 1.8rem;
            }
        
            .card i {
                font-size: 2.2rem;
            }
        }
        
        /* Prevent overflow issues on small screens */
        .card {
            width: 100%;
            box-sizing: border-box;
        }


     
        #withdraw-toast-container {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }
        
        .withdraw-toast {
            background: #e67e22;
            color: #fff;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 12px;
            width: 220px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            animation: slideIn 0.5s ease, fadeOut 0.5s ease 4.5s forwards;
        }
        
        @keyframes slideIn {
            from { transform: translateX(40px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes fadeOut {
            to { opacity: 0; transform: translateX(40px); }
        }


        #job-toast-container {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }
        
        .job-toast {
            background: #2ecc71;
            color: #fff;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 12px;
            width: 220px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            animation: slideIn 0.5s ease, fadeOut 0.5s ease 4.5s forwards;
        }
        
        @keyframes slideIn {
            from { transform: translateX(40px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes fadeOut {
            to { opacity: 0; transform: translateX(40px); }
        }


        .card.balance{
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #fff;
            border-radius: 18px;
            padding: 25px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow:
                0 10px 30px rgba(0,0,0,0.25),
                0 0 20px rgba(46,204,113,0.15);
            transition: all 0.3s ease;
        }
        
        .card.balance::before{
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 140px;
            height: 140px;
            background: rgba(46,204,113,0.15);
            border-radius: 50%;
            filter: blur(20px);
        }
        
        .card.balance:hover{
            transform: translateY(-5px);
            box-shadow:
                0 15px 40px rgba(0,0,0,0.35),
                0 0 30px rgba(46,204,113,0.25);
        }
        
        .card.balance i{
            font-size: 2rem;
            color: #2ecc71;
            margin-bottom: 12px;
        }
        
        .card.balance h3{
            color: rgba(255,255,255,0.8);
            font-size: 0.95rem;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .card.balance p{
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
        }

        .balance-wrapper{
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        
        .funding-note{
            font-size: 11px;
            color: rgba(255, 255, 255, 0.65);
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .whatsapp-float-wrapper{
            position: fixed;
            top: 65%;
            right: 20px;
        
            transform: translateY(-50%);
        
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 9999;
        }
        
        .whatsapp-float{
            width: 55px;
            height: 55px;
            background: #25D366;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            text-decoration: none;
            position: relative;
            
            animation: floatUpDown 2.5s ease-in-out infinite;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        @keyframes floatUpDown{
            0%   { transform: translateY(0); }
            50%  { transform: translateY(-6px); }
            100% { transform: translateY(0); }
        }
        
        .whatsapp-float::after{
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(37, 211, 102, 0.4);
            z-index: -1;
            animation: pulseGlow 1.8s infinite;
        }
        
        @keyframes pulseGlow{
            0%{
                transform: scale(1);
                opacity: 0.7;
            }
            100%{
                transform: scale(1.7);
                opacity: 0;
            }
        }
        
        .whatsapp-label{
            margin-top: 6px;
            font-size: 11px;
            font-weight: bold;
            color: #fff;
            background: rgba(0,0,0,0.35);
            padding: 3px 8px;
            border-radius: 8px;
            letter-spacing: 0.3px;
        
            animation: fadeLabel 2.5s ease-in-out infinite;
        }
        
        @keyframes fadeLabel{
            0%, 100% { opacity: 0.7; transform: translateY(0); }
            50% { opacity: 1; transform: translateY(-2px); }
        }
        
        .whatsapp-label{
            margin-top: 6px;
            font-size: 11px;
            font-weight: bold;
            color: #333;
            background: rgba(255,255,255,0.8);
            padding: 3px 8px;
            border-radius: 8px;
            letter-spacing: 0.3px;
        }
        
        
    </style>
</head>

<body>
