<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un type d'opération</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Ajouter un type d'opération</h1>
        <p class="text-muted">Créez un nouveau type d'opération avec ses barèmes de frais</p>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/operation-types/store" method="post">
            <?= csrf_field() ?>

            <!-- Type d'opération -->
            <div class="card mb-4">
                <div class="card-header"><strong>Type d'opération</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="code" class="form-label">Code interne</label>
                        <select name="code" id="code" class="form-select" required>
                            <option value="" disabled <?= old('code') ? '' : 'selected' ?>>-- Choisir un type --</option>
                            <option value="DEPOSIT"    <?= in_array('DEPOSIT', $existingCodes) ? 'disabled' : '' ?> <?= old('code') === 'DEPOSIT' ? 'selected' : '' ?>>DÉPÔT <?= in_array('DEPOSIT', $existingCodes) ? '(déjà créé)' : '' ?></option>
                            <option value="WITHDRAWAL" <?= in_array('WITHDRAWAL', $existingCodes) ? 'disabled' : '' ?> <?= old('code') === 'WITHDRAWAL' ? 'selected' : '' ?>>RETRAIT <?= in_array('WITHDRAWAL', $existingCodes) ? '(déjà créé)' : '' ?></option>
                            <option value="TRANSFER"   <?= in_array('TRANSFER', $existingCodes) ? 'disabled' : '' ?> <?= old('code') === 'TRANSFER' ? 'selected' : '' ?>>TRANSFERT <?= in_array('TRANSFER', $existingCodes) ? '(déjà créé)' : '' ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="label" class="form-label">Libellé</label>
                        <input type="text" name="label" id="label" class="form-control" value="<?= old('label') ?>" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="has_fees" id="has_fees" class="form-check-input" value="1" checked>
                        <label for="has_fees" class="form-check-label">Ce type d'opération applique des frais</label>
                    </div>
                </div>
            </div>

            <!-- Barèmes de frais -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Barèmes de frais par tranche de montant</strong>
                    <button type="button" class="btn btn-sm btn-success" onclick="addFeeRow()">+ Ajouter une tranche</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="fee-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Montant min (Ar)</th>
                                    <th>Montant max (Ar)</th>
                                    <th>Frais fixes (Ar)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="fee-rows">
                                <!-- Lignes ajoutées via JS -->
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted small mb-0">Laissez les barèmes vides si ce type d'opération n'a pas de frais.</p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/operation-types" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <script>
    // Auto-complétion du libellé
    const labels = {
        'DEPOSIT': 'Dépôt',
        'WITHDRAWAL': 'Retrait',
        'TRANSFER': 'Transfert'
    };

    document.getElementById('code').addEventListener('change', function() {
        document.getElementById('label').value = labels[this.value] || '';
    });

    let feeRowIndex = 0;

    function addFeeRow(data = {}) {
        const tbody = document.getElementById('fee-rows');
        const tr = document.createElement('tr');
        tr.id = 'fee-row-' + feeRowIndex;
        tr.innerHTML = `
            <td><input type="number" name="min_amount[]" class="form-control form-control-sm" step="0.01" min="0" value="${data.min_amount || ''}" placeholder="0" required></td>
            <td><input type="number" name="max_amount[]" class="form-control form-control-sm" step="0.01" min="0" value="${data.max_amount || ''}" placeholder="999999999"></td>
            <td><input type="number" name="fee_fixed[]" class="form-control form-control-sm" step="0.01" min="0" value="${data.fee_fixed || ''}" placeholder="0"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeFeeRow(${feeRowIndex})">✕</button></td>
        `;
        tbody.appendChild(tr);
        feeRowIndex++;
    }

    function removeFeeRow(index) {
        const row = document.getElementById('fee-row-' + index);
        if (row) row.remove();
    }

    // Ajouter 2 lignes vides par défaut
    window.addEventListener('DOMContentLoaded', function() {
        const codeSelect = document.getElementById('code');
        if (codeSelect.value) {
            document.getElementById('label').value = labels[codeSelect.value] || '';
        }
        addFeeRow();
        addFeeRow();
    });
    </script>
</body>
</html>
