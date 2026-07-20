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
            <a href="/log_admin" class="btn btn-outline-primary"><i class="fas fa-home"></i> Accueil</a>
            <a href="/operators/create" class="btn btn-primary">+ Ajouter un opérateur</a>
        </div>

        <div class="card mb-4">
            <div class="card-header"><strong>Opérateurs configurés</strong></div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Préfixe principal</th>
                            <th>Autres préfixes</th>
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
                                    <?php if (!empty($op['other_prefixes'])): ?>
                                        <?php foreach (explode(',', $op['other_prefixes']) as $p): ?>
                                            <span class="badge bg-secondary"><?= esc(trim($p)) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/operators/edit/<?= $op['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                                    <a href="/operators/delete/<?= $op['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet opérateur ?')">Supprimer</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Aucun opérateur configuré.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if (!empty($commissions)): ?>
        <div class="card">
            <div class="card-header"><strong>Commissions inter-opérateurs</strong></div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Opérateur source</th>
                            <th>Opérateur cible</th>
                            <th>Commission (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commissions as $c): ?>
                        <tr>
                            <td><?= esc($c['operator_name']) ?></td>
                            <td><?= esc($c['target_name']) ?></td>
                            <td><strong><?= number_format($c['commission_percent'], 2) ?>%</strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>