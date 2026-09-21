<?php
session_start();
require __DIR__ . '/../config/db.php';

$base = '../';

// Récupération des messages flash (stockés en session après une redirection)
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    $flashType = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
} else {
    $flashMessage = '';
    $flashType = 'success';
}

// --- Suppression d'un livre ---
if (isset($_GET['delete'])) {
    $id_livre = (int) $_GET['delete'];
    try {
        $pdo->beginTransaction();

        // 1. Supprimer les liens avec les auteurs
        $stmtAuteur = $pdo->prepare("DELETE FROM LIVRE_AUTEUR WHERE id_livre = ?");
        $stmtAuteur->execute([$id_livre]);

        // 2. Supprimer l'historique des emprunts liés à ce livre pour contourner la contrainte de clé étrangère
        $stmtEmprunt = $pdo->prepare("DELETE FROM EMPRUNT WHERE id_livre = ?");
        $stmtEmprunt->execute([$id_livre]);

        // 3. Enfin, supprimer le livre
        $stmtLivre = $pdo->prepare("DELETE FROM LIVRE WHERE id_livre = ?");
        $stmtLivre->execute([$id_livre]);

        $pdo->commit();
        $_SESSION['flash_message'] = "Le livre et son historique ont été supprimés avec succès.";
        $_SESSION['flash_type'] = "success";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_message'] = "Erreur lors de la suppression du livre : " . $e->getMessage();
        $_SESSION['flash_type'] = "error";
    }
    header("Location: livres.php");
    exit();
}

// --- Ajout d'un livre ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_livre'])) {
    $titre         = trim($_POST['titre'] ?? '');
    $isbn          = trim($_POST['isbn'] ?? '');
    $annee         = trim($_POST['annee_publication'] ?? '');
    $categorieNom  = trim($_POST['categorie'] ?? '');
    $auteursTexte  = trim($_POST['auteurs'] ?? '');

    if ($titre !== '' && $isbn !== '' && $annee !== '' && $categorieNom !== '') {
        try {
            $pdo->beginTransaction();

            // 1. Gestion de la catégorie (recherche ou insertion automatique)
            $stmtC = $pdo->prepare("SELECT id_categorie FROM CATEGORIE WHERE libelle = ?");
            $stmtC->execute([$categorieNom]);
            $id_categorie = $stmtC->fetchColumn();

            if (!$id_categorie) {
                $stmtInsC = $pdo->prepare("INSERT INTO CATEGORIE (libelle) VALUES (?)");
                $stmtInsC->execute([$categorieNom]);
                $id_categorie = (int) $pdo->lastInsertId();
            }

            // 2. Insertion du livre
            $stmt = $pdo->prepare(
                "INSERT INTO LIVRE (titre, isbn, annee_publication, id_categorie) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$titre, $isbn, (int) $annee, $id_categorie]);
            $id_livre = (int) $pdo->lastInsertId();

            // 3. Gestion des auteurs
            if ($auteursTexte !== '') {
                foreach (explode(',', $auteursTexte) as $nomComplet) {
                    $nomComplet = trim($nomComplet);
                    if ($nomComplet === '') {
                        continue;
                    }
                    $parts  = preg_split('/\s+/', $nomComplet, 2);
                    $prenom = $parts[0];
                    $nom    = $parts[1] ?? $parts[0];

                    $stmtA = $pdo->prepare("SELECT id_auteur FROM AUTEUR WHERE nom = ? AND prenom = ?");
                    $stmtA->execute([$nom, $prenom]);
                    $id_auteur = $stmtA->fetchColumn();

                    if (!$id_auteur) {
                        $stmtIns = $pdo->prepare("INSERT INTO AUTEUR (nom, prenom) VALUES (?, ?)");
                        $stmtIns->execute([$nom, $prenom]);
                        $id_auteur = (int) $pdo->lastInsertId();
                    }

                    $stmtLa = $pdo->prepare(
                        "INSERT IGNORE INTO LIVRE_AUTEUR (id_livre, id_auteur) VALUES (?, ?)"
                    );
                    $stmtLa->execute([$id_livre, $id_auteur]);
                }
            }

            $pdo->commit();
            $flashMessage = "Le livre « " . htmlspecialchars($titre) . " » a été ajouté avec succès.";
            $flashType = 'success';
        } catch (Exception $e) {
            $pdo->rollBack();
            $flashMessage = "Erreur lors de l'ajout du livre. Vérifiez que l'ISBN n'existe pas déjà.";
            $flashType = 'error';
        }
    } else {
        $flashMessage = "Veuillez remplir tous les champs obligatoires.";
        $flashType = 'error';
    }
}

// --- Recherche ---
$q = trim($_GET['q'] ?? '');

$sql = "SELECT L.id_livre, L.titre, L.isbn, L.annee_publication, L.disponible,
               C.libelle AS categorie,
               GROUP_CONCAT(DISTINCT CONCAT(A.prenom, ' ', A.nom) ORDER BY A.nom SEPARATOR ', ') AS auteurs
        FROM LIVRE L
        JOIN CATEGORIE C ON C.id_categorie = L.id_categorie
        LEFT JOIN LIVRE_AUTEUR LA ON LA.id_livre = L.id_livre
        LEFT JOIN AUTEUR A ON A.id_auteur = LA.id_auteur";

$params = [];
if ($q !== '') {
    $sql .= " WHERE L.titre LIKE ? OR L.isbn LIKE ? OR C.libelle LIKE ?";
    $like = "%$q%";
    $params = [$like, $like, $like];
}
$sql .= " GROUP BY L.id_livre ORDER BY L.titre";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$livres = $stmt->fetchAll();

$pageTitle = 'Livres';
require __DIR__ . '/../includes/header.php';
?>
<main class="container">
    <h2>Catalogue des livres<?php echo $q !== '' ? ' — résultats pour « ' . htmlspecialchars($q) . ' »' : ''; ?></h2>

    <?php if ($flashMessage !== ''): ?>
        <div class="alert alert-<?php echo $flashType; ?>">
            <?php echo htmlspecialchars($flashMessage); ?>
        </div>
    <?php endif; ?>

    <details class="add-panel">
        <summary>Ajouter un livre</summary>
            <form action="livres.php" method="POST" class="form-grid">
                <div class="form-group">
                    <label for="titre">Titre</label>
                    <input type="text" name="titre" id="titre" required>
                </div>
                <div class="form-group">
                    <label for="isbn">ISBN</label>
                    <input type="text" name="isbn" id="isbn" required>
                </div>
                <div class="form-group">
                    <label for="annee_publication">Année de publication</label>
                    <input type="number" name="annee_publication" id="annee_publication" min="0" max="<?php echo date('Y'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="categorie">Catégorie <span class="hint">(ex : Roman, Science-Fiction...)</span></label>
                    <input type="text" name="categorie" id="categorie" required>
                </div>
                <div class="form-group form-group-wide">
                    <label for="auteurs">Auteur(s) <span class="hint">(séparés par une virgule, ex : Victor Hugo, Jules Verne)</span></label>
                    <input type="text" name="auteurs" id="auteurs">
                </div>
                <div class="form-actions">
                    <button type="submit" name="add_livre" class="btn btn-primary">Ajouter le livre</button>
                </div>
            </form>
    </details>

    <table id="data-table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur(s)</th>
                <th>Catégorie</th>
                <th>Année</th>
                <th>ISBN</th>
                <th>Disponibilité</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($livres) > 0): ?>
                <?php foreach ($livres as $livre): ?>
                    <tr>
                        <td data-label="Titre"><?php echo htmlspecialchars($livre['titre']); ?></td>
                        <td data-label="Auteur(s)"><?php echo htmlspecialchars($livre['auteurs'] ?? '') !== '' ? htmlspecialchars($livre['auteurs']) : '—'; ?></td>
                        <td data-label="Catégorie"><?php echo htmlspecialchars($livre['categorie']); ?></td>
                        <td data-label="Année"><?php echo htmlspecialchars($livre['annee_publication']); ?></td>
                        <td data-label="ISBN"><?php echo htmlspecialchars($livre['isbn']); ?></td>
                        <td data-label="Disponibilité">
                            <?php if ($livre['disponible'] == 1): ?>
                                <span class="badge badge-disponible">Disponible</span>
                            <?php else: ?>
                                <span class="badge badge-emprunte">Emprunté</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Actions">
                            <a href="?delete=<?php echo $livre['id_livre']; ?>" class="btn btn-info btn-delete" data-confirm="Supprimer le livre « <?php echo htmlspecialchars(addslashes($livre['titre'])); ?> » ?">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="no-result">Aucun livre trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>