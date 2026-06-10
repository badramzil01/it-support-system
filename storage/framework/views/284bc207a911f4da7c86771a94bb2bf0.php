<?php if (\Illuminate\Support\Facades\Blade::check('role', 'support')): ?>

<aside class="w-64 bg-indigo-900 text-white flex flex-col">

    <div class="p-6 border-b border-indigo-700">
        <h1 class="text-2xl font-bold">
            Support Team
        </h1>
    </div>

    <nav class="flex-1 p-4 space-y-6 overflow-y-auto">

        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">
                Vue générale
            </h3>

            <a href="<?php echo e(route('support.dashboard')); ?>"
               class="block px-4 py-3 rounded-lg bg-indigo-600 text-white">
                Dashboard
            </a>
        </div>

        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">
                Tickets
            </h3>

            <a href="<?php echo e(route('support.tickets.index')); ?>"
               class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                Mes tickets
            </a>

            <!-- Escalades removed: handled in Tickets page -->
        </div>

        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">
                Conversations
            </h3>

            <a href="<?php echo e(route('support.discussions.index')); ?>"
               class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                Conversations
            </a>
        </div>

        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">
                Knowledge Base
            </h3>

            <a href="<?php echo e(route('support.ui.knowledge.index')); ?>"
               class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                Base de connaissance
            </a>
        </div>

        <div>
            <h3 class="px-4 mb-2 text-xs font-semibold uppercase text-slate-400">
                Configuration
            </h3>

            <a href="<?php echo e(url('/equipeIT/ui/notifications')); ?>#"
               class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                Notifications
            </a>

            <a href="<?php echo e(route('support.settings.index')); ?>"
               class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                Paramètres
            </a>
        </div>

    </nav>

    <div class="p-4 border-t border-indigo-700">

        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>

            <button
                type="submit"
                class="w-full bg-red-500 hover:bg-red-600 py-2 rounded-lg">
                Déconnexion
            </button>

        </form>

    </div>

</aside>

<?php endif; ?><?php /**PATH C:\xamp2\htdocs\prj\pfe-laravel\it-support-system\resources\views/parts/sidebar.blade.php ENDPATH**/ ?>