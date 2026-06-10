<?php $__env->startSection('title','roles et permissions'); ?>
<?php $__env->startSection('content'); ?>
<div class="p-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Formulaire Role -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">
                <?php echo e(isset($role) ? 'Modifier un rôle' : 'Créer un rôle'); ?>

            </h2>

            <?php if(isset($role)): ?>
                <form method="POST" action="<?php echo e(route('admin.ui.roles.update', $role->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
            <?php else: ?>
                <form method="POST" action="<?php echo e(route('admin.ui.roles.store')); ?>">
                    <?php echo csrf_field(); ?>
            <?php endif; ?>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">
                        Nom du rôle
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="<?php echo e(old('name', $role->name ?? '')); ?>"
                        class="w-full border rounded-md px-3 py-2"
                        placeholder="Ex: Administrateur">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">
                        Permissions
                    </label>

                    <select
                        name="permissions[]"
                        multiple
                        class="w-full border rounded-md p-2 h-48">

                        <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option
                                value="<?php echo e($permission->name); ?>"
                                <?php if(
                                    isset($role)
                                    && $role->permissions->contains('name', $permission->name)
                                ): ?>
                                    selected
                                <?php endif; ?>
                            >
                                <?php echo e($permission->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>
                </div>

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <?php echo e(isset($role) ? 'Mettre à jour' : 'Créer le rôle'); ?>

                </button>

            </form>
        </div>

        <!-- Liste des rôles -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">
                Liste des rôles
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-3">ID</th>
                            <th class="border px-4 py-3">Rôle</th>
                            <th class="border px-4 py-3">Permissions</th>
                            <th class="border px-4 py-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="border px-4 py-3">
                                    <?php echo e($role->id); ?>

                                </td>

                                <td class="border px-4 py-3 font-semibold">
                                    <?php echo e(strtoupper($role->name)); ?>

                                </td>

                                <td class="border px-4 py-3">
                                    <?php $__currentLoopData = $role->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="inline-flex px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full mr-1 mb-1">
                                            <?php echo e($permission->name); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>

                                <td class="border px-4 py-3">
                                    <div class="flex gap-2">

                                        <a
                                            href="<?php echo e(route('admin.ui.roles', ['edit' => $role->id])); ?>"
                                            class="bg-yellow-500 text-white px-3 py-1 rounded">
                                            Modifier
                                        </a>

                                        <form
                                            method="POST"
                                            action="<?php echo e(route('admin.ui.roles.destroy', $role->id)); ?>"
                                            onsubmit="return confirm('Supprimer ce rôle ?')">

                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>

                                            <button
                                                type="submit"
                                                class="bg-red-600 text-white px-3 py-1 rounded">
                                                Supprimer
                                            </button>

                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    Aucun rôle trouvé
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>

    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Formulaire Permission -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">
                <?php echo e(isset($permission) ? 'Modifier une permission' : 'Créer permission'); ?>

            </h2>

            <?php if(isset($permission)): ?>
                <form method="POST" action="<?php echo e(route('admin.ui.permissions.update', $permission->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
            <?php else: ?>
                <form method="POST" action="<?php echo e(route('admin.ui.permissions.store')); ?>">
                    <?php echo csrf_field(); ?>
            <?php endif; ?>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">
                        Permission :
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="<?php echo e(old('name', $permission->name ?? '')); ?>"
                        class="w-full border rounded-md px-3 py-2"
                        placeholder="Ex: create">
                </div>

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <?php echo e(isset($permission) ? 'Mettre à jour' : 'Créer permission'); ?>

                </button>

            </form>
        </div>

        <!-- Liste des rôles -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">
                Liste des permission
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-3">ID</th>
                            <th class="border px-4 py-3">Permissions</th>
                            <th class="border px-4 py-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="border px-4 py-3">
                                    <?php echo e($p->id); ?>

                                </td>

                                <td class="border px-4 py-3 font-semibold">
                                    <?php echo e(strtoupper($p->name)); ?>

                                </td>

                                <td class="border px-4 py-3">
                                    <div class="flex gap-2">

                                        <a
                                            href="<?php echo e(route('admin.ui.role_permissions', ['editp' => $p->id])); ?>"
                                            class="bg-yellow-500 text-white px-3 py-1 rounded">
                                            Modifier
                                        </a>

                                        <form
                                            method="POST"
                                            action="<?php echo e(route('admin.ui.permission.destroy', $p->id)); ?>"
                                            onsubmit="return confirm('Supprimer cette permission ?')">

                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>

                                            <button
                                                type="submit"
                                                class="bg-red-600 text-white px-3 py-1 rounded">
                                                Supprimer
                                            </button>

                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    Aucune permission trouvé
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xamp2\htdocs\prj\pfe-laravel\it-support-system\resources\views/admin/users/roles.blade.php ENDPATH**/ ?>