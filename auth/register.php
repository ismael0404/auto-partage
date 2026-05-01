<?php
// auth/register.php
require_once __DIR__ . '/../includes/header.php';
redirectIfConnected();
?>

<div class="auth-container">
    <div class="auth-form-section" style="max-width: 600px;">
        <h2 class="mb-4">CrÃ©er un compte</h2>
        
        <form action="/Projet_Auto/auth/register_process.php" method="POST">
            <div class="d-flex" style="gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">PrÃ©nom</label>
                    <input type="text" name="prenom" class="form-control" placeholder="Entrez votre prÃ©nom" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" placeholder="Entrez votre nom" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Entrez votre email" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">TÃ©lÃ©phone</label>
                <input type="tel" name="telephone" class="form-control" placeholder="Entrez votre numÃ©ro" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" class="form-control" placeholder="CrÃ©ez un mot de passe" required>
                    <button type="button" onclick="togglePassword('password')" style="position: absolute; right: 10px; top: 12px; background: none; border: none; cursor: pointer; color: var(--gray-500);">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirmer le mot de passe</label>
                <div style="position: relative;">
                    <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Confirmez votre mot de passe" required>
                    <button type="button" onclick="togglePassword('password_confirm')" style="position: absolute; right: 10px; top: 12px; background: none; border: none; cursor: pointer; color: var(--gray-500);">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block mt-4">S'inscrire</button>
        </form>
        
        <p class="text-center mt-4">
            Vous avez dÃ©jÃ  un compte ? <a href="/Projet_Auto/auth/login.php" style="font-weight: 600;">Se connecter</a>
        </p>
    </div>
    
    <div class="auth-image-section">
        <h2><i class="fa-solid fa-car"></i> AutoPartage</h2>
        <h1 class="mt-4">Rejoignez-nous !</h1>
        <p class="mt-2" style="color: var(--gray-300);">CrÃ©ez votre compte et commencez votre expÃ©rience d'autopartage</p>
        <img src="/Projet_Auto/assets/images/incription.jfif" alt="Inscription" class="mt-5" style="max-width: 90%; border-radius: var(--border-radius); filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3));">
    </div>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

