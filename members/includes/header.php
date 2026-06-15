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
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            color: var(--dark);
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
        header h1 { font-size: 2.8rem; font-weight: 700; margin-bottom: 8px; position: relative; z-index: 1; }
        header p { font-size: 1.2rem; opacity: 0.9; position: relative; z-index: 1; }
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
        .nav-tabs button.active, .nav-tabs button:hover {
            background: var(--primary);
            color: white;
            box-shadow: 0 5px 15px rgba(0,31,63,0.3);
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .section { display: none; animation: fadeIn 0.6s ease; }
        .section.active { display: block; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
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
        .card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,31,63,0.2); }
        .card i { font-size: 2.8rem; margin-bottom: 15px; opacity: 0.9; }
        .card.balance i { color: var(--accent); }
        .card.loans i { color: var(--success); }
        .card.pending i { color: var(--warning); }
        .card.approved i { color: var(--success); }
        .card h3 { font-size: 1.1rem; margin-bottom: 10px; color: #555; }
        .card p { font-size: 2.2rem; font-weight: 700; color: var(--dark); }
        .loan-form {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: var(--shadow);
            max-width: 700px;
            margin: 0 auto;
        }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 10px; font-weight: 600; color: var(--primary); }
        .form-group input, .form-group select {
            width: 100%;
            padding: 16px;
            border: 2px solid #ddd;
            border-radius: 12px;
            font-size: 1.1rem;
            transition: var(--transition);
        }
        .form-group input:focus, .form-group select:focus {
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
        .submit-btn:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(0,31,63,0.4); }
        .limits-table {
            width: 100%;
            margin: 30px 0;
            border-collapse: collapse;
            background: #f8fbff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .limits-table th, .limits-table td {
            padding: 20px;
            text-align: center;
        }
        .limits-table th {
            background: var(--primary);
            color: white;
            font-size: 1.1rem;
        }
        .limits-table td {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary);
        }
        .verify-card {
            background: #fff3cd;
            border-radius: 16px;
            padding: 40px;
            text-align: center;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }
        .verify-link-btn {
            display: inline-block;
            background: var(--warning);
            color: white;
            padding: 16px 40px;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: 600;
            text-decoration: none;
            margin-top: 20px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        .verify-link-btn:hover {
            background: #e67e22;
            transform: translateY(-3px);
        }
        .pending-card {
            background: #fffbe6;
            border-left: 5px solid var(--warning);
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }
        .table-container {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        table { width: 100%; border-collapse: collapse; }
        th { background: var(--primary); color: white; padding: 20px; text-align: left; font-weight: 600; }
        td { padding: 18px 20px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f8fbff; }
        .status { padding: 8px 16px; border-radius: 50px; font-size: 0.9rem; font-weight: 600; text-transform: capitalize; }
        .status.pending { background: #fff3cd; color: #856404; }
        .status.approved { background: #d4edda; color: #155724; }
        .status.rejected { background: #f8d7da; color: #721c24; }
        .status.paid { background: #d1ecf1; color: #0c5460; }
        .profile-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: var(--shadow);
            max-width: 700px;
            margin: 0 auto;
            text-align: center;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 20px;
            font-weight: bold;
        }
        .profile-info p {
            font-size: 1.2rem;
            margin: 15px 0;
            color: #555;
        }
        .profile-info strong { color: var(--primary); }
        .alert-success, .alert-error {
            padding: 15px;
            border-radius: 12px;
            margin: 20px 0;
            text-align: center;
            font-weight: 500;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .edit-form h3 {
            color: var(--primary);
            margin: 40px 0 20px;
            text-align: left;
        }
        @media (max-width: 768px) {
            header h1 { font-size: 2.2rem; }
            .nav-tabs button { padding: 12px 20px; font-size: 0.95rem; min-width: 120px; }
            .cards-grid { grid-template-columns: 1fr; }
            .loan-form, .profile-card { padding: 30px; }
            th, td { padding: 14px; font-size: 0.95rem; }
            .limits-table td { font-size: 1.3rem; }
        }






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
            width: auto;
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
        
        .dropbtn:hover {
            background: #0056b3;
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
        
        .dropdown-content a {
            display: block;
            padding: 12px 15px;
            color: #333;
            text-decoration: none;
        }
        
        .dropdown-content a:hover {
            background: #f5f5f5;
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
        }


        .footer {
            text-align: center;
            padding: 15px;
            margin-top: 30px;
            background: #fff;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        
    </style>
</head>
<body>
