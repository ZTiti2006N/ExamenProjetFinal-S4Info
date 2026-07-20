<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - MoneyFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 1.5rem;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #fff;
            margin: 0 auto 1rem;
        }
        .login-title {
            font-weight: 700;
            font-size: 1.5rem;
            color: #1e293b;
            text-align: center;
            margin-bottom: 0.25rem;
        }
        .login-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            text-align: center;
            margin-bottom: 2rem;
        }
        .form-label {
            font-weight: 500;
            font-size: 0.85rem;
            color: #475569;
        }
        .form-control {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
        .input-group-text {
            border-radius: 12px 0 0 12px;
            border: 2px solid #e2e8f0;
            border-right: none;
            background: #f8fafc;
            font-weight: 600;
            color: #475569;
        }
        .input-group .form-control {
            border-radius: 0 12px 12px 0;
        }
        .btn-login {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.85rem;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.2s;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(37,99,235,0.3);
        }
        .alert {
            border-radius: 12px;
            font-size: 0.85rem;
        }
        .info-text {
            font-size: 0.78rem;
            color: #94a3b8;
            text-align: center;
            margin-top: 1.5rem;
        }
        .info-text i {
            color: #3b82f6;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <i class="fas fa-bolt"></i>
            </div>
            <h1 class="login-title">MoneyFlow</h1>
            <p class="login-subtitle">Connectez-vous avec votre numéro de téléphone</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <form action="/auth/authenticate" method="POST">
                <div class="mb-3">
                    <label for="phone" class="form-label">Numéro de téléphone</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               placeholder="032 12 345 67" maxlength="10" 
                               value="<?= old('phone') ?>" required autofocus>
                    </div>
                    <div class="form-text">
                        <i class="fas fa-info-circle"></i> Saisissez votre numéro à 10 chiffres (ex: 032 12 345 67)
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-arrow-right-to-bracket me-2"></i>Se connecter
                </button>
            </form>

            <div class="info-text">
                <i class="fas fa-shield-alt me-1"></i>
                La connexion est automatique. Tout numéro valide sera reconnu.
            </div>
        </div>
    </div>
</body>
</html>