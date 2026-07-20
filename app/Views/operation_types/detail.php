<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail : <?= esc($operationType['label']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Détail du type d'opération</h1>
        <p class="text-muted">Consultez les informations et les barèmes de frais</p>

        <!-- Informations du type d'opération -->
        <div class="card mb-4">
            <div class="card-header">
                <strong>Informations</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Code :</strong>
                        <span class="badge bg-secondary"><?= esc($operationType['code']) ?></span>
                    </div>
                    <div class="col-md-3">
                        <strong>Libellé :</strong>
                        <?= esc($operationType['label']) ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Frais :</strong>
                        <?php if ($operationType['has_fees']): ?>
                            <span class="badge bg-warning text-dark">Avec frais</span>
                        <?php else: ?>
                            <span class="badge bg-success">Sans frais</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barèmes de frais (lecture seule) -->
        <div class="card mb-4">
            <div class="card-header">
                <strong>Barèmes de frais</strong>
            </div>
            <div class="card-body">
                <?php if (! empty($feeScales)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Montant min (Ar)</th>
                                    <th>Montant max (Ar)</th>
                                    <th>Frais fixes (Ar)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($feeScales as $fs): ?>
                                <tr>
                                    <td class="text-end"><?= number_format($fs['min_amount'], 2, ',', ' ') ?></td>
                                    <td class="text-end"><?= number_format($fs['max_amount'], 2, ',', ' ') ?></td>
                                    <td class="text-end"><?= number_format($fs['fee_fixed'], 2, ',', ' ') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Aucun barème défini pour ce type d'opération.</p>
                <?php endif; ?>
            </div>
        </div>

        <a href="/operation-types" class="btn btn-secondary">← Retour à la liste</a>
    </div>
</body>
</html>