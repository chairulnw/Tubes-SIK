    <footer class="main-footer">
        <div class="footer-container">
            <p>&copy; <?php echo date('Y'); ?> Klinik Sehat. Sistem Informasi Klinik.</p>
            <p>Sistem Manajemen Pasien & Rekam Medis</p>
        </div>
    </footer>

    <script>
        // Mobile navigation toggle
        document.addEventListener('DOMContentLoaded', function() {
            const navToggle = document.getElementById('navToggle');
            const navMenu = document.querySelector('.nav-menu');

            if (navToggle) {
                navToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                    navToggle.classList.toggle('active');
                });
            }
        });
    </script>
