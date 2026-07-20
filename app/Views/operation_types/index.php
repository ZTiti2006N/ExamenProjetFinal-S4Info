<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Types d'opérations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Types d'opérations</h1>
        <p class="text-muted">Gérez les types d'opérations (dépôt, retrait, transfert, etc.)</p>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="/operation-types/create" class="btn btn-primary">+ Ajouter un type d'opération</a>
            <a href="/operators" class="btn btn-outline-secondary">← Gestion des préfixes</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Libellé</th>
                    <th>Frais</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! empty($operationTypes)): ?>
                    <?php foreach ($operationTypes as $ot): ?>
                    <tr>
                        <td><?= esc($ot['id']) ?></td>
                        <td><span class="badge bg-secondary"><?= esc($ot['code']) ?></span></td>
                        <td><?= esc($ot['label']) ?></td>
                        <td>
                            <?php if ($ot['has_fees']): ?>
                                <span class="badge bg-warning text-dark">Avec frais</span>
                            <?php else: ?>
                                <span class="badge bg-success">Sans frais</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/operation-types/detail/<?= $ot['id'] ?>" class="btn btn-sm btn-info">Détail</a>
                            <a href="/operation-types/edit/<?= $ot['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                            <a href="/operation-types/delete/<?= $ot['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce type d\'opération et tous ses barèmes ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucun type d'opération.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>