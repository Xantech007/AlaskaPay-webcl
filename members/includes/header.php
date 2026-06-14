<?php
if (!isset($_SESSION)) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Alaska Energy Network</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --primary:#001f3f;
    --primary-light:#003366;
    --accent:#00aaff;
    --success:#27ae60;
    --warning:#f39c12;
    --danger:#e74c3c;
    --shadow:0 10px 30px rgba(0,31,63,0.15);
}
body {
    margin:0;
    font-family:Inter, sans-serif;
    background:#f5f7fa;
}
.container {
    max-width:1200px;
    margin:40px auto;
    padding:0 20px;
}
</style>
</head>
<body>
