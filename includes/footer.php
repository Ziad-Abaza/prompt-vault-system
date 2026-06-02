        </main>

        <footer class="bg-white border-t border-slate-100 py-10">
            <div class="max-w-6xl mx-auto px-4 md:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center space-x-6 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        <a href="about.php" class="hover:text-primary-600 transition-colors">About</a>
                        <a href="privacy.php" class="hover:text-primary-600 transition-colors">Privacy</a>
                        <a href="terms.php" class="hover:text-primary-600 transition-colors">Terms</a>
                    </div>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">
                        &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> &bull; Part of the <a href="https://egyitech.com" class="hover:text-primary-600">EgyiTech</a> Ecosystem
                    </p>
                </div>
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

        // Toggle prompt save status via AJAX
        function toggleSave(promptId, btnElement) {
            const formData = new FormData();
            formData.append('prompt_id', promptId);

            fetch('ajax_save_prompt.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const svg = btnElement.querySelector('svg');
                    if (data.state === 'saved') {
                        btnElement.classList.add('text-red-500', 'bg-red-50');
                        btnElement.classList.remove('text-slate-400');
                        svg.setAttribute('fill', 'currentColor');
                        btnElement.title = 'Unsave Prompt';
                    } else {
                        btnElement.classList.remove('text-red-500', 'bg-red-50');
                        btnElement.classList.add('text-slate-400');
                        svg.setAttribute('fill', 'none');
                        btnElement.title = 'Save to My Library';
                    }
                } else if (data.error === 'auth_required') {
                    window.location.href = 'login.php';
                }
            })
            .catch(err => console.error('Save error:', err));
        }

        // Global copy to clipboard with feedback
        function copyToClipboard(text, btnElement, promptId = null) {
            navigator.clipboard.writeText(text).then(() => {
                // Track copy if promptId provided
                if (promptId) {
                    fetch('track_copy.php?id=' + promptId);
                }
                
                const originalHtml = btnElement ? btnElement.innerHTML : null;
                if (btnElement) {
                    btnElement.innerHTML = '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                    setTimeout(() => {
                        btnElement.innerHTML = originalHtml;
                    }, 2000);
                }
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
</body>
</html>
