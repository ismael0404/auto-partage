<?php $base = BASE_URL; ?>
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="logo" style="color:#fff;margin-bottom:16px">
                        <span class="icon" style="background:#fff;color:#111">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                        </span> AutoPartage
                    </div>
                    <p>© <?= date('Y') ?> AutoPartage. Tous droits réservés.</p>
                </div>
                <div>
                    <h4>Liens utiles</h4>
                    <a href="<?= $base ?>/index.php">Accueil</a><br>
                    <a href="<?= $base ?>/client/vehicles.php">Véhicules</a><br>
                    <a href="#">Conditions</a><br>
                    <a href="#">Confidentialité</a>
                </div>
                <div>
                    <h4>Service client</h4>
                    <a href="#">Contact</a><br>
                    <a href="#">FAQ</a><br>
                    <a href="#">Assistance</a>
                </div>
                <div>
                    <h4>Nous suivre</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-x-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© <?= date('Y') ?> AutoPartage - Tous droits réservés</p>
            </div>
        </div>
    </footer>
    <script src="<?= $base ?>/assets/js/app.js"></script>
</body>
</html>
