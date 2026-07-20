<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situation des gains via les frais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Situation des gains via les frais</h1>
        <p class="text-muted">Récapitulatif des frais collectés sur les retraits et transferts</p>

        <?php if (isset($dbError)): ?>
            <div class="alert alert-warning"><?= esc($dbError) ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="/" class="btn btn-outline-secondary">← Accueil</a>
            <a href="/reports/accounts-summary" class="btn btn-outline-info">Situation des comptes clients</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Type d'opération</th>
                    <th>Libellé</th>
                    <th>Total opérations</th>
                    <th>Total montants traités (Ar)</th>
                    <th>Total frais collectés (Ar)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! empty($feesSummary)): ?>
                    <?php foreach ($feesSummary as $row): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= esc($row['operation_type']) ?></span></td>
                        <td><?= esc($row['operation_label']) ?></td>
                        <td class="text-end"><?= number_format($row['total_operations'], 0, ',', ' ') ?></td>
                        <td class="text-end"><?= number_format($row['total_amount_transacted'], 2, ',', ' ') ?></td>
                        <td class="text-end"><strong><?= number_format($row['total_fees_collected'], 2, ',', ' ') ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucune opération avec frais pour le moment.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (! empty($feesSummary)): ?>
            <?php
            $totalFees = array_sum(array_column($feesSummary, 'total_fees_collected'));
            $totalAmount = array_sum(array_column($feesSummary, 'total_amount_transacted'));
            ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Total des gains (frais)</div>
                        <div class="card-body">
                            <h3 class="card-title"><?= number_format($totalFees, 2, ',', ' ') ?> Ar</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header">Total montants traités</div>
                        <div class="card-body">
                            <h3 class="card-title"><?= number_format($totalAmount, 2, ',', ' ') ?> Ar</h3>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>