</main>
<footer class="site-footer">
    <div class="footer-content">
        <p class="disclaimer">
            <strong>Academic Disclaimer:</strong> This website is for educational purposes only
            and serves as a requirement for a final project. It is not a commercial platform
            and processes no real payments.
        </p>
        <p class="group-info">
            Unbound &copy; <?= date('Y') ?>
        </p>
    </div>
</footer>
<script>
    (function () {
        const buttons = document.querySelectorAll('[data-theme-toggle]');
        const root = document.documentElement;

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                const nextTheme = root.getAttribute('data-theme') === 'unbound-light' ? 'unbound-dark' : 'unbound-light';
                root.setAttribute('data-theme', nextTheme);
                localStorage.setItem('unbound-theme', nextTheme);
            });
        });
    })();
</script>
</body>
</html>
