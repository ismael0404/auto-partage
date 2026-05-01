<?php
// auth/login.php
require_once __DIR__ . '/../includes/header.php';
redirectIfConnected();
?>

<div class="auth-container">
    <div class="auth-image-section">
        <h2><i class="fa-solid fa-car"></i> AutoPartage</h2>
        <h1 class="mt-4">Bienvenue !</h1>
        <p class="mt-2" style="color: var(--gray-300);">Connectez-vous Ã  votre compte pour continuer</p>
        <img src="/Projet_Auto/assets/images/connexion.jfif" alt="Connexion" class="mt-5" style="max-width: 90%; border-radius: var(--border-radius); filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3));">
    </div>
    
    <div class="auth-form-section">
        <h2 class="mb-4">Connexion</h2>
        
        <form action="/Projet_Auto/auth/login_process.php" method="POST">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Entrez votre email" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Entrez votre mot de passe" required>
                    <button type="button" onclick="togglePassword('password')" style="position: absolute; right: 10px; top: 12px; background: none; border: none; cursor: pointer; color: var(--gray-500);">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <label class="d-flex align-items-center" style="gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="remember"> Se souvenir de moi
                </label>
                <a href="#" style="font-size: 0.875rem;">Mot de passe oubliÃ© ?</a>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>
        
        <p class="text-center mt-4">
            Vous n'avez pas de compte ? <a href="/Projet_Auto/auth/register.php" style="font-weight: 600;">S'inscrire</a>
        </p>
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

