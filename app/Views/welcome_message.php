<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Système de Gestion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Système de Gestion des Opérations</h1>
        <p class="text-muted">Application de gestion des opérations financières (Mobile Money)</p>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Opérateurs</h5>
                        <p class="card-text">Gérer les opérateurs et leurs préfixes</p>
                        <a href="/operators" class="btn btn-primary">Accéder</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Types d'opérations</h5>
                        <p class="card-text">Gérer les types d'opérations et leurs barèmes</p>
                        <a href="/operation-types" class="btn btn-primary">Accéder</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Situation des gains</h5>
                        <p class="card-text">Voir les frais collectés par type d'opération</p>
                        <a href="/reports/fees-summary" class="btn btn-info">Voir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Comptes clients</h5>
                        <p class="card-text">Voir la situation de tous les comptes clients</p>
                        <a href="/reports/accounts-summary" class="btn btn-info">Voir</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>