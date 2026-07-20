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
                <input type="text" name="name" id="name" class="form-control" value="<?= old('name') ?>" placeholder="Ex: Orange, Airtel, Djezzy..." required>
            </div>
            <div class="mb-3">
                <label for="prefix" class="form-label">Préfixe</label>
                <input type="text" name="prefix" id="prefix" class="form-control" value="<?= old('prefix') ?>" placeholder="Ex: 033, 037..." required>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/operators" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>