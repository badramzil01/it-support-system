<?php $__env->startSection('title','Paramètres'); ?>
<?php $__env->startSection('content'); ?>
<div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 max-w-3xl">
    <h4 class="font-semibold text-slate-900 dark:text-slate-100">Mon Compte</h4>
    <?php if(session('success')): ?>
        <div class="mt-3 px-3 py-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mt-3 px-3 py-2 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-300 text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('support.ui.settings.profile.update')); ?>" class="mt-4">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-medium text-slate-700 dark:text-slate-300">Nom</label>
                <input name="name" class="w-full border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" value="<?php echo e(old('name', auth()->user()->name)); ?>" />
            </div>
            <div>
                <label class="text-xs font-medium text-slate-700 dark:text-slate-300">Email</label>
                <input name="email" class="w-full border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" value="<?php echo e(old('email', auth()->user()->email)); ?>" />
            </div>
        </div>
        <div class="mt-4">
            <button class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition">Enregistrer</button>
        </div>
    </form>

    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
        <h4 class="font-semibold text-slate-900 dark:text-slate-100">Changer le mot de passe</h4>
        <form method="POST" action="<?php echo e(route('support.ui.settings.password.update')); ?>" class="mt-4 max-w-md">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-medium text-slate-700 dark:text-slate-300">Mot de passe actuel</label>
                    <input name="current_password" type="password" class="w-full border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-700 dark:text-slate-300">Nouveau mot de passe</label>
                    <input name="password" type="password" class="w-full border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-700 dark:text-slate-300">Confirmer le mot de passe</label>
                    <input name="password_confirmation" type="password" class="w-full border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
                </div>
                <div>
                    <button class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition">Mettre à jour le mot de passe</button>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
        <h4 class="font-semibold text-slate-900 dark:text-slate-100">Apparence</h4>
        <div class="mt-3 flex items-center gap-4">
            <label class="relative inline-flex items-center cursor-pointer">
                <input id="settingsThemeToggle" type="checkbox" class="sr-only peer" />
                <div class="w-11 h-6 bg-slate-300 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-500/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                <span class="ms-3 text-sm font-medium text-slate-700 dark:text-slate-300">Mode sombre</span>
            </label>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Basculer le thème de l'application (persisté en local)</p>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // initialize toggle according to localStorage
    (function(){
        const toggle = document.getElementById('settingsThemeToggle');
        const saved = localStorage.getItem('theme');
        const dark = (saved === 'dark') || (!saved && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (toggle) { toggle.checked = dark; }
        if (dark) document.documentElement.classList.add('dark');
        toggle?.addEventListener('change', (e) => {
            document.documentElement.classList.toggle('dark', e.target.checked);
            localStorage.setItem('theme', e.target.checked ? 'dark' : 'light');
        });
    })();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('support.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\badr\Desktop\support it\support-system\resources\views/support/settings/index.blade.php ENDPATH**/ ?>