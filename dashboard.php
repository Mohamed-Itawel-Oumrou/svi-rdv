<!-- /var/www/html/rendezvous/ -->
<?php
// Connexion à MySQL
$host = 'localhost';
$db = 'hopital';
$user = 'root';
$pass = 'passer';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Total de rendez-vous
$total = $conn->query("SELECT COUNT(*) as total FROM rendezvous")->fetch_assoc()['total'];

// Rendez-vous aujourd’hui
$aujourdhui = date('Y-m-d');
$rdv_today = $conn->query("SELECT COUNT(*) as total FROM rendezvous WHERE date_rdv = '$aujourdhui'")->fetch_assoc()['total'];

// Rendez-vous demain
$demain = date('Y-m-d', strtotime('+1 day'));
$rdv_demain = $conn->query("SELECT COUNT(*) as total FROM rendezvous WHERE date_rdv = '$demain'")->fetch_assoc()['total'];

// RDV par service
$services = $conn->query("SELECT service, COUNT(*) as total FROM rendezvous GROUP BY service");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - Rendez-vous</title>
    <style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: linear-gradient(120deg, #f4f8fb 60%, #e3eefe 100%);
        margin: 0;
    }
    .navbar {
        background: linear-gradient(90deg, #007BFF 60%, #0056b3 100%);
        padding: 18px 0;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .navbar a {
        color: #fff;
        text-decoration: none;
        margin: 0 28px;
        font-weight: 600;
        font-size: 20px;
        letter-spacing: 1px;
        transition: color 0.2s;
    }
    .navbar a:hover {
        color: #ffd700;
        text-decoration: underline;
    }
    .container {
        max-width: 900px;
        margin: 40px auto 0 auto;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        padding: 40px 40px 60px 40px;
    }
    h1 {
        color: #222;
        margin-bottom: 30px;
        font-size: 2.2em;
        letter-spacing: 1px;
        text-align: center;
    }
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 28px;
        margin-bottom: 32px;
    }
    .card {
        background: linear-gradient(120deg, #e3eefe 60%, #f9f9f9 100%);
        padding: 32px 24px;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        text-align: center;
        transition: transform 0.18s, box-shadow 0.18s;
        position: relative;
        overflow: hidden;
    }
    .card:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 8px 32px rgba(0,123,255,0.10);
    }
    .card h2 {
        color: #007BFF;
        margin-bottom: 12px;
        font-size: 1.3em;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .card p {
        font-size: 2.1em;
        color: #222;
        margin: 0;
        font-weight: 600;
    }
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-top: 18px;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    th, td {
        padding: 14px 18px;
        border-bottom: 1px solid #e9ecef;
        text-align: left;
        font-size: 1.05em;
    }
    th {
        background: #f1f5fa;
        color: #007BFF;
        font-weight: 700;
        border-bottom: 2px solid #007BFF;
    }
    tr:last-child td {
        border-bottom: none;
    }
    tr:hover {
        background: #f6fbff;
        transition: background 0.2s;
    }

    </style>
</head>
<body>

<div class="navbar">
  <a href="dashboard.php">Dashboard</a>
  <a href="rendezvous.php">Rendez-vous</a>
</div>

<div class="container">

<h1>Tableau de bord</h1>

<div class="grid">
    <div class="card">
        <h2>Total RDV</h2>
        <p><?= $total ?></p>
    </div>
    <div class="card">
        <h2>RDV Aujourd’hui</h2>
        <p><?= $rdv_today ?></p>
    </div>
    <div class="card">
        <h2>RDV Demain</h2>
        <p><?= $rdv_demain ?></p>
    </div>
</div>

<div class="card">
    <h2>RDV par service</h2>
    <table>
        <tr><th>Service</th><th>Nombre</th></tr>
        <?php while ($row = $services->fetch_assoc()): ?>
            <tr><td><?= htmlspecialchars($row['service']) ?></td><td><?= $row['total'] ?></td></tr>
        <?php endwhile; ?>
    </table>
</div>

</div>

</body>
</html>
