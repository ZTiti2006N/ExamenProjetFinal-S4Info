<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un type d'opération</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Modifier un type d'opération</h1>
        <p class="text-muted">Modifiez le type d'opération et ses barèmes</p>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php
        // Codes déjà utilisés (sauf celui en cours)
        $otherModel = new \App\Models\OperationTypeModel();
        $allCodes = $otherModel->select('code')->where('id !=', $operationType['id'])->findAll();
        $usedCodes = array_column($allCodes, 'code');
        ?>

        <form action="/operation-types/update/<?= $operationType['id'] ?>" method="post">
            <?= csrf_field() ?>

            <!-- Type d'opération -->
            <div class="card mb-4">
                <div class="card-header"><strong>Type d'opération</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="code" class="form-label">Code interne</label>
                        <select name="code" id="code" class="form-select" required>
                            <option value="DEPOSIT"    <?= in_array('DEPOSIT', $usedCodes) ? 'disabled' : '' ?> <?= $operationType['code'] === 'DEPOSIT' ? 'selected' : '' ?>>DÉPÔT <?= in_array('DEPOSIT', $usedCodes) ? '(déjà utilisé)' : '' ?></option>
                            <option value="WITHDRAWAL" <?= in_array('WITHDRAWAL', $usedCodes) ? 'disabled' : '' ?> <?= $operationType['code'] === 'WITHDRAWAL' ? 'selected' : '' ?>>RETRAIT <?= in_array('WITHDRAWAL', $usedCodes) ? '(déjà utilisé)' : '' ?></option>
                            <option value="TRANSFER"   <?= in_array('TRANSFER', $usedCodes) ? 'disabled' : '' ?> <?= $operationType['code'] === 'TRANSFER' ? 'selected' : '' ?>>TRANSFERT <?= in_array('TRANSFER', $usedCodes) ? '(déjà utilisé)' : '' ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="label" class="form-label">Libellé</label>
                        <input type="text" name="label" id="label" class="form-control" value="<?= old('label', esc($operationType['label'])) ?>" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="has_fees" id="has_fees" class="form-check-input" value="1" <?= $operationType['has_fees'] ? 'checked' : '' ?>>
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
                                <!-- Les lignes existantes seront ajoutées via JS avec les données -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/operation-types" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <script>
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

    // Charger les barèmes existants au démarrage
    <?php if (isset($feeScales) && ! empty($feeScales)): ?>
    const existingFeeScales = <?= json_encode($feeScales) ?>;
    <?php else: ?>
    const existingFeeScales = [];
    <?php endif; ?>

    window.addEventListener('DOMContentLoaded', function() {
        // Auto-complétion du libellé
        const codeSelect = document.getElementById('code');
        document.getElementById('label').value = labels[codeSelect.value] || '';

        // Ajouter les barèmes existants
        if (existingFeeScales.length > 0) {
            existingFeeScales.forEach(function(fs) {
                addFeeRow({
                    min_amount: fs.min_amount,
                    max_amount: fs.max_amount,
                    fee_fixed: fs.fee_fixed
                });
            });
        } else {
            addFeeRow();
            addFeeRow();
        }
    });
    </script>
</body>
</html>