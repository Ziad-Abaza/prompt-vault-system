        </main>

        <footer class="bg-white border-t border-slate-100 py-8">
            <div class="max-w-6xl mx-auto px-4 md:px-8 text-center">
                <p class="text-slate-400 text-xs font-medium uppercase tracking-widest">
                    &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> &bull; Private & Secure
                </p>
            </div>
        </footer>
    </div>

    <!-- Vanilla JS for global functionality -->
    <script>
        // Mobile Menu Toggle
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const sidebar = document.getElementById('sidebar');
        
        if (mobileMenuToggle && sidebar) {
            mobileMenuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth < 768 && !sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            });
        }

        // Global copy to clipboard with feedback
        function copyToClipboard(text, btnElement) {
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = btnElement ? btnElement.innerHTML : null;
                if (btnElement) {
                    btnElement.innerHTML = '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                    setTimeout(() => {
                        btnElement.innerHTML = originalHtml;
                    }, 2000);
                } else {
                    alert('Copied to clipboard!');
                }
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
</body>
</html>
