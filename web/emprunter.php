<?php
session_start();
require __DIR__ . '/../config/db.php';

$base = '../';
$flashMessage = '';
$flashType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_adherent        = $_POST['adherent'] ?? null;
    $id_livre           = $_POST['livre'] ?? null;
    $date_emprunt       = $_POST['date_emprunt'] ?? null;
    $date_retour_prevue = $_POST['date_retour_prevue'] ?? null;

    if ($id_adherent && $id_livre && $date_emprunt && $date_retour_prevue) {
        try {
            $pdo->beginTransaction();

            $stmtInsert = $pdo->prepare(
                "INSERT INTO EMPRUNT (id_adherent, id_livre, date_emprunt, date_retour_prevue)
                 VALUES (?, ?, ?, ?)"
            );
            $stmtInsert->execute([$id_adherent, $id_livre, $date_emprunt, $date_retour_prevue]);

            // Le livre passe en statut "emprunté"
            $stmtUpdate = $pdo->prepare("UPDATE LIVRE SET disponible = 0 WHERE id_livre = ?");
            $stmtUpdate->execute([$id_livre]);

            $pdo->commit();
            $flashMessage = "Emprunt enregistré avec succès !";
        } catch (Exception $e) {
            $pdo->rollBack();
            $flashMessage = "Erreur lors de l'enregistrement de l'emprunt.";
            $flashType = 'error';
        }
    } else {
        $flashMessage = "Veuillez remplir tous les champs.";
        $flashType = 'error';
    }
}

// Listes déroulantes
$adherents = $pdo->query(
    "SELECT id_adherent, CONCAT(prenom, ' ', nom) AS nom_complet FROM ADHERENT ORDER BY nom"
)->fetchAll();

// Uniquement les livres disponibles
$livresDisponibles = $pdo->query(
    "SELECT id_livre, titre FROM LIVRE WHERE disponible = 1 ORDER BY titre"
)->fetchAll();

// Dates par défaut (aujourd'hui et dans 14 jours)
$dateAujourdhui       = date('Y-m-d');
$dateDansDeuxSemaines = date('Y-m-d', strtotime('+14 days'));

$pageTitle = 'Emprunter';
require __DIR__ . '/../includes/header.php';
?>
<main class="container">
    <h2>Nouvel emprunt</h2>

    <form action="emprunter.php" method="POST" class="form-grid">
        <div class="form-group">
            <label for="adherent">Adhérent</label>
            <select name="adherent" id="adherent" required>
                <option value="">Sélectionner un adhérent...</option>
                <?php foreach ($adherents as $adh): ?>
                    <option value="<?php echo $adh['id_adherent']; ?>">
                        <?php echo htmlspecialchars($adh['nom_complet']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="livre">Livre (disponibles uniquement)</label>
            <select name="livre" id="livre" required>
                <option value="">Sélectionner un livre...</option>
                <?php foreach ($livresDisponibles as $livre): ?>
                    <option value="<?php echo $livre['id_livre']; ?>">
                        <?php echo htmlspecialchars($livre['titre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="date_emprunt">Date d'emprunt</label>
            <input type="date" name="date_emprunt" id="date_emprunt" value="<?php echo $dateAujourdhui; ?>" required>
        </div>

        <div class="form-group">
            <label for="date_retour_prevue">Date de retour prévue</label>
            <input type="date" name="date_retour_prevue" id="date_retour_prevue" value="<?php echo $dateDansDeuxSemaines; ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer l'emprunt</button>
            <a href="emprunt.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
