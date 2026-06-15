<script>
// Sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const isOpen = !sidebar.classList.contains('-translate-x-full');
    sidebar.classList.toggle('-translate-x-full', isOpen);
    overlay.classList.toggle('hidden', isOpen);
}

// Dark mode
document.getElementById('darkToggle')?.addEventListener('click', () => {
    document.documentElement.classList.toggle('dark');
    const isDark = document.documentElement.classList.contains('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    const settingsToggle = document.getElementById('settingsThemeToggle');
    if (settingsToggle) settingsToggle.checked = isDark;
});
(function() {
    const saved = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (saved === 'dark' || (!saved && prefersDark)) document.documentElement.classList.add('dark');
})();

// Dropdowns
function toggleDropdown(id) {
    const menu = document.getElementById(id);
    const isOpen = menu.classList.contains('open');
    document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
    if (!isOpen) menu.classList.add('open');
}
document.addEventListener('click', (e) => {
    if (!e.target.closest('[onclick^="toggleDropdown"]') && !e.target.closest('.dropdown-menu')) {
        document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
    }
});

// Command Palette
function openCmdPalette() {
    const overlay = document.getElementById('cmdOverlay');
    const palette = document.getElementById('cmdPalette');
    overlay.classList.remove('pointer-events-none');
    overlay.style.opacity = '1';
    palette.classList.remove('pointer-events-none', 'opacity-0', 'scale-95');
    palette.style.opacity = '1';
    setTimeout(() => document.getElementById('cmdInput')?.focus(), 50);
}
function closeCmdPalette() {
    const overlay = document.getElementById('cmdOverlay');
    const palette = document.getElementById('cmdPalette');
    overlay.style.opacity = '0';
    palette.style.opacity = '0';
    setTimeout(() => {
        overlay.classList.add('pointer-events-none');
        palette.classList.add('pointer-events-none');
    }, 200);
}
document.addEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') { e.preventDefault(); openCmdPalette(); }
    if (e.key === 'Escape') closeCmdPalette();
});

// Polling unread notifications
(function(){
    async function fetchUnread() {
        try {
            const res = await fetch('<?php echo e(url("admin/ui/notifications/unread-count")); ?>', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const j = await res.json();
            const count = parseInt(j.unread || 0, 10);
            const badge = document.getElementById('notifBadge');
            const dot = document.getElementById('sidebarNotifDot');
            if (badge) {
                if (count > 0) { badge.textContent = count; badge.classList.remove('bg-transparent','text-transparent'); badge.classList.add('bg-red-500','text-white'); }
                else { badge.textContent = ''; badge.classList.add('bg-transparent','text-transparent'); }
            }
            if (dot) dot.style.display = count > 0 ? '' : 'none';
        } catch (e) {}
    }
    fetchUnread();
    setInterval(fetchUnread, 10000);
})();
</script>
<?php /**PATH C:\xamp2\htdocs\prj\pfe-laravel\it-support-system\resources\views/admin/layouts/partials/scripts.blade.php ENDPATH**/ ?>