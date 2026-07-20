<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Système de Gestion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f9;
            overflow-x: hidden;
        }
        .wrapper { display: flex; width: 100%; min-height: 100vh; }
        
        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
            transition: all 0.3s;
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
        .sidebar .nav-link .badge-nav {
            margin-left: auto;
            background: rgba(255,255,255,0.1);
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
        }
        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding: 1rem 1.25rem;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
        }
        
        /* ===== NAVBAR ===== */
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
        .top-navbar .page-title { font-weight: 600; font-size: 1.2rem; color: #1e293b; margin: 0; }
        .top-navbar .navbar-right { display: flex; align-items: center; gap: 16px; }
        .top-navbar .notification-icon {
            position: relative;
            width: 36px; height: 36px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            cursor: pointer;
            transition: 0.2s;
        }
        .top-navbar .notification-icon:hover { background: #e2e8f0; }
        .top-navbar .notification-dot {
            position: absolute;
            top: 4px; right: 4px;
            width: 8px; height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid #fff;
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
            cursor: pointer;
        }

        /* ===== CONTENT AREA ===== */
        .content-area { padding: 1.5rem; }

        /* ===== STAT CARDS ===== */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            border: 1px solid #eef2f6;
            cursor: pointer;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        .stat-card .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 0.75rem;
        }
        .stat-card .stat-label { font-size: 0.8rem; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-card .stat-value { font-size: 1.7rem; font-weight: 700; color: #1e293b; margin: 2px 0; }
        .stat-card .stat-detail { font-size: 0.78rem; color: #94a3b8; }

        /* ===== CHARTS ===== */
        .chart-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #eef2f6;
            height: 100%;
        }
        .chart-card .chart-title { font-size: 0.95rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem; }
        .chart-card .chart-title a { color: inherit; text-decoration: none; }
        .chart-card .chart-title a:hover { color: #3b82f6; }

        /* ===== NOTIFICATION DROPDOWN ===== */
        .notif-dropdown {
            display: none;
            position: absolute;
            top: 44px;
            right: 60px;
            width: 320px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border: 1px solid #e2e8f0;
            z-index: 1001;
            max-height: 320px;
            overflow-y: auto;
        }
        .notif-dropdown.show { display: block; }
        .notif-dropdown .notif-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
        }
        .notif-dropdown .notif-item {
            padding: 0.7rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.82rem;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .notif-dropdown .notif-item:last-child { border-bottom: none; }
        .notif-dropdown .notif-item .notif-icon {
            width: 28px; height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            flex-shrink: 0;
        }
        .notif-dropdown .notif-item .notif-text { line-height: 1.3; }
        .notif-dropdown .notif-item .notif-time { font-size: 0.7rem; color: #94a3b8; margin-top: 2px; }
        .notif-dropdown .notif-empty {
            padding: 2rem 1rem;
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar { width: 0; overflow: hidden; }
            .main-content { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- ===== SIDEBAR ===== -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-icon"><i class="fas fa-bolt"></i></div>
                <div>
                    <div class="brand-text">MoneyFlow</div>
                    <div class="brand-sub">Gestion des opérations</div>
                </div>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="/log_admin">
                        <i class="fas fa-chart-pie"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/operators">
                        <i class="fas fa-building"></i> Opérateurs
                        <span class="badge-nav"><?= $totalOperators ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/operation-types">
                        <i class="fas fa-exchange-alt"></i> Types d'opérations
                        <span class="badge-nav"><?= $totalOperationTypes ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/reports/fees-summary">
                        <i class="fas fa-coins"></i> Gains (frais)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/reports/accounts-summary">
                        <i class="fas fa-users"></i> Comptes clients
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <div style="display:flex; align-items:center; gap:10px; opacity:0.6; font-size:0.8rem;">
                    <i class="fas fa-circle" style="color:#22c55e; font-size:0.5rem;"></i>
                    Système actif
                </div>
            </div>
        </aside>

        <!-- ===== MAIN ===== -->
        <div class="main-content">
            <!-- Navbar -->
            <nav class="top-navbar">
                <h5 class="page-title"><i class="fas fa-chart-pie me-2 text-primary"></i>Tableau de bord</h5>
                <div class="navbar-right">
                    <div class="notification-icon" onclick="toggleNotif()">
                        <i class="fas fa-bell"></i>
                        <?php if (! empty($recentNotifications)): ?>
                            <span class="notification-dot"></span>
                        <?php endif; ?>
                    </div>
                    <div class="user-avatar">OP</div>
                </div>
            </nav>

            <!-- Notifications dropdown -->
            <div class="notif-dropdown" id="notifDropdown">
                <div class="notif-header">
                    <span>Notifications</span>
                    <span style="color:#94a3b8;font-size:0.75rem;"><?= count($recentNotifications) ?> nouvelle(s)</span>
                </div>
                <?php if (! empty($recentNotifications)): ?>
                    <?php foreach ($recentNotifications as $notif): ?>
                    <div class="notif-item">
                        <div class="notif-icon" style="background:#dbeafe;color:#2563eb;">
                            <?php
                            $iconType = 'fas fa-circle';
                            if (strpos($notif['type'], 'Dépôt') !== false) $iconType = 'fas fa-arrow-down';
                            elseif (strpos($notif['type'], 'Retrait') !== false) $iconType = 'fas fa-arrow-up';
                            elseif (strpos($notif['type'], 'Transfert') !== false) $iconType = 'fas fa-arrows-left-right';
                            ?>
                            <i class="<?= $iconType ?>"></i>
                        </div>
                        <div class="notif-text">
                            <strong><?= esc($notif['first_name'] . ' ' . $notif['last_name']) ?></strong> a effectué un <strong><?= esc($notif['type']) ?></strong> de <strong><?= number_format($notif['amount'], 0, ',', ' ') ?> Ar</strong>
                            <?php if ($notif['fee_amount'] > 0): ?>
                                <span style="color:#dc2626;">(frais: <?= number_format($notif['fee_amount'], 0, ',', ' ') ?> Ar)</span>
                            <?php endif; ?>
                            <div class="notif-time"><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="notif-empty">
                        <i class="fas fa-check-circle mb-2" style="font-size:1.5rem;color:#22c55e;"></i><br>
                        Aucune notification récente
                    </div>
                <?php endif; ?>
            </div>

            <!-- Content -->
            <div class="content-area">

                <!-- Stat Cards Row - 6 cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="stat-card" onclick="window.location='/operation-types'">
                            <div class="stat-icon" style="background:#ede9fe; color:#7c3aed;"><i class="fas fa-exchange-alt"></i></div>
                            <div class="stat-label">Opérations</div>
                            <div class="stat-value"><?= number_format($totalOperations, 0, ',', ' ') ?></div>
                            <div class="stat-detail">Total enregistrées</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="stat-card" onclick="window.location='/reports/accounts-summary'">
                            <div class="stat-icon" style="background:#dbeafe; color:#2563eb;"><i class="fas fa-users"></i></div>
                            <div class="stat-label">Clients</div>
                            <div class="stat-value"><?= number_format($totalClients, 0, ',', ' ') ?></div>
                            <div class="stat-detail">Inscrits</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="stat-card" onclick="window.location='/operators'">
                            <div class="stat-icon" style="background:#fef3c7; color:#d97706;"><i class="fas fa-building"></i></div>
                            <div class="stat-label">Opérateurs</div>
                            <div class="stat-value"><?= $totalOperators ?></div>
                            <div class="stat-detail">Configurés</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="stat-card" onclick="window.location='/operation-types'">
                            <div class="stat-icon" style="background:#dcfce7; color:#16a34a;"><i class="fas fa-tags"></i></div>
                            <div class="stat-label">Types Op.</div>
                            <div class="stat-value"><?= $totalOperationTypes ?></div>
                            <div class="stat-detail">Dépôt / Retrait / Transfert</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="stat-card" onclick="window.location='/operation-types'">
                            <div class="stat-icon" style="background:#fce7f3; color:#db2777;"><i class="fas fa-list"></i></div>
                            <div class="stat-label">Barèmes</div>
                            <div class="stat-value"><?= $totalFeeScales ?></div>
                            <div class="stat-detail">Tranches de frais</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="stat-card" onclick="window.location='/reports/fees-summary'">
                            <div class="stat-icon" style="background:#d1fae5; color:#059669;"><i class="fas fa-coins"></i></div>
                            <div class="stat-label">Gains frais</div>
                            <div class="stat-value"><?= number_format($totalFeesCollected, 0, ',', ' ') ?> <small style="font-size:0.7rem;">Ar</small></div>
                            <div class="stat-detail">Collectés — Cliquez pour détail</div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <div class="chart-card" onclick="window.location='/reports/fees-summary'" style="cursor:pointer;">
                            <div class="chart-title">
                                <i class="fas fa-chart-bar me-2 text-primary"></i>
                                <a href="/reports/fees-summary">Frais collectés par type d'opération</a>
                            </div>
                            <canvas id="feesChart" height="180"></canvas>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="chart-card">
                            <div class="chart-title"><i class="fas fa-chart-doughnut me-2 text-warning"></i>Opérations par type</div>
                            <canvas id="typesChart" height="180"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Activity card -->
                <div class="row g-3">
                    <div class="col-12">
                        <div class="chart-card">
                            <div class="chart-title d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-clock me-2 text-primary"></i>Activité récente</span>
                                <small style="color:#94a3b8;font-weight:400;">
                                    Dernière opération : <?= is_string($lastActivity) && $lastActivity !== 'Aucune opération' ? date('d/m/Y H:i', strtotime($lastActivity)) : $lastActivity ?>
                                </small>
                            </div>
                            <?php if (! empty($recentNotifications)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recentNotifications as $notif): ?>
                                    <div class="list-group-item d-flex align-items-center gap-3 px-0" style="border-color:#f1f5f9;">
                                        <div style="width:36px;height:36px;border-radius:10px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#475569;flex-shrink:0;">
                                            <?php
                                            $iconAct = 'fas fa-circle';
                                            if (strpos($notif['type'], 'Dépôt') !== false) $iconAct = 'fas fa-arrow-down text-success';
                                            elseif (strpos($notif['type'], 'Retrait') !== false) $iconAct = 'fas fa-arrow-up text-danger';
                                            elseif (strpos($notif['type'], 'Transfert') !== false) $iconAct = 'fas fa-arrows-left-right text-info';
                                            ?>
                                            <i class="<?= $iconAct ?>"></i>
                                        </div>
                                        <div style="flex:1;">
                                            <strong><?= esc($notif['first_name'] . ' ' . $notif['last_name']) ?></strong>
                                            <span style="color:#64748b;">— <?= esc($notif['type']) ?></span>
                                            <span style="color:#1e293b;font-weight:500;"><?= number_format($notif['amount'], 0, ',', ' ') ?> Ar</span>
                                            <?php if ($notif['fee_amount'] > 0): ?>
                                                <span style="color:#dc2626;font-size:0.8rem;">(+<?= number_format($notif['fee_amount'], 0, ',', ' ') ?> Ar frais)</span>
                                            <?php endif; ?>
                                        </div>
                                        <small style="color:#94a3b8;white-space:nowrap;"><?= date('d/m H:i', strtotime($notif['created_at'])) ?></small>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0"><i class="fas fa-info-circle me-2"></i>Aucune activité pour le moment. Les opérations effectuées par les clients apparaîtront ici.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
    function toggleNotif() {
        document.getElementById('notifDropdown').classList.toggle('show');
    }
    document.addEventListener('click', function(e) {
        const dd = document.getElementById('notifDropdown');
        if (!e.target.closest('.notification-icon') && !e.target.closest('.notif-dropdown')) {
            dd.classList.remove('show');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // ===== BAR CHART: Frais collectés - 3 barres fixes : Dépôt, Retrait, Transfert =====
        <?php
        $chartLabels = [];
        $chartData = [];
        foreach ($feesSummary as $row) {
            $chartLabels[] = esc($row['operation_label']);
            $chartData[] = (float)$row['total_fees_collected'];
        }
        // Ensure we always have 3 labels
        while (count($chartLabels) < 3) {
            $chartLabels[] = count($chartLabels) === 0 ? 'Dépôt' : (count($chartLabels) === 1 ? 'Retrait' : 'Transfert');
            $chartData[] = 0;
        }
        ?>
        new Chart(document.getElementById('feesChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [{
                    label: 'Frais collectés (Ar)',
                    data: <?= json_encode($chartData) ?>,
                    backgroundColor: ['#3b82f6', '#8b5cf6', '#10b981'],
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        ticks: {
                            stepSize: 100,
                            callback: function(v) { return v.toLocaleString() + ' Ar'; }
                        }
                    }
                }
            }
        });

        // ===== DOUGHNUT CHART: Opérations par type =====
        <?php
        $typeLabels2 = [];
        $typeCounts2 = [];
        $hasOperations = false;
        foreach ($operationTypes as $ot) {
            $cnt = (int)$ot['op_count'];
            if ($cnt > 0) {
                $hasOperations = true;
            }
            $typeLabels2[] = esc($ot['label']);
            $typeCounts2[] = $cnt;
        }
        // Si aucune opération, on affiche les types avec 0
        if (empty($typeLabels2)) {
            $typeLabels2 = ['Aucune opération'];
            $typeCounts2 = [1];
        }
        ?>
        new Chart(document.getElementById('typesChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($typeLabels2) ?>,
                datasets: [{
                    data: <?= json_encode($typeCounts2) ?>,
                    backgroundColor: ['#3b82f6', '#8b5cf6', '#10b981'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 12, usePointStyle: true, font: { size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                return label + ': ' + value + ' opération(s)';
                            }
                        }
                    }
                }
            }
        });
    });
    </script>
</body>
</html>