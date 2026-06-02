        </main>

        <footer class="bg-white border-t border-slate-100 py-10">
            <div class="max-w-6xl mx-auto px-4 md:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center space-x-6 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        <a href="<?php echo APP_URL_BASE; ?>/about.php" class="hover:text-primary-600 transition-colors">About</a>
                        <a href="<?php echo APP_URL_BASE; ?>/privacy.php" class="hover:text-primary-600 transition-colors">Privacy</a>
                        <a href="<?php echo APP_URL_BASE; ?>/terms.php" class="hover:text-primary-600 transition-colors">Terms</a>
                    </div>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">
                        &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> &bull; Part of the <a href="https://egyitech.com" class="hover:text-primary-600">EgyiTech</a> Ecosystem
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-24 right-8 z-[60] flex flex-col gap-3 pointer-events-none"></div>

    <script>
        /**
         * Global Toast System
         */
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-slate-900' : 'bg-red-600';
            const icon = type === 'success' 
                ? '<svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>'
                : '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>';

            toast.className = `flex items-center gap-3 px-6 py-4 rounded-2xl shadow-2xl text-white text-sm font-bold transform translate-y-10 opacity-0 transition-all duration-500 pointer-events-auto ${bgColor}`;
            toast.innerHTML = `
                <div class="shrink-0">${icon}</div>
                <div class="whitespace-nowrap">${message}</div>
            `;

            container.appendChild(toast);

            // Animate In
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });

            // Auto Remove
            setTimeout(() => {
                toast.classList.add('translate-y-[-20px]', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

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

        // Toggle prompt save status via AJAX
        function toggleSave(promptId, btnElement) {
            const formData = new FormData();
            formData.append('prompt_id', promptId);
            
            // Add CSRF token for security
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                formData.append('csrf_token', csrfToken);
            }

            fetch('<?php echo APP_URL_BASE; ?>/ajax_save_prompt.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const svg = btnElement.querySelector('svg');
                    const span = btnElement.querySelector('span');
                    if (data.state === 'saved') {
                        btnElement.classList.add('text-red-500', 'bg-red-50', 'border-red-100');
                        btnElement.classList.remove('text-slate-400');
                        svg.setAttribute('fill', 'currentColor');
                        btnElement.title = 'Unsave Prompt';
                        if (span) span.innerText = 'Saved';
                        showToast('Saved to your favorites');
                    } else {
                        btnElement.classList.remove('text-red-500', 'bg-red-50', 'border-red-100');
                        btnElement.classList.add('text-slate-400');
                        svg.setAttribute('fill', 'none');
                        btnElement.title = 'Save to My Library';
                        if (span) span.innerText = 'Save';
                        showToast('Removed from favorites');
                    }
                } else if (data.error === 'auth_required') {
                    window.location.href = '<?php echo APP_URL_BASE; ?>/login.php';
                }
            })
            .catch(err => console.error('Save error:', err));
        }

        // Global copy to clipboard with feedback
        function copyToClipboard(text, btnElement, promptId = null) {
            navigator.clipboard.writeText(text).then(() => {
                // Track copy if promptId provided
                if (promptId) {
                    fetch('<?php echo APP_URL_BASE; ?>/track_copy.php?id=' + promptId);
                }
                
                showToast('Prompt copied to clipboard');

                const originalHtml = btnElement ? btnElement.innerHTML : null;
                if (btnElement) {
                    btnElement.innerHTML = '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                    setTimeout(() => {
                        btnElement.innerHTML = originalHtml;
                    }, 2000);
                }
            }).catch(err => {
                showToast('Failed to copy', 'error');
                console.error('Failed to copy: ', err);
            });
        }
    </script>

    <!-- Back to Top Button -->
    <button id="backToTop" class="fixed bottom-8 right-8 z-50 p-4 bg-slate-900 text-white rounded-2xl shadow-2xl opacity-0 translate-y-10 invisible transition-all duration-500 hover:bg-primary-600 focus:outline-none group">
        <svg class="w-6 h-6 transform group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>

    <script>
        const backToTopBtn = document.getElementById('backToTop');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                backToTopBtn.classList.remove('opacity-0', 'translate-y-10', 'invisible');
                backToTopBtn.classList.add('opacity-100', 'translate-y-0', 'visible');
            } else {
                backToTopBtn.classList.add('opacity-0', 'translate-y-10', 'invisible');
                backToTopBtn.classList.remove('opacity-100', 'translate-y-0', 'visible');
            }
        });

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>
