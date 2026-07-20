i<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique - MoneyFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f9;
        }
        .wrapper { display: flex; width: 100%; min-height: 100vh; }

        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-header .logo-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .sidebar-header .brand-text { font-weight: 700; font-size: 1.1rem; margin: 0; }
        .sidebar-header .brand-sub { font-size: 0.7rem; opacity: 0.6; }
        .sidebar .nav { padding: 0.75rem 0; flex: 1; }
        .sidebar .nav-item { width: 100%; }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.65);
            padding: 0.7rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.06);
            border-left-color: #3b82f6;
        }
        .sidebar .nav-link i { width: 20px; text-align: center; font-size: 1rem; }
        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding: 1rem 1.25rem;
        }
        .sidebar-footer a {
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
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
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .top-navbar .page-title { font-weight: 600; font-size: 1.1rem; color: #1e293b; margin: 0; }
        .top-navbar .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.85rem;
            color: #64748b;
        }
        .top-navbar .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .content-area { padding: 1.5rem; }

        .card-custom {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #eef2f6;
            overflow: hidden;
        }
        .card-custom .card-header-custom {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 600;
            color: #1e293b;
        }

        .table {
            margin-bottom: 0;
        }
        .table th {
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #f1f5f9;
            padding: 0.75rem 1rem;
        }
        .table td {
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        .table tr:last-child td { border-bottom: none; }
        .table tr:hover { background: #f8fafc; }
        .type-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .amount-positive { color: #059669; font-weight: 600; }
        .amount-negative { color: #dc2626; font-weight: 600; }

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
                    <a class="nav-link" href="/client">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="/client/history">
                        <i class="fas fa-history"></i> Historique
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="/logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </aside>

        <!-- MAIN -->
        <div class="main-content">
            <!-- Navbar -->
            <nav class="top-navbar">
                <h5 class="page-title"><i class="fas fa-history me-2 text-primary"></i>Historique des opérations</h5>
                <div class="user-info">
                    <i class="fas fa-phone-alt text-muted"></i>
                    <span><?= esc($phone) ?></span>
                    <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
                </div>
            </nav>

            <!-- Content -->
            <div class="content-area">
                <div class="card-custom">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-list me-2 text-primary"></i>Toutes les opérations</span>
                        <a href="/client" class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="fas fa-arrow-left me-1"></i>Retour
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Montant</th>
                                    <th>Frais</th>
                                    <th>Destinataire</th>
                                    <th>Solde avant</th>
                                    <th>Solde après</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($history)): ?>
                                    <?php foreach ($history as $op): ?>
                                    <?php
                                        $isDeposit = strpos($op['type'], 'Dépôt') !== false;
                                        $isWithdrawal = strpos($op['type'], 'Retrait') !== false;
                                        $isTransfer = strpos($op['type'], 'Transfert') !== false;
                                        $bgType = $isDeposit ? '#d1fae5;color:#059669' : ($isWithdrawal ? '#fce7f3;color:#db2777' : '#dbeafe;color:#2563eb');
                                        $iconType = $isDeposit ? 'fa-arrow-down' : ($isWithdrawal ? 'fa-arrow-up' : 'fa-exchange-alt');
                                    ?>
                                    <tr>
                                        <td style="white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($op['created_at'])) ?></td>
                                        <td>
                                            <span class="type-badge" style="background:<?= $bgType ?>;">
                                                <i class="fas <?= $iconType ?> me-1"></i><?= esc($op['type']) ?>
                                            </span>
                                        </td>
                                        <td class="<?= $isDeposit ? 'amount-positive' : 'amount-negative' ?>">
                                            <?= ($isDeposit ? '+' : '-') . number_format($op['amount'], 0, ',', ' ') ?> Ar
                                        </td>
                                        <td>
                                            <?php if ($op['fee_amount'] > 0): ?>
                                                <span style="color:#dc2626;"><?= number_format($op['fee_amount'], 0, ',', ' ') ?> Ar</span>
                                            <?php else: ?>
                                                <span style="color:#94a3b8;">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= $op['recipient_phone'] ? esc($op['recipient_phone']) : '<span style="color:#94a3b8;">—</span>' ?>
                                        </td>
                                        <td><?= number_format($op['balance_before'], 0, ',', ' ') ?> Ar</td>
                                        <td><strong><?= number_format($op['balance_after'], 0, ',', ' ') ?> Ar</strong></td>
                                        <td>
                                            <span class="badge <?= $op['status'] === 'completed' ? 'bg-success' : 'bg-warning' ?> rounded-pill">
                                                <?= $op['status'] === 'completed' ? 'Complété' : ucfirst($op['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5" style="color:#94a3b8;">
                                            <i class="fas fa-inbox mb-2" style="font-size:2rem;"></i><br>
                                            Aucune opération effectuée pour le moment.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>