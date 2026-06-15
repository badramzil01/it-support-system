<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI IT SUPPORT</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 overflow-hidden">

<div class="flex h-screen">

    
    <?php echo $__env->make('parts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="flex-1 flex flex-col overflow-hidden">

        
        <header class="bg-white shadow px-8 py-4 flex justify-between items-center">

            <div>

                <h2 class="text-2xl font-bold text-gray-800">

                    <?php if(request()->is('admin/*')): ?>
                        Dashboard Administrateur

                    <?php elseif(request()->is('equipeIT/*')): ?>
                        Dashboard Support

                    <?php else: ?>
                        Dashboard
                    <?php endif; ?>

                </h2>

            </div>

            <div class="flex items-center gap-4">

                <div class="text-right">

                    <p class="font-semibold">
                        <?php echo e(auth()->user()->name); ?>

                    </p>

                    <p class="text-sm text-gray-500">

                        <?php if(request()->is('admin/*')): ?>
                            ADMIN

                        <?php elseif(request()->is('equipeIT/*')): ?>
                            SUPPORT

                        <?php else: ?>
                            <?php echo e(strtoupper(auth()->user()->getRoleNames()->first() ?? 'USER')); ?>


                        <?php endif; ?>

                    </p>

                </div>

                <div
                    class="w-12 h-12 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-lg">

                    <?php echo e(strtoupper(substr(auth()->user()->name,0,1))); ?>


                </div>

            </div>

        </header>

        
        <main class="flex-1 overflow-y-auto p-8">

            <?php echo $__env->yieldContent('content'); ?>

        </main>

    </div>

</div>

</body>

</html><?php /**PATH C:\xamp2\htdocs\prj\pfe-laravel\it-support-system\resources\views/parts/base.blade.php ENDPATH**/ ?>