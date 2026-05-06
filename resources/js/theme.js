(function() {
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    
    // Verificar tema guardado
    const storedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = storedTheme ? storedTheme === 'dark' : prefersDark;
    
    if (isDark) {
        body.classList.add('dark');
    }
    
    // Toggle tema
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const isDarkNow = body.classList.toggle('dark');
            localStorage.setItem('theme', isDarkNow ? 'dark' : 'light');
        });
    }
})();