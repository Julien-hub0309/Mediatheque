<?php
/**
 * En-tête commun.
 * Chaque page doit définir $base ('' pour la racine, '../' depuis /web)
 * avant d'inclure ce fichier. $pageTitle, $flashMessage et $flashType sont optionnels.
 */
$base = $base ?? '';
$pageTitle = $pageTitle ?? '';
$flashMessage = $flashMessage ?? '';
$flashType = $flashType ?? 'success';
$current = basename($_SERVER['SCRIPT_NAME']);

function navActive(string $file, string $current): string
{
    return $file === $current ? ' active' : '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médiathèque<?php echo $pageTitle !== '' ? ' — ' . htmlspecialchars($pageTitle) : ''; ?></title>
    <link rel="stylesheet" href="<?php echo $base; ?>modules/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="<?php echo $base; ?>index.php">
            <i class="fa-solid fa-book-open"></i> Médiathèque
        </a>

        <button class="menu-toggle" type="button" aria-label="Ouvrir le menu" aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
        </button>

        <nav>
            <ul class="nav-list">
                <li><a class="nav-link<?php echo navActive('index.php', $current); ?>" href="<?php echo $base; ?>index.php">Accueil</a></li>
                <li><a class="nav-link<?php echo navActive('adherent.php', $current); ?>" href="<?php echo $base; ?>web/adherent.php">Adhérents</a></li>
                <li><a class="nav-link<?php echo navActive('livres.php', $current); ?>" href="<?php echo $base; ?>web/livres.php">Livres</a></li>
                <li><a class="nav-link<?php echo navActive('emprunt.php', $current); ?>" href="<?php echo $base; ?>web/emprunt.php">Emprunts</a></li>
                <li><a class="nav-link<?php echo navActive('emprunter.php', $current); ?>" href="<?php echo $base; ?>web/emprunter.php">Emprunter</a></li>
                <li><a class="nav-link<?php echo navActive('retour.php', $current); ?>" href="<?php echo $base; ?>web/retour.php">Retour</a></li>
            </ul>
        </nav>

        <form class="search-form" action="<?php echo $base; ?>web/livres.php" method="get">
            <div class="search-wrap">
                <input
                    type="search"
                    name="q"
                    id="search-input"
                    placeholder="Rechercher un livre..."
                    value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                    autocomplete="off"
                >
                <button type="submit" aria-label="Rechercher">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>
    </div>
</header>

<?php if ($flashMessage !== ''): ?>
    <div class="flash flash-<?php echo htmlspecialchars($flashType); ?>" role="alert">
        <?php echo htmlspecialchars($flashMessage); ?>
    </div>
<?php endif; ?>
