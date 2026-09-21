<?php
session_start();
require __DIR__ . '/../config/db.php';

$base = '../';

$sql = "SELECT E.id_emprunt, CONCAT(A.prenom, ' ', A.nom) AS adherent, L.titre AS livre,
               E.date_emprunt, E.date_retour_prevue, E.date_retour
        FROM EMPRUNT E
        JOIN ADHERENT A ON A.id_adherent = E.id_adherent
        JOIN LIVRE L ON L.id_livre = E.id_livre
        ORDER BY E.date_emprunt DESC";
$emprunts = $pdo->query($sql)->fetchAll();

$pageTitle = 'Emprunts';
require __DIR__ . '/../includes/header.php';
?>
<main class="container">
    <h2>Liste des emprunts</h2>

    <table id="data-table">
        <thead>
            <tr>
                <th>Adhérent</th>
                <th>Livre</th>
                <th>Date d'emprunt</th>
                <th>Retour prévu</th>
                <th>État</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($emprunts) > 0): ?>
                <?php foreach ($emprunts as $e): ?>
                    <?php $enRetard = !$e['date_retour'] && strtotime($e['date_retour_prevue']) < strtotime(date('Y-m-d')); ?>
                    <tr>
                        <td data-label="Adhérent"><?php echo htmlspecialchars($e['adherent']); ?></td>
                        <td data-label="Livre"><?php echo htmlspecialchars($e['livre']); ?></td>
                        <td data-label="Date d'emprunt"><?php echo htmlspecialchars(date('d/m/Y', strtotime($e['date_emprunt']))); ?></td>
                        <td data-label="Retour prévu"><?php echo htmlspecialchars(date('d/m/Y', strtotime($e['date_retour_prevue']))); ?></td>
                        <td data-label="État">
                            <?php if ($e['date_retour']): ?>
                                <span class="badge badge-disponible">Rendu le <?php echo date('d/m/Y', strtotime($e['date_retour'])); ?></span>
                            <?php elseif ($enRetard): ?>
                                <span class="badge badge-retard">En retard</span>
                            <?php else: ?>
                                <span class="badge badge-emprunte">En cours</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="no-result">Aucun emprunt enregistré.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
