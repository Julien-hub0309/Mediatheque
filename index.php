<?php
session_start();
require __DIR__ . '/config/db.php';

$base = '';

// Statistiques du tableau de bord
$nbLivresDispos   = (int) $pdo->query("SELECT COUNT(*) FROM LIVRE WHERE disponible = 1")->fetchColumn();
$nbAdherents      = (int) $pdo->query("SELECT COUNT(*) FROM ADHERENT")->fetchColumn();
$nbEmpruntsCours  = (int) $pdo->query("SELECT COUNT(*) FROM EMPRUNT WHERE date_retour IS NULL")->fetchColumn();
$nbEmpruntsRetard = (int) $pdo->query(
    "SELECT COUNT(*) FROM EMPRUNT WHERE date_retour_prevue < CURDATE() AND date_retour IS NULL"
)->fetchColumn();

$pageTitle = 'Accueil';
require __DIR__ . '/includes/header.php';
?>
<main>
    <section class="hero">
        <h1>Bienvenue sur la Médiathèque</h1>
        <p>Consultez les livres, gérez les adhérents et suivez les emprunts et les retours.</p>
    </section>

    <section class="container stats-grid">
        <div class="stat-card card-livres">
            <div class="icon">📖</div>
            <div class="number"><?php echo $nbLivresDispos; ?></div>
            <div class="label">Livres disponibles</div>
        </div>

        <div class="stat-card card-adherents">
            <div class="icon">👥</div>
            <div class="number"><?php echo $nbAdherents; ?></div>
            <div class="label">Adhérents</div>
        </div>

        <div class="stat-card card-emprunts">
            <div class="icon">📅</div>
            <div class="number"><?php echo $nbEmpruntsCours; ?></div>
            <div class="label">Emprunts en cours</div>
        </div>

        <div class="stat-card card-retard">
            <div class="icon">⚠️</div>
            <div class="number"><?php echo $nbEmpruntsRetard; ?></div>
            <div class="label">Emprunts en retard</div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
