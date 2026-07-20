<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un opérateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Ajouter un opérateur</h1>
        <p class="text-muted">Configurez un nouveau préfixe téléphonique valable</p>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/operators/store" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="name" class="form-label">Nom de l'opérateur</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Orange" value="<?= old('name') ?>" required>
            </div>
            <div class="mb-3">
                <label for="prefix" class="form-label">Préfixe principal</label>
                <input type="text" class="form-control" id="prefix" name="prefix" placeholder="033" maxlength="10" value="<?= old('prefix') ?>" required>
            </div>
            <div class="mb-3">
                <label for="other_prefixes" class="form-label">Autres préfixes valables</label>
                <input type="text" class="form-control" id="other_prefixes" name="other_prefixes" placeholder="032,031,038" value="<?= old('other_prefixes') ?>">
                <div class="form-text">Préfixes séparés par des virgules (ex: 032,031,038)</div>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/operators" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>