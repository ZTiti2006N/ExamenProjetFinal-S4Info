<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situation des comptes clients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Situation des comptes clients</h1>
        <p class="text-muted">Récapitulatif de tous les comptes clients et leurs activités</p>

        <?php if (isset($dbError)): ?>
            <div class="alert alert-warning"><?= esc($dbError) ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="/" class="btn btn-outline-primary"><i class="fas fa-home"></i> Accueil</a>
            <a href="/reports/fees-summary" class="btn btn-outline-info"><i class="fas fa-coins"></i> Situation des gains via les frais</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Client</th>
                    <th>Téléphone</th>
                    <th>Opérateur</th>
                    <th>Solde actuel (Ar)</th>
                    <th>Total dépôts (Ar)</th>
                    <th>Total retraits (Ar)</th>
                    <th>Transferts émis (Ar)</th>
                    <th>Transferts reçus (Ar)</th>
                    <th>Total opérations</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! empty($accountsSummary)): ?>
                    <?php foreach ($accountsSummary as $row): ?>
                    <tr>
                        <td><?= esc($row['nom'] . ' ' . $row['prenom']) ?></td>
                        <td><?= esc($row['telephone']) ?></td>
                        <td><?= esc($row['operateur']) ?></td>
                        <td class="text-end"><strong><?= number_format($row['solde_actuel'], 2, ',', ' ') ?></strong></td>
                        <td class="text-end"><?= number_format($row['total_depots'], 2, ',', ' ') ?></td>
                        <td class="text-end"><?= number_format($row['total_retraits'], 2, ',', ' ') ?></td>
                        <td class="text-end"><?= number_format($row['total_transferts_emis'], 2, ',', ' ') ?></td>
                        <td class="text-end"><?= number_format($row['total_transferts_recus'], 2, ',', ' ') ?></td>
                        <td class="text-end"><?= number_format($row['total_operations'], 0, ',', ' ') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Aucun client enregistré.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>