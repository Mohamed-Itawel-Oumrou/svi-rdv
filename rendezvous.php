<!-- /var/www/html/rendezvous/ -->
<?php
// Connexion MySQL
$host = 'localhost';
$db = 'hopital';
$user = 'root';
$pass = 'passer';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Suppression
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Récup info pour SMS
    $res = $conn->query("SELECT * FROM rendezvous WHERE id = $id");
    if ($res && $rdv = $res->fetch_assoc()) {
        $extension = $rdv['numero'];
        $service = $rdv['service'];

        $message = "Votre rendez-vous en $service a été annulé.";
        file_put_contents("/var/spool/asterisk/tmp/message_{$extension}.txt", $message);

        shell_exec("asterisk -rx \"channel originate Local/{$extension}@envoi_sms extension {$extension}@envoi_sms\"");
    }

    // Supprimer le RDV
    $conn->query("DELETE FROM rendezvous WHERE id = $id");
    header("Location: rendezvous.php");
    exit;
}

// Lecture des rendez-vous
$rdvs = $conn->query("SELECT * FROM rendezvous ORDER BY date_rdv");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liste des Rendez-vous</title>
    <style>
        .container {padding: 40px 80px 150px;}
        body { font-family: Arial, sans-serif; background: #f9f9f9; margin:0;}
        h1 { color: #444; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background: #eee; }
        a.delete { color: red; text-decoration: none; }

        .navbar {
    background-color: #007BFF;
    padding: 15px;
    text-align: center;
  }

  .navbar a {
    color: white;
    text-decoration: none;
    margin: 0 20px;
    font-weight: bold;
    font-size: 18px;
  }

  .navbar a:hover {
    text-decoration: underline;
  }


    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: #f4f8fb;
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
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-top: 20px;
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
    a.delete {
        color: #e74c3c;
        background: #fbeee6;
        padding: 6px 14px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        transition: background 0.2s, color 0.2s;
        border: 1px solid #f5c6cb;
    }
    a.delete:hover {
        background: #e74c3c;
        color: #fff;
        border-color: #e74c3c;
    }


    </style>
</head>
<body>

<div class="navbar">
  <a href="dashboard.php">Dashboard</a>
  <a href="rendezvous.php">Rendez-vous</a>
</div>

<div class="container">
<h1>Liste des rendez-vous</h1>

<table>
    <tr>
        <th>ID</th>
        <th>Extension</th>
        <th>Service</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $rdvs->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['numero'] ?></td>
        <td><?= htmlspecialchars($row['service']) ?></td>
        <td><?= $row['date_rdv'] ?></td>
        <td><a class="delete" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a></td>
    </tr>
    <?php endwhile; ?>
</table>

</div>

</body>
</html>
