<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opérateurs - Configuration des préfixes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Configuration des préfixes</h1>
        <p class="text-muted">Gérez les préfixes téléphoniques valables (ex: 033, 037)</p>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="/operators/create" class="btn btn-primary">+ Ajouter un opérateur</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Préfixe</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! empty($operators)): ?>
                    <?php foreach ($operators as $op): ?>
                    <tr>
                        <td><?= esc($op['id']) ?></td>
                        <td><?= esc($op['name']) ?></td>
                        <td><span class="badge bg-info"><?= esc($op['prefix']) ?></span></td>
                        <td>
                            <a href="/operators/edit/<?= $op['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                            <a href="/operators/delete/<?= $op['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet opérateur ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Aucun opérateur configuré.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="/" class="btn btn-secondary">Retour</a>
    </div>
</body>
</html>