<?php
session_start();
require __DIR__ . '/../config/db.php';

$base = '../';
$flashMessage = '';
$flashType = 'success';

// --- Ajout d'un adhérent ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_adherent'])) {
    $nom              = trim($_POST['nom'] ?? '');
    $prenom           = trim($_POST['prenom'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $date_inscription = $_POST['date_inscription'] ?? date('Y-m-d');

    if ($nom !== '' && $prenom !== '' && $email !== '') {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO ADHERENT (nom, prenom, email, date_inscription) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$nom, $prenom, $email, $date_inscription]);
            $flashMessage = "L'adhérent " . $prenom . " " . $nom . " a été ajouté avec succès.";
        } catch (PDOException $e) {
            $flashMessage = ($e->getCode() == 23000)
                ? "Cet email est déjà utilisé par un autre adhérent."
                : "Erreur lors de l'ajout de l'adhérent.";
            $flashType = 'error';
        }
    } else {
        $flashMessage = "Veuillez remplir tous les champs.";
        $flashType = 'error';
    }
}

// --- Suppression d'un adhérent ---
if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM ADHERENT WHERE id_adherent = ?");
        $stmt->execute([(int) $_GET['delete']]);
    } catch (PDOException $e) {
        // Un adhérent ayant des emprunts en cours ne peut pas être supprimé (contrainte de clé étrangère)
        $flashMessage = "Impossible de supprimer cet adhérent : il a des emprunts enregistrés.";
        $flashType = 'error';
    }
    if ($flashMessage === '') {
        header("Location: adherent.php");
        exit();
    }
}

$adherents = $pdo->query("SELECT * FROM ADHERENT ORDER BY nom, prenom")->fetchAll();

$pageTitle = 'Adhérents';
require __DIR__ . '/../includes/header.php';
?>
<main class="container">
    <h2>Adhérents</h2>

    <details class="add-panel">
        <summary>Ajouter un adhérent</summary>
        <form action="adherent.php" method="POST" class="form-grid">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="date_inscription">Date d'inscription</label>
                <input type="date" name="date_inscription" id="date_inscription" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-actions">
                <button type="submit" name="add_adherent" class="btn btn-primary">Ajouter l'adhérent</button>
            </div>
        </form>
    </details>

    <table id="data-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Date d'inscription</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($adherents) > 0): ?>
                <?php foreach ($adherents as $adh): ?>
                    <tr>
                        <td data-label="Nom"><?php echo htmlspecialchars($adh['nom']); ?></td>
                        <td data-label="Prénom"><?php echo htmlspecialchars($adh['prenom']); ?></td>
                        <td data-label="Email"><?php echo htmlspecialchars($adh['email']); ?></td>
                        <td data-label="Inscription"><?php echo htmlspecialchars(date('d/m/Y', strtotime($adh['date_inscription']))); ?></td>
                        <td data-label="Actions">
                            <a href="?delete=<?php echo $adh['id_adherent']; ?>" class="btn btn-info btn-delete" data-confirm="Supprimer l'adhérent <?php echo htmlspecialchars(addslashes($adh['prenom'] . ' ' . $adh['nom'])); ?> ?">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="no-result">Aucun adhérent trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
