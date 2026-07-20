<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier opérateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Modifier l'opérateur</h1>
        <p class="text-muted">Modifiez les informations et commissions</p>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <div><?= esc($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="/operators/update/<?= $operator['id'] ?>" method="POST">
            <div class="card mb-4">
                <div class="card-header"><strong>Informations générales</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nom de l'opérateur</label>
                            <input type="text" class="form-control" name="name" value="<?= esc($operator['name']) ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Préfixe principal</label>
                            <input type="text" class="form-control" name="prefix" value="<?= esc($operator['prefix']) ?>" maxlength="10" required>
                            <div class="form-text">Préfixe principal (ex: 033)</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Autres préfixes valables</label>
                            <input type="text" class="form-control" name="other_prefixes" value="<?= esc($operator['other_prefixes'] ?? '') ?>" placeholder="032,031,038">
                            <div class="form-text">Préfixes séparés par des virgules (ex: 032,031,038)</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <strong>Commissions sur transferts vers d'autres opérateurs (%)</strong>
                </div>
                <div class="card-body" id="commissions-container">
                    <?php if (!empty($all_operators)): ?>
                        <?php 
                        $existingCommissions = [];
                        foreach ($commissions as $c) {
                            $existingCommissions[$c['target_operator_id']] = $c['commission_percent'];
                        }
                        $idx = 0;
                        foreach ($all_operators as $targetOp): 
                            $perc = $existingCommissions[$targetOp['id']] ?? '';
                        ?>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label">Commission pour transferts vers <strong><?= esc($targetOp['name']) ?></strong></label>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="hidden" name="target_operator_id[]" value="<?= $targetOp['id'] ?>">
                                    <input type="number" class="form-control" name="commission_percent[]" 
                                           value="<?= $perc ?>" min="0" max="100" step="0.01" placeholder="0.00">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <?php $idx++; endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">Aucun autre opérateur configuré.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="/operators" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</body>
</html>