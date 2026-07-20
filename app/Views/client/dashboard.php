<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace - MoneyFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f4f6f9; }
        .wrapper { display: flex; width: 100%; min-height: 100vh; }
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-header .logo-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .sidebar-header .brand-text { font-weight: 700; font-size: 1.1rem; margin: 0; }
        .sidebar-header .brand-sub { font-size: 0.7rem; opacity: 0.6; }
        .sidebar .nav { padding: 0.75rem 0; flex: 1; }
        .sidebar .nav-item { width: 100%; }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.65);
            padding: 0.7rem 1.25rem;
            display: flex; align-items: center; gap: 12px;
            font-size: 0.88rem; font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff; background: rgba(255,255,255,0.06);
            border-left-color: #3b82f6;
        }
        .sidebar .nav-link i { width: 20px; text-align: center; font-size: 1rem; }
        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding: 1rem 1.25rem;
        }
        .sidebar-footer a {
            color: rgba(255,255,255,0.65);
            text-decoration: none; font-size: 0.85rem;
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-footer a:hover { color: #fff; }

        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
        }

        .top-navbar {
            background: #fff;
            padding: 0.85rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 999;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .top-navbar .page-title { font-weight: 600; font-size: 1.1rem; color: #1e293b; margin: 0; }
        .top-navbar .user-info { display: flex; align-items: center; gap: 12px; font-size: 0.85rem; color: #64748b; }
        .top-navbar .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 600; font-size: 0.85rem;
        }

        .content-area { padding: 1.5rem; }

        .balance-card {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border-radius: 20px; padding: 2rem; color: #fff;
            position: relative; overflow: hidden;
        }
        .balance-card::before {
            content: ''; position: absolute; top: -50%; right: -20%;
            width: 200px; height: 200px;
            background: rgba(59,130,246,0.15); border-radius: 50%;
        }
        .balance-card::after {
            content: ''; position: absolute; bottom: -30%; left: -10%;
            width: 150px; height: 150px;
            background: rgba(139,92,246,0.1); border-radius: 50%;
        }
        .balance-card .balance-label { font-size: 0.85rem; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px; }
        .balance-card .balance-amount { font-size: 2.5rem; font-weight: 700; margin: 5px 0; position: relative; z-index: 1; }
        .balance-card .balance-phone { font-size: 0.85rem; opacity: 0.6; position: relative; z-index: 1; }

        .action-card {
            background: #fff;
            border-radius: 16px; padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #eef2f6; height: 100%;
            transition: transform 0.2s;
        }
        .action-card:hover { transform: translateY(-2px); }
        .action-card .action-icon {
            width: 48px; height: 48px;
            border-radius: 14px; display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; margin-bottom: 0.75rem;
        }
        .action-card h6 { font-weight: 600; color: #1e293b; margin-bottom: 0.25rem; }
        .action-card p { font-size: 0.8rem; color: #94a3b8; margin-bottom: 1rem; }

        .form-control, .form-select {
            border-radius: 12px; border: 2px solid #e2e8f0;
            padding: 0.65rem 1rem; font-size: 0.9rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        }
        .btn-action {
            border-radius: 12px; padding: 0.65rem 1.5rem;
            font-weight: 600; font-size: 0.9rem; border: none; width: 100%;
            transition: all 0.2s;
        }
        .btn-deposit { background: #10b981; color: #fff; }
        .btn-deposit:hover { background: #059669; }
        .btn-withdrawal { background: #ef4444; color: #fff; }
        .btn-withdrawal:hover { background: #dc2626; }
        .btn-transfer { background: #3b82f6; color: #fff; }
        .btn-transfer:hover { background: #2563eb; }

        .activity-item {
            display: flex; align-items: center; gap: 12px;
            padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        .alert { border-radius: 12px; font-size: 0.85rem; }

        .modal-content { border-radius: 16px; border: none; }
        .modal-header { border-bottom: 1px solid #f1f5f9; padding: 1.25rem 1.5rem; }
        .modal-body { padding: 1.5rem; }
        .modal-footer { border-top: 1px solid #f1f5f9; padding: 1rem 1.5rem; }

        @media (max-width: 768px) {
            .sidebar { width: 0; overflow: hidden; }
            .main-content { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-icon"><i class="fas fa-bolt"></i></div>
                <div>
                    <div class="brand-text">MoneyFlow</div>
                    <div class="brand-sub">Espace Client</div>
                </div>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="/client"><i class="fas fa-home"></i> Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/client/history"><i class="fas fa-history"></i> Historique</a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="/logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </aside>

        <!-- MAIN -->
        <div class="main-content">
            <nav class="top-navbar">
                <h5 class="page-title"><i class="fas fa-home me-2 text-primary"></i>Mon Espace</h5>
                <div class="user-info">
                    <i class="fas fa-phone-alt text-muted"></i>
                    <span><?= esc($phone) ?></span>
                    <div class="user-avatar"><?= strtoupper(substr($first_name, 0, 1)) ?></div>
                </div>
            </nav>

            <div class="content-area">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success d-flex align-items-center gap-2"><i class="fas fa-check-circle"></i> <?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2"><i class="fas fa-exclamation-circle"></i> <?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="balance-card">
                            <div class="balance-label"><i class="fas fa-wallet me-2"></i>Solde actuel</div>
                            <div class="balance-amount"><?= number_format($balance, 0, ',', ' ') ?> <small style="font-size:1rem;font-weight:400;">Ar</small></div>
                            <div class="balance-phone"><i class="fas fa-user me-1"></i> <?= esc($first_name . ' ' . $last_name) ?> · <?= esc($phone) ?></div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="action-card">
                            <div class="action-icon" style="background:#d1fae5;color:#059669;"><i class="fas fa-arrow-down"></i></div>
                            <h6>Dépôt</h6>
                            <p>Ajouter de l'argent sur votre compte</p>
                            <button class="btn-action btn-deposit" data-bs-toggle="modal" data-bs-target="#depositModal">
                                <i class="fas fa-plus-circle me-1"></i> Faire un dépôt
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="action-card">
                            <div class="action-icon" style="background:#fce7f3;color:#db2777;"><i class="fas fa-arrow-up"></i></div>
                            <h6>Retrait</h6>
                            <p>Retirer de l'argent de votre compte</p>
                            <button class="btn-action btn-withdrawal" data-bs-toggle="modal" data-bs-target="#withdrawalModal">
                                <i class="fas fa-minus-circle me-1"></i> Faire un retrait
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="action-card">
                            <div class="action-icon" style="background:#dbeafe;color:#2563eb;"><i class="fas fa-users"></i></div>
                            <h6>Transfert multiple</h6>
                            <p>Envoyer à plusieurs destinataires</p>
                            <button class="btn-action btn-transfer" data-bs-toggle="modal" data-bs-target="#transferModal">
                                <i class="fas fa-paper-plane me-1"></i> Effectuer un transfert
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="action-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="fas fa-clock me-2 text-primary"></i>Dernières opérations</h6>
                                <a href="/client/history" class="btn btn-sm btn-outline-primary rounded-pill">Voir tout</a>
                            </div>
                            <?php if (!empty($history)): ?>
                                <?php foreach ($history as $op): ?>
                                <div class="activity-item">
                                    <div class="activity-icon" style="background:
                                        <?= strpos($op['type'], 'Dépôt') !== false ? '#d1fae5;color:#059669' : (strpos($op['type'], 'Retrait') !== false ? '#fce7f3;color:#db2777' : '#dbeafe;color:#2563eb') ?>;">
                                        <i class="fas <?= strpos($op['type'], 'Dépôt') !== false ? 'fa-arrow-down' : (strpos($op['type'], 'Retrait') !== false ? 'fa-arrow-up' : 'fa-exchange-alt') ?>"></i>
                                    </div>
                                    <div style="flex:1;">
                                        <strong><?= esc($op['type']) ?></strong>
                                        <span style="color:#1e293b;font-weight:600;"><?= number_format($op['amount'], 0, ',', ' ') ?> Ar</span>
                                        <?php if ($op['fee_amount'] > 0): ?>
                                            <span style="color:#dc2626;font-size:0.8rem;">(frais: <?= number_format($op['fee_amount'], 0, ',', ' ') ?> Ar)</span>
                                        <?php endif; ?>
                                        <?php if (!empty($op['recipient_phone'])): ?>
                                            <span style="color:#64748b;font-size:0.8rem;">→ <?= esc($op['recipient_phone']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($op['include_fees'])): ?>
                                            <span class="badge bg-info" style="font-size:0.7rem;">Frais inclus</span>
                                        <?php endif; ?>
                                        <div style="font-size:0.75rem;color:#94a3b8;">
                                            Solde: <?= number_format($op['balance_after'], 0, ',', ' ') ?> Ar · <?= date('d/m/Y H:i', strtotime($op['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted mb-0 text-center py-3">
                                    <i class="fas fa-inbox me-2"></i>Aucune opération pour le moment.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DÉPÔT -->
    <div class="modal fade" id="depositModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/client/deposit" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-arrow-down text-success me-2"></i>Faire un dépôt</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Montant du dépôt (Ar)</label>
                            <div class="input-group">
                                <span class="input-group-text">Ar</span>
                                <input type="number" class="form-control" name="amount" min="100" step="100" required placeholder="Ex: 5000">
                            </div>
                            <div class="form-text">Minimum : 100 Ar</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success rounded-pill"><i class="fas fa-check me-1"></i>Confirmer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL RETRAIT (avec option inclure frais) -->
    <div class="modal fade" id="withdrawalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/client/withdrawal" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-arrow-up text-danger me-2"></i>Faire un retrait</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Montant du retrait (Ar)</label>
                            <div class="input-group">
                                <span class="input-group-text">Ar</span>
                                <input type="number" class="form-control" name="amount" min="100" step="100" required placeholder="Ex: 5000">
                            </div>
                            <div class="form-text">Des frais peuvent s'appliquer selon le barème.</div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_fees" id="includeFees" value="1">
                                <label class="form-check-label" for="includeFees">
                                    <strong>Inclure les frais dans le montant</strong>
                                    <br><small class="text-muted">Si coché, le montant saisi inclut les frais.</small>
                                </label>
                            </div>
                        </div>
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-info-circle me-1"></i> Solde actuel : <strong><?= number_format($balance, 0, ',', ' ') ?> Ar</strong>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger rounded-pill"><i class="fas fa-check me-1"></i>Confirmer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL TRANSFERT MULTIPLE -->
    <div class="modal fade" id="transferModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/client/transfer" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-exchange-alt text-primary me-2"></i>Transfert multiple</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">
                                Destinataires 
                                <small class="text-muted">(le montant total sera divisé)</small>
                            </label>
                            <div id="recipientsContainer">
                                <div class="input-group mb-2">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" class="form-control" name="recipient_phones[]" maxlength="10" required placeholder="0331234567">
                                    <button type="button" class="btn btn-outline-success" onclick="addRecipient()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-text">Ajoutez plusieurs numéros pour diviser le montant.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Montant total (Ar)</label>
                            <div class="input-group">
                                <span class="input-group-text">Ar</span>
                                <input type="number" class="form-control" name="amount" id="transferAmount" min="100" step="100" required placeholder="Ex: 10000">
                            </div>
                            <div class="form-text" id="splitInfo">Montant par destinataire : <strong>—</strong></div>
                        </div>
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-info-circle me-1"></i> Solde actuel : <strong><?= number_format($balance, 0, ',', ' ') ?> Ar</strong>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary rounded-pill"><i class="fas fa-paper-plane me-1"></i>Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function addRecipient() {
        const container = document.getElementById('recipientsContainer');
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <span class="input-group-text"><i class="fas fa-phone"></i></span>
            <input type="tel" class="form-control" name="recipient_phones[]" maxlength="10" required placeholder="0331234567">
            <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove(); updateSplitInfo();">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(div);
        updateSplitInfo();
    }

    function updateSplitInfo() {
        const inputs = document.querySelectorAll('input[name="recipient_phones[]"]');
        const count = inputs.length;
        const total = parseFloat(document.getElementById('transferAmount').value) || 0;
        const splitEl = document.getElementById('splitInfo');
        if (count > 0 && total > 0) {
            splitEl.innerHTML = 'Montant par destinataire : <strong>' + (total / count).toLocaleString('fr-FR') + ' Ar</strong>';
        } else {
            splitEl.innerHTML = 'Montant par destinataire : <strong>—</strong>';
        }
    }

    document.getElementById('transferAmount')?.addEventListener('input', updateSplitInfo);
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les champs existants
        document.querySelectorAll('input[name="recipient_phones[]"]').forEach(function(el) {
            el.addEventListener('input', updateSplitInfo);
        });
    });
    </script>
</body>
</html>