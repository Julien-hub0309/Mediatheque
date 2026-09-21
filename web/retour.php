<?php
session_start();
require __DIR__ . '/../config/db.php';

$base = '../';
$flashMessage = '';
$flashType = 'success';

// --- Enregistrement d'un retour ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_emprunt'])) {
    $id_emprunt = (int) $_POST['id_emprunt'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT id_livre FROM EMPRUNT WHERE id_emprunt = ? AND date_retour IS NULL");
        $stmt->execute([$id_emprunt]);
        $id_livre = $stmt->fetchColumn();

        if ($id_livre) {
            $stmtMaj = $pdo->prepare("UPDATE EMPRUNT SET date_retour = CURDATE() WHERE id_emprunt = ?");
            $stmtMaj->execute([$id_emprunt]);

            $stmtLivre = $pdo->prepare("UPDATE LIVRE SET disponible = 1 WHERE id_livre = ?");
            $stmtLivre->execute([$id_livre]);

            $pdo->commit();
            $flashMessage = "Retour enregistré avec succès.";
        } else {
            $pdo->rollBack();
            $flashMessage = "Cet emprunt est introuvable ou déjà rendu.";
            $flashType = 'error';
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $flashMessage = "Erreur lors de l'enregistrement du retour.";
        $flashType = 'error';
    }
}

// Emprunts encore en cours (non rendus)
$sql = "SELECT E.id_emprunt, CONCAT(A.prenom, ' ', A.nom) AS adherent, L.titre AS livre,
               E.date_emprunt, E.date_retour_prevue
        FROM EMPRUNT E
        JOIN ADHERENT A ON A.id_adherent = E.id_adherent
        JOIN LIVRE L ON L.id_livre = E.id_livre
        WHERE E.date_retour IS NULL
        ORDER BY E.date_retour_prevue ASC";
$empruntsEnCours = $pdo->query($sql)->fetchAll();

$pageTitle = 'Retour';
require __DIR__ . '/../includes/header.php';
?>
<main class="container">
    <h2>Retour d'un livre</h2>

    <table id="data-table">
        <thead>
            <tr>
                <th>Adhérent</th>
                <th>Livre</th>
                <th>Date d'emprunt</th>
                <th>Retour prévu</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($empruntsEnCours) > 0): ?>
                <?php foreach ($empruntsEnCours as $e): ?>
                    <?php $enRetard = strtotime($e['date_retour_prevue']) < strtotime(date('Y-m-d')); ?>
                    <tr>
                        <td data-label="Adhérent"><?php echo htmlspecialchars($e['adherent']); ?></td>
                        <td data-label="Livre"><?php echo htmlspecialchars($e['livre']); ?></td>
                        <td data-label="Date d'emprunt"><?php echo htmlspecialchars(date('d/m/Y', strtotime($e['date_emprunt']))); ?></td>
                        <td data-label="Retour prévu">
                            <?php echo htmlspecialchars(date('d/m/Y', strtotime($e['date_retour_prevue']))); ?>
                            <?php if ($enRetard): ?><span class="badge badge-retard">En retard</span><?php endif; ?>
                        </td>
                        <td data-label="Actions">
                            <form action="retour.php" method="POST" class="inline-form">
                                <input type="hidden" name="id_emprunt" value="<?php echo $e['id_emprunt']; ?>">
                                <button type="submit" class="btn btn-primary">Marquer comme rendu</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="no-result">Aucun emprunt en cours.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
