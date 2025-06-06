<?php
/*
*******************************************************************************************************************
* Warning!!! Tidak untuk diperjual belikan! Cukup pakai sendiri atau share kepada orang lain secara gratis
*******************************************************************************************************************
* Original Loginpage untuk Mikrotik dibuat oleh @Badaro
*
* Modifikasi Untuk coova-chilli oleh @ibnuhadjar https://t.me/ibnuhdjr
*******************************************************************************************************************
* © 2025 Mutiara-Net By @ibnuhadjar
*******************************************************************************************************************
*/
ob_start();

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.use_only_cookies', 1);

session_name('hotspot_session');
session_start();

if (headers_sent($file, $line)) {
    exit();
}

if (isset($_GET['mac'])) {
    $mac = $_GET['mac'];
}

if (!isset($_SESSION['username'])) {
    header("Location: ../profile/login.php?mac=$mac");
    exit();
}

function getUserInfo($username) {
    require '../config/mysqli_db.php';

    $sql = "SELECT id, whatsapp_number, balance FROM client WHERE username = ?";
    $userInfo = [];

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $userInfo = $row;
        }

        $stmt->close();
    }

    return $userInfo;
}

function money($number) {
    return "" . number_format($number, 0, ',', '.');
}

$userInfo = getUserInfo($_SESSION['username']);
$userId = htmlspecialchars($userInfo['id']);

require '../config/mysqli_db.php';
$stmt = $conn->prepare("SELECT COUNT(*) FROM topup WHERE user_id = ? AND status = 'Pending' AND date >= NOW() - INTERVAL 1 DAY");
$stmt->bind_param("s", $userId);
$stmt->execute();
$stmt->bind_result($pendingCount);
$stmt->fetch();
$stmt->close();

$harga = [10000, 15000, 20000, 25000, 30000, 35000, 40000, 45000, 50000, 60000, 70000, 80000, 90000, 100000];
$warna = ['success', 'danger', 'warning', 'primary', 'info', 'secondary', 'accent'];
$total_harga = count($harga);

ob_end_flush();
?>
<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title>TOPUP</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.svg" sizes="32x32">
    <style>
        /* Base Variables */
        :root {
            --primary-color: #0b0b1e;
            --secondary-color: #00f7ff;
            --accent-color: #ff00a0;
            --accent-color-2: #7700ff;
            --dark-color: #0a0a1a;
            --light-color: #e6f2ff;
            --danger-color: #ff003c;
            --warning-color: #ffbb00;
            --success-color: #00ff88;
            --info-color: #00a2ff;
            --gray-color: #6c757d;
            --gray-dark-color: #343a40;
            --gray-light-color: #f8f9fa;
            --white-color: #ffffff;
            --black-color: #000000;
            --border-color: #1a1a3a;
            --border-radius: 8px;
            --box-shadow: 0 4px 30px rgba(0, 247, 255, 0.1);
            --transition: all 0.3s ease;
            --font-family: 'Rajdhani', 'Orbitron', sans-serif;
            --font-family-monospace: 'Share Tech Mono', 'Courier New', monospace;
            --neon-glow: 0 0 5px rgba(0, 247, 255, 0.5), 0 0 10px rgba(0, 247, 255, 0.3), 0 0 15px rgba(0, 247, 255, 0.1);
            --neon-glow-pink: 0 0 5px rgba(255, 0, 160, 0.5), 0 0 10px rgba(255, 0, 160, 0.3), 0 0 15px rgba(255, 0, 160, 0.1);
            --neon-glow-purple: 0 0 5px rgba(119, 0, 255, 0.5), 0 0 10px rgba(119, 0, 255, 0.3), 0 0 15px rgba(119, 0, 255, 0.1);
            --neon-text-shadow: 0 0 5px rgba(0, 247, 255, 0.7), 0 0 10px rgba(0, 247, 255, 0.5), 0 0 15px rgba(0, 247, 255, 0.3);
        }

        /* Reset & Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Rajdhani:wght@300;400;500;600;700&family=Share+Tech+Mono&display=swap');

        html {
            font-size: 16px;
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-family);
            font-size: 1rem;
            line-height: 1.5;
            color: var(--light-color);
            background-color: var(--dark-color);
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(0, 247, 255, 0.05) 1%, transparent 10%),
                radial-gradient(circle at 75% 75%, rgba(255, 0, 160, 0.05) 1%, transparent 10%),
                linear-gradient(to bottom, rgba(10, 10, 26, 0.9), rgba(11, 11, 30, 0.9)),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%2300f7ff' fill-opacity='0.03'%3E%3Cpath opacity='.5' d='M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z'/%3E%3Cpath d='M6 5V0H5v5H0v1h5v94h1V6h94V5H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                repeating-linear-gradient(
                    to bottom,
                    transparent 0px,
                    transparent 1px,
                    rgba(0, 247, 255, 0.03) 1px,
                    rgba(0, 247, 255, 0.03) 2px
                );
            pointer-events: none;
            z-index: -1;
        }

        body::after {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255, 0, 160, 0.03), rgba(0, 247, 255, 0.03), rgba(119, 0, 255, 0.03));
            opacity: 0.2;
            pointer-events: none;
            z-index: -1;
            animation: colorShift 15s infinite alternate;
        }

        @keyframes colorShift {
            0% {
                opacity: 0.1;
                background-position: 0% 50%;
            }
            50% {
                opacity: 0.2;
                background-position: 100% 50%;
            }
            100% {
                opacity: 0.1;
                background-position: 0% 50%;
            }
        }

        a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
        }

        a:hover {
            color: var(--accent-color);
            text-shadow: var(--neon-glow-pink);
        }

        a::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 1px;
            bottom: -2px;
            left: 0;
            background-color: var(--accent-color);
            transform: scaleX(0);
            transform-origin: bottom right;
            transition: transform 0.3s ease-out;
        }

        a:hover::after {
            transform: scaleX(1);
            transform-origin: bottom left;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            margin-top: 0;
            margin-bottom: 0.5rem;
            font-weight: 700;
            line-height: 1.2;
            color: var(--white-color);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        h1 { font-size: 2.5rem; }
        h2 { font-size: 2rem; }
        h3 { font-size: 1.75rem; }
        h4 { font-size: 1.5rem; }
        h5 { font-size: 1.25rem; }
        h6 { font-size: 1rem; }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }

        .text-primary { color: var(--secondary-color) !important; }
        .text-secondary { color: var(--accent-color) !important; }
        .text-success { color: var(--success-color) !important; }
        .text-danger { color: var(--danger-color) !important; }
        .text-warning { color: var(--warning-color) !important; }
        .text-info { color: var(--info-color) !important; }
        .text-light { color: var(--light-color) !important; }
        .text-dark { color: var(--dark-color) !important; }
        .text-white { color: var(--white-color) !important; }

        .bg-primary { background-color: var(--primary-color) !important; }
        .bg-secondary { background-color: var(--secondary-color) !important; }
        .bg-success { background-color: var(--success-color) !important; }
        .bg-danger { background-color: var(--danger-color) !important; }
        .bg-warning { background-color: var(--warning-color) !important; }
        .bg-info { background-color: var(--info-color) !important; }
        .bg-light { background-color: var(--light-color) !important; }
        .bg-dark { background-color: var(--dark-color) !important; }
        .bg-white { background-color: var(--white-color) !important; }

        /* Layout */
        .mt-1 { margin-top: 0.25rem !important; }
        .mt-2 { margin-top: 0.5rem !important; }
        .mt-3 { margin-top: 1rem !important; }
        .mt-4 { margin-top: 1.5rem !important; }
        .mt-5 { margin-top: 3rem !important; }

        .mb-1 { margin-bottom: 0.25rem !important; }
        .mb-2 { margin-bottom: 0.5rem !important; }
        .mb-3 { margin-bottom: 1rem !important; }
        .mb-4 { margin-bottom: 1.5rem !important; }
        .mb-5 { margin-bottom: 3rem !important; }

        .p-1 { padding: 0.25rem !important; }
        .p-2 { padding: 0.5rem !important; }
        .p-3 { padding: 1rem !important; }
        .p-4 { padding: 1.5rem !important; }
        .p-5 { padding: 3rem !important; }

        .d-flex { display: flex !important; }
        .flex-grow-1 { flex-grow: 1 !important; }
        .ml-2 { margin-left: 0.5rem !important; }

        /* App Structure */
        #appCapsule {
            padding: 56px 0 80px 0;
            margin: 0;
            position: relative;
            min-height: 100vh;
            width: 100%;
            overflow: hidden;
        }

        .section {
            padding: 0 16px;
            margin-bottom: 20px;
            position: relative;
        }

        /* App Header */
        .appHeader {
            height: 56px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 999;
            background: var(--primary-color);
            color: var(--white-color);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid var(--secondary-color);
        }

        .appHeader::after {
            content: "";
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 1px;
            background: var(--secondary-color);
            box-shadow: var(--neon-glow);
        }

        .appHeader .left,
        .appHeader .right {
            height: 56px;
            display: flex;
            align-items: center;
            position: absolute;
        }

        .appHeader .left {
            left: 10px;
        }

        .appHeader .right {
            right: 10px;
        }

        .appHeader .pageTitle {
            font-size: 1.2rem;
            font-weight: 700;
            padding: 0 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            display: inline-block;
            color: var(--secondary-color);
            text-shadow: var(--neon-text-shadow);
        }

        .headerButton {
            min-width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            color: var(--white-color);
            border-radius: var(--border-radius);
            font-size: 26px;
            margin-left: 5px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 247, 255, 0.3);
            background: rgba(0, 247, 255, 0.05);
            transition: all 0.3s ease;
        }

        .headerButton:hover {
            box-shadow: var(--neon-glow);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .headerButton svg {
            filter: drop-shadow(var(--neon-glow));
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: var(--white-color);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 5px rgba(255, 0, 60, 0.7);
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1050;
            background: rgba(0, 0, 0, 0.7);
            overflow: auto;
            backdrop-filter: blur(5px);
        }

        .modal.show {
            display: block;
        }

        .modal-dialog {
            position: relative;
            width: auto;
            margin: 10px;
            pointer-events: none;
            max-width: 320px;
            margin: 1.75rem auto;
        }

        .dialogbox .modal-dialog {
            max-width: 320px;
            margin: 1.75rem auto;
        }

        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background: linear-gradient(135deg, rgba(11, 11, 30, 0.95), rgba(10, 10, 26, 0.95));
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            box-shadow: var(--neon-glow);
            outline: 0;
        }

        .modal-content::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(45deg, transparent, rgba(0, 247, 255, 0.05), transparent),
                repeating-linear-gradient(45deg, rgba(0, 247, 255, 0.05) 0px, rgba(0, 247, 255, 0.05) 1px, transparent 1px, transparent 10px);
            pointer-events: none;
            z-index: -1;
            border-radius: var(--border-radius);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            position: relative;
        }

        .modal-header::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background: var(--secondary-color);
            box-shadow: var(--neon-glow);
        }

        .modal-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--secondary-color);
            text-shadow: var(--neon-text-shadow);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 16px;
            text-align: center;
        }

        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 16px;
            border-top: 1px solid var(--border-color);
            position: relative;
        }

        .modal-footer::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background: var(--secondary-color);
            box-shadow: var(--neon-glow);
        }

        .btn-inline {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .btn-text-primary {
            color: var(--secondary-color);
            background: transparent;
            border: none;
            padding: 8px 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-text-primary:hover {
            text-shadow: 0 0 5px var(--secondary-color), 0 0 10px var(--secondary-color);
        }

        .btn-text-danger {
            color: var(--danger-color);
            background: transparent;
            border: none;
            padding: 8px 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-text-danger:hover {
            text-shadow: 0 0 5px var(--danger-color), 0 0 10px var(--danger-color);
        }

        .modal-icon {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .modal-icon svg {
            width: 48px;
            height: 48px;
            filter: drop-shadow(var(--neon-glow));
        }

        /* Topup Cards */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -8px;
            margin-left: -8px;
        }

        .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding-right: 8px;
            padding-left: 8px;
            position: relative;
        }

        .stat-box {
            background: linear-gradient(135deg, rgba(11, 11, 30, 0.9), rgba(10, 10, 26, 0.9));
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .stat-box::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(45deg, transparent, rgba(0, 247, 255, 0.05), transparent),
                repeating-linear-gradient(45deg, rgba(0, 247, 255, 0.05) 0px, rgba(0, 247, 255, 0.05) 1px, transparent 1px, transparent 10px);
            pointer-events: none;
            z-index: -1;
        }

        .stat-box:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .stat-box .value {
            font-size: 24px;
            font-weight: 700;
            font-family: var(--font-family-monospace);
            letter-spacing: 1px;
        }

        /* Color variants for topup cards */
        .stat-box.success {
            border-color: var(--success-color);
        }

        .stat-box.success::after {
            content: "";
            position: absolute;
            top: -1px;
            left: -1px;
            right: -1px;
            bottom: -1px;
            border: 1px solid var(--success-color);
            border-radius: var(--border-radius);
            box-shadow: 0 0 5px rgba(0, 255, 136, 0.5), 0 0 10px rgba(0, 255, 136, 0.3), 0 0 15px rgba(0, 255, 136, 0.1);
            z-index: -1;
            pointer-events: none;
        }

        .stat-box.success .value {
            color: var(--success-color);
            text-shadow: 0 0 5px rgba(0, 255, 136, 0.7), 0 0 10px rgba(0, 255, 136, 0.5), 0 0 15px rgba(0, 255, 136, 0.3);
        }

        .stat-box.danger {
            border-color: var(--danger-color);
        }

        .stat-box.danger::after {
            content: "";
            position: absolute;
            top: -1px;
            left: -1px;
            right: -1px;
            bottom: -1px;
            border: 1px solid var(--danger-color);
            border-radius: var(--border-radius);
            box-shadow: 0 0 5px rgba(255, 0, 60, 0.5), 0 0 10px rgba(255, 0, 60, 0.3), 0 0 15px rgba(255, 0, 60, 0.1);
            z-index: -1;
            pointer-events: none;
        }

        .stat-box.danger .value {
            color: var(--danger-color);
            text-shadow: 0 0 5px rgba(255, 0, 60, 0.7), 0 0 10px rgba(255, 0, 60, 0.5), 0 0 15px rgba(255, 0, 60, 0.3);
        }

        .stat-box.warning {
            border-color: var(--warning-color);
        }

        .stat-box.warning::after {
            content: "";
            position: absolute;
            top: -1px;
            left: -1px;
            right: -1px;
            bottom: -1px;
            border: 1px solid var(--warning-color);
            border-radius: var(--border-radius);
            box-shadow: 0 0 5px rgba(255, 187, 0, 0.5), 0 0 10px rgba(255, 187, 0, 0.3), 0 0 15px rgba(255, 187, 0, 0.1);
            z-index: -1;
            pointer-events: none;
        }

        .stat-box.warning .value {
            color: var(--warning-color);
            text-shadow: 0 0 5px rgba(255, 187, 0, 0.7), 0 0 10px rgba(255, 187, 0, 0.5), 0 0 15px rgba(255, 187, 0, 0.3);
        }

        .stat-box.primary {
            border-color: var(--secondary-color);
        }

        .stat-box.primary::after {
            content: "";
            position: absolute;
            top: -1px;
            left: -1px;
            right: -1px;
            bottom: -1px;
            border: 1px solid var(--secondary-color);
            border-radius: var(--border-radius);
            box-shadow: var(--neon-glow);
            z-index: -1;
            pointer-events: none;
        }

        .stat-box.primary .value {
            color: var(--secondary-color);
            text-shadow: var(--neon-text-shadow);
        }

        .stat-box.info {
            border-color: var(--info-color);
        }

        .stat-box.info::after {
            content: "";
            position: absolute;
            top: -1px;
            left: -1px;
            right: -1px;
            bottom: -1px;
            border: 1px solid var(--info-color);
            border-radius: var(--border-radius);
            box-shadow: 0 0 5px rgba(0, 162, 255, 0.5), 0 0 10px rgba(0, 162, 255, 0.3), 0 0 15px rgba(0, 162, 255, 0.1);
            z-index: -1;
            pointer-events: none;
        }

        .stat-box.info .value {
            color: var(--info-color);
            text-shadow: 0 0 5px rgba(0, 162, 255, 0.7), 0 0 10px rgba(0, 162, 255, 0.5), 0 0 15px rgba(0, 162, 255, 0.3);
        }

        .stat-box.secondary {
            border-color: var(--accent-color);
        }

        .stat-box.secondary::after {
            content: "";
            position: absolute;
            top: -1px;
            left: -1px;
            right: -1px;
            bottom: -1px;
            border: 1px solid var(--accent-color);
            border-radius: var(--border-radius);
            box-shadow: var(--neon-glow-pink);
            z-index: -1;
            pointer-events: none;
        }

        .stat-box.secondary .value {
            color: var(--accent-color);
            text-shadow: 0 0 5px rgba(255, 0, 160, 0.7), 0 0 10px rgba(255, 0, 160, 0.5), 0 0 15px rgba(255, 0, 160, 0.3);
        }

        .stat-box.accent {
            border-color: var(--accent-color-2);
        }

        .stat-box.accent::after {
            content: "";
            position: absolute;
            top: -1px;
            left: -1px;
            right: -1px;
            bottom: -1px;
            border: 1px solid var(--accent-color-2);
            border-radius: var(--border-radius);
            box-shadow: var(--neon-glow-purple);
            z-index: -1;
            pointer-events: none;
        }

        .stat-box.accent .value {
            color: var(--accent-color-2);
            text-shadow: 0 0 5px rgba(119, 0, 255, 0.7), 0 0 10px rgba(119, 0, 255, 0.5), 0 0 15px rgba(119, 0, 255, 0.3);
        }

        /* Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            pointer-events: none;
        }

        /* Data Stream */
        .data-stream {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            overflow: hidden;
        }

        .data-line {
            position: absolute;
            width: 1px;
            height: 50px;
            background-color: var(--secondary-color);
            box-shadow: var(--neon-glow);
            opacity: 0.5;
            animation: dataFall 8s linear infinite;
        }

        @keyframes dataFall {
            0% {
                transform: translateY(-100%);
            }
            100% {
                transform: translateY(100vh);
            }
        }

        /* Text Glitch Effect */
        .text-glitch {
            position: relative;
            animation: glitch 3s infinite;
        }

        @keyframes glitch {
            0% { text-shadow: var(--neon-text-shadow); }
            3% { text-shadow: 0 0 5px var(--accent-color), 0 0 10px var(--accent-color); }
            6% { text-shadow: var(--neon-text-shadow); }
            39% { text-shadow: var(--neon-text-shadow); }
            40% { text-shadow: 0 0 5px var(--accent-color-2), 0 0 10px var(--accent-color-2); opacity: 1; }
            41% { text-shadow: var(--neon-text-shadow); opacity: 1; }
            45% { text-shadow: var(--neon-text-shadow); opacity: 1; }
            46% { text-shadow: var(--neon-text-shadow); opacity: 0.8; }
            47% { text-shadow: var(--neon-text-shadow); opacity: 1; }
            48% { text-shadow: var(--neon-text-shadow); opacity: 0.8; }
            49% { text-shadow: var(--neon-text-shadow); opacity: 1; }
            50% { text-shadow: var(--neon-text-shadow); opacity: 0.8; }
            51% { text-shadow: var(--neon-text-shadow); opacity: 1; }
            92% { text-shadow: var(--neon-text-shadow); }
            93% { text-shadow: 0 0 5px var(--accent-color), 0 0 10px var(--accent-color); }
            94% { text-shadow: var(--neon-text-shadow); }
            100% { text-shadow: var(--neon-text-shadow); }
        }
    </style>
</head>

<body>
    <!-- Particles -->
    <div class="particles" id="particles-js"></div>

    <!-- Data Stream -->
    <div class="data-stream" id="data-stream"></div>

    <!-- App Header -->
    <div class="appHeader">
        <div class="left">
            <a href="../index.php?mac=<?php echo "$mac" ?>" class="headerButton goBack">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z" />
                </svg>
            </a>
        </div>
        <div class="pageTitle text-glitch">
            Topup
        </div>
        <div class="right">
            <a href="#" class="headerButton" data-bs-toggle="modal" data-bs-target="#DialogIconedInfo">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 21H14C14 22.1 13.1 23 12 23S10 22.1 10 21M21 19V20H3V19L5 17V11C5 7.9 7 5.2 10 4.3V4C10 2.9 10.9 2 12 2S14 2.9 14 4V4.3C17 5.2 19 7.9 19 11V17L21 19M17 11C17 8.2 14.8 6 12 6S7 8.2 7 11V18H17V11Z" />
                </svg>
            </a>
        </div>
    </div>
    <!-- * App Header -->

    <!-- DialogIconedInfo -->
    <div class="modal fade dialogbox" id="DialogIconedInfo" data-bs-backdrop="static" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--secondary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 12C9.97 12 8.1 12.67 6.6 13.8L4.8 11.4C6.81 9.89 9.3 9 12 9S17.19 9.89 19.2 11.4L17.92 13.1C17.55 13.17 17.18 13.27 16.84 13.41C15.44 12.5 13.78 12 12 12M21 9L22.8 6.6C19.79 4.34 16.05 3 12 3S4.21 4.34 1.2 6.6L3 9C5.5 7.12 8.62 6 12 6S18.5 7.12 21 9M12 15C10.65 15 9.4 15.45 8.4 16.2L12 21L13.04 19.61C13 19.41 13 19.21 13 19C13 17.66 13.44 16.43 14.19 15.43C13.5 15.16 12.77 15 12 15M17.75 19.43L16.16 17.84L15 19L17.75 22L22.5 17.25L21.34 15.84L17.75 19.43Z" />
                    </svg>
                </div>
                <div class="modal-header">
                    <h5 class="modal-title"><script src="../assets/config/namawifi.js"></script></h5>
                </div>
                <div class="modal-body">
                    <?php echo "<p>Hi " . htmlspecialchars($_SESSION['username']) . ", <br> Sisa saldo Anda adalah : Rp " . money($userInfo['balance']) . "</p>"; ?>
                </div>
                <div class="modal-footer">
                    <div class="btn-inline">
                        <a href="#" class="btn btn-text-primary" data-bs-dismiss="modal">OKE</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- * DialogIconedInfo -->

    <!-- App Capsule -->
    <div id="appCapsule">
        <div class="section mt-2">
            <!-- Paket Unggulan -->
            <div class="section">
                <?php for ($i = 0; $i < $total_harga; $i += 2): ?>
                    <div class="row mt-2">
                        <div class="col-6">
                            <div class="stat-box <?php echo $warna[$i % count($warna)]; ?>" onclick="showConfirmationModal(<?php echo $harga[$i]; ?>, <?php echo $userId; ?>)">
                                <div class="value">
                                    <center>Rp <?php echo money($harga[$i]); ?></center>
                                </div>
                            </div>
                        </div>
                        <?php if ($i + 1 < $total_harga): ?>
                            <div class="col-6">
                                <div class="stat-box <?php echo $warna[($i + 1) % count($warna)]; ?>" onclick="showConfirmationModal(<?php echo $harga[$i + 1]; ?>, <?php echo $userId; ?>)">
                                    <div class="value">
                                        <center>Rp <?php echo money($harga[$i + 1]); ?></center>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
            <!-- * Paket Unggulan -->

            <!-- Modal Konfirmasi Pembelian -->
            <div class="modal fade dialogbox" id="DialogPurchaseConfirmation" data-bs-backdrop="static" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Topup</h5>
                        </div>
                        <div class="modal-body">
                            <p>Apakah Anda yakin ingin topup dengan jumlah <span id="selectedAmount" class="text-primary"></span>?</p>
                        </div>
                        <div class="modal-footer">
                            <div class="btn-inline">
                                <button id="confirmPurchase" class="btn btn-text-primary" onclick="redirectToPurchase()">Ya</button>
                                <button type="button" class="btn btn-text-danger" data-bs-dismiss="modal">Tidak</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popup untuk permintaan topup yang belum dikonfirmasi -->
            <div class="modal fade dialogbox" id="DialogPendingTopup" data-bs-backdrop="static" tabindex="-1" aria-labelledby="DialogPendingTopupLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger">Warning!!</h5>
                        </div>
                        <div class="modal-body">
                            <p>Anda masih memiliki permintaan topup yang belum dikonfirmasi.</p>
                        </div>
                        <div class="modal-footer">
                            <div class="btn-inline">
                                <button class="btn btn-text-primary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- * Popup untuk permintaan topup yang belum dikonfirmasi -->
        </div>
    </div>
    <!-- * App Capsule -->

    <script>
        // Create particle effect
        document.addEventListener('DOMContentLoaded', function() {
            // Create canvas element
            const canvas = document.createElement('canvas');
            canvas.id = 'particles-canvas';
            canvas.style.position = 'fixed';
            canvas.style.top = '0';
            canvas.style.left = '0';
            canvas.style.width = '100%';
            canvas.style.height = '100%';
            canvas.style.zIndex = '-2';
            canvas.style.pointerEvents = 'none';
            document.getElementById('particles-js').appendChild(canvas);
            
            // Particle animation
            const ctx = canvas.getContext('2d');
            let particles = [];
            const colors = ['#00f7ff', '#ff00a0', '#7700ff', '#00ff88'];
            
            function resizeCanvas() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            }
            
            function createParticles() {
                particles = [];
                const particleCount = Math.floor(window.innerWidth / 20);
                
                for (let i = 0; i < particleCount; i++) {
                    particles.push({
                        x: Math.random() * canvas.width,
                        y: Math.random() * canvas.height,
                        radius: Math.random() * 2 + 1,
                        color: colors[Math.floor(Math.random() * colors.length)],
                        speedX: Math.random() * 1 - 0.5,
                        speedY: Math.random() * 1 - 0.5,
                        alpha: Math.random() * 0.5 + 0.1
                    });
                }
            }
            
            function drawParticles() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                for (let i = 0; i < particles.length; i++) {
                    const p = particles[i];
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                    ctx.fillStyle = p.color;
                    ctx.globalAlpha = p.alpha;
                    ctx.fill();
                    
                    // Connect particles
                    for (let j = i + 1; j < particles.length; j++) {
                        const p2 = particles[j];
                        const distance = Math.sqrt(Math.pow(p.x - p2.x, 2) + Math.pow(p.y - p2.y, 2));
                        
                        if (distance < 100) {
                            ctx.beginPath();
                            ctx.strokeStyle = p.color;
                            ctx.globalAlpha = p.alpha * 0.2 * (1 - distance / 100);
                            ctx.lineWidth = 0.5;
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(p2.x, p2.y);
                            ctx.stroke();
                        }
                    }
                }
                
                // Update particle positions
                for (let i = 0; i < particles.length; i++) {
                    const p = particles[i];
                    p.x += p.speedX;
                    p.y += p.speedY;
                    
                    // Bounce off edges
                    if (p.x < 0 || p.x > canvas.width) p.speedX *= -1;
                    if (p.y < 0 || p.y > canvas.height) p.speedY *= -1;
                }
                
                requestAnimationFrame(drawParticles);
            }
            
            window.addEventListener('resize', function() {
                resizeCanvas();
                createParticles();
            });
            
            resizeCanvas();
            createParticles();
            drawParticles();
            
            // Create data stream effect
            const dataStream = document.getElementById('data-stream');
            const streamCount = 20;
            
            for (let i = 0; i < streamCount; i++) {
                const dataLine = document.createElement('div');
                dataLine.className = 'data-line';
                dataLine.style.left = `${Math.random() * 100}%`;
                dataLine.style.height = `${Math.random() * 100 + 50}px`;
                dataLine.style.opacity = `${Math.random() * 0.5 + 0.1}`;
                dataLine.style.animationDuration = `${Math.random() * 5 + 3}s`;
                dataLine.style.animationDelay = `${Math.random() * 5}s`;
                dataStream.appendChild(dataLine);
            }
            
            <?php if ($pendingCount > 0): ?>
                var pendingModal = new bootstrap.Modal(document.getElementById('DialogPendingTopup'));
                pendingModal.show();
                
                const modalElement = document.getElementById('DialogPendingTopup');
                modalElement.addEventListener('hidden.bs.modal', function () {
                    window.location.href = '../index.php?mac=<?php echo "$mac" ?>';
                });
            <?php endif; ?>
        });

        let currentPurchaseUrl = "";

        function showConfirmationModal(selectedAmount, userId) {
            currentPurchaseUrl = "./request.php?mac=<?php echo "$mac" ?>&user_id=" + encodeURIComponent(userId) + "&amount=" + encodeURIComponent(selectedAmount);
            document.getElementById('selectedAmount').textContent = "Rp " + selectedAmount.toLocaleString('id-ID');
            var myModal = new bootstrap.Modal(document.getElementById('DialogPurchaseConfirmation')); 
            myModal.show();
        }

        function redirectToPurchase() {
            window.location.href = currentPurchaseUrl;
        }
    </script>

    <!-- Bootstrap -->
    <script src="../assets/js/lib/bootstrap.bundle.min.js"></script>
</body>
</html>
