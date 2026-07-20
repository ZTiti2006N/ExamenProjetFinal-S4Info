<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Montants à envoyer aux opérateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Montants à envoyer aux opérateurs</h1>
        <p class="text-muted">Situation des montants à reverser à chaque opérateur</p>

        <?php if (isset($dbError)): ?>
            <div class="alert alert-warning"><?= esc($dbError) ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="/log_admin" class="btn btn-outline-primary"><i class="fas fa-home"></i> Accueil</a>
            <a href="/reports/fees-summary" class="btn btn-outline-info"><i class="fas fa-coins"></i> Gains (frais)</a>
            <a href="/reports/accounts-summary" class="btn btn-outline-info"><i class="fas fa-users"></i> Comptes clients</a>
        </div>

        <?php if (!empty($payouts)): ?>
            <div class="card">
                <div class="card-header"><strong>Récapitulatif</strong></div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Opérateur</th>
                                <th class="text-end">Transferts reçus</th>
                                <th class="text-end">Total montants reçus (Ar)</th>
                                <th class="text-end">Total frais perçus (Ar)</th>
                                <th class="text-end">Commission due (Ar)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payouts as $row): ?>
                            <tr>
                                <td><strong><?= esc($row['operator_name']) ?></strong></td>
                                <td class="text-end"><?= number_format($row['total_transferts_recus'], 0, ',', ' ') ?></td>
                                <td class="text-end"><?= number_format($row['total_montants_recus'], 2, ',', ' ') ?></td>
                                <td class="text-end"><?= number_format($row['total_frais_percus'], 2, ',', ' ') ?></td>
                                <td class="text-end">
                                    <?php if ((float)$row['commission_due'] > 0): ?>
                                        <strong class="text-success"><?= number_format($row['commission_due'], 2, ',', ' ') ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">0,00</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <td><strong>Totaux</strong></td>
                                <td class="text-end"><strong><?= number_format(array_sum(array_column($payouts, 'total_transferts_recus')), 0, ',', ' ') ?></strong></td>
                                <td class="text-end"><strong><?= number_format(array_sum(array_column($payouts, 'total_montants_recus')), 2, ',', ' ') ?></strong></td>
                                <td class="text-end"><strong><?= number_format(array_sum(array_column($payouts, 'total_frais_percus')), 2, ',', ' ') ?></strong></td>
                                <td class="text-end"><strong><?= number_format(array_sum(array_column($payouts, 'commission_due')), 2, ',', ' ') ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Aucun transfert inter-opérateur pour le moment.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>