<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

try {

    $id = (int) $_POST['id'];

    if ($id <= 0) {
        throw new Exception("Invalid region ID");
    }

    $country = trim($_POST['country']);
    $fee = (float) $_POST['fee'];

    $currency = trim($_POST['currency'] ?? '');
    $rate = $_POST['rate'] !== ''
        ? (float) $_POST['rate']
        : null;

    $convert_currency = $_POST['convert_currency'] ?? 'no';

    $method = trim($_POST['method'] ?? '');
    $method_name = trim($_POST['method_name'] ?? '');
    $method_id = trim($_POST['method_id'] ?? '');

    $method_value = trim($_POST['method_value'] ?? '');
    $method_name_value = trim($_POST['method_name_value'] ?? '');
    $method_id_value = trim($_POST['method_id_value'] ?? '');

    $ignore_location = $_POST['ignore_location'] ?? 'no';
    $alternate_country = trim($_POST['alternate_country'] ?? '');

    /* -----------------------------
       EXTERNAL PAYMENT SETTINGS
    ------------------------------*/
    $use_external = $_POST['use_external'] ?? 'no';
    $external_name = trim($_POST['external_name'] ?? '');
    $external_link = trim($_POST['external_link'] ?? '');

    /* -----------------------------
       JOB PAYMENT SETTINGS (NEW)
    ------------------------------*/
    $job_fee = $_POST['job_fee'] !== ''
        ? (float) $_POST['job_fee']
        : 0;

    $job_method = trim($_POST['job_method'] ?? '');
    $job_method_name = trim($_POST['job_method_name'] ?? '');
    $job_method_id = trim($_POST['job_method_id'] ?? '');

    $job_method_value = trim($_POST['job_method_value'] ?? '');
    $job_method_name_value = trim($_POST['job_method_name_value'] ?? '');
    $job_method_id_value = trim($_POST['job_method_id_value'] ?? '');

    $job_external_name = trim($_POST['job_external_name'] ?? '');
    $job_external_link = trim($_POST['job_external_link'] ?? '');

    $stmt = $pdo->prepare("
        UPDATE region_settings SET

            country = ?,
            fee = ?,
            currency = ?,
            rate = ?,
            convert_currency = ?,

            method = ?,
            method_name = ?,
            method_id = ?,

            method_value = ?,
            method_name_value = ?,
            method_id_value = ?,

            ignore_location = ?,
            alternate_country = ?,

            use_external = ?,
            external_name = ?,
            external_link = ?,

            job_fee = ?,
            job_method = ?,
            job_method_name = ?,
            job_method_id = ?,

            job_method_value = ?,
            job_method_name_value = ?,
            job_method_id_value = ?,

            job_external_name = ?,
            job_external_link = ?

        WHERE id = ?
    ");

    $stmt->execute([
        $country,
        $fee,
        $currency,
        $rate,
        $convert_currency,

        $method,
        $method_name,
        $method_id,

        $method_value,
        $method_name_value,
        $method_id_value,

        $ignore_location,
        $alternate_country,

        $use_external,
        $external_name,
        $external_link,

        $job_fee,
        $job_method,
        $job_method_name,
        $job_method_id,

        $job_method_value,
        $job_method_name_value,
        $job_method_id_value,

        $job_external_name,
        $job_external_link,

        $id
    ]);

    $_SESSION['success'] = "Region updated successfully";

} catch (Exception $e) {

    $_SESSION['error'] = $e->getMessage();
}

header("Location: region-settings");
exit();
