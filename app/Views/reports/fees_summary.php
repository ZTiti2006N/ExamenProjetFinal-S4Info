<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situation des gains via les frais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Situation des gains via les frais</h1>
        <p class="text-muted">Récapitulatif des frais collectés sur les retraits et transferts, par opérateur</p>

        <?php if (isset($dbError)): ?>
            <div class="alert alert-warning"><?= esc($dbError) ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="/log_admin" class="btn btn-outline-primary"><i class="fas fa-home"></i> Accueil</a>
            <a href="/reports/accounts-summary" class="btn btn-outline-info"><i class="fas fa-users"></i> Comptes clients</a>
            <a href="/reports/operator-payouts" class="btn btn-outline-success"><i class="fas fa-hand-holding-usd"></i> Montants à envoyer</a>
        </div>

        <?php if (!empty($groupedByOperator)): ?>
            <?php foreach ($groupedByOperator as $opName => $rows): ?>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong><i class="fas fa-building me-2"></i><?= esc($opName) ?></strong>
                    <span class="badge bg-primary rounded-pill">
                        Total frais: <?= number_format($totalByOperator[$opName]['total_fees'], 2, ',', ' ') ?> Ar
                    </span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type d'opération</th>
                                <th>Libellé</th>
                                <th>Total opérations</th>
                                <th>Total montants traités (Ar)</th>
                                <th>Total frais collectés (Ar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= esc($row['operation_type']) ?></span></td>
                                <td><?= esc($row['operation_label']) ?></td>
                                <td class="text-end"><?= number_format($row['total_operations'], 0, ',', ' ') ?></td>
                                <td class="text-end"><?= number_format($row['total_amount_transacted'], 2, ',', ' ') ?></td>
                                <td class="text-end"><strong><?= number_format($row['total_fees_collected'], 2, ',', ' ') ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <td colspan="2"><strong>Sous-total <?= esc($opName) ?></strong></td>
                                <td class="text-end"><strong><?= number_format($totalByOperator[$opName]['total_ops'], 0, ',', ' ') ?></strong></td>
                                <td class="text-end"><strong><?= number_format($totalByOperator[$opName]['total_amount'], 2, ',', ' ') ?></strong></td>
                                <td class="text-end"><strong><?= number_format($totalByOperator[$opName]['total_fees'], 2, ',', ' ') ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Grand total -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Total général des gains (frais)</div>
                        <div class="card-body">
                            <h3 class="card-title"><?= number_format($grandTotalFees, 2, ',', ' ') ?> Ar</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header">Total général montants traités</div>
                        <div class="card-body">
                            <h3 class="card-title"><?= number_format($grandTotalAmount, 2, ',', ' ') ?> Ar</h3>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Aucune opération avec frais pour le moment.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>