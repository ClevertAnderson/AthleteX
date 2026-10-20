<aside id="sidebar-wrapper" class="fixed inset-y-0 left-0 z-50 w-64 h-screen bg-[#2e4e1f] text-white flex flex-col p-6 transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0 shadow-2xl">
    <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center mb-6 text-lg font-bold no-underline text-white">
        SPORTS OFFICE
    </a>

    <?php if(auth()->check()): ?>
        <?php
            $userRole = strtolower(trim(auth()->user()->role ?? ''));
        ?>
        
        <?php if($userRole === 'coach'): ?>
            <a href="<?php echo e(route('coach')); ?>" class="flex items-center mb-6 p-2 bg-[#3b5d28] rounded flex-shrink-0 hover:bg-[#446634] transition-colors text-white no-underline shadow-sm border border-transparent hover:border-green-600">
                <i class="bi bi-person-circle mr-2 text-2xl"></i>
                <span class="font-semibold text-sm truncate">
                    Coach: <?php echo e(auth()->user()->coach ? (auth()->user()->coach->coach_first_name . ' ' . auth()->user()->coach->coach_last_name) : auth()->user()->name); ?>

                </span>
            </a>
        <?php else: ?>
            <div class="flex items-center mb-6 p-2 bg-[#3b5d28] rounded flex-shrink-0">
                <i class="bi bi-person-circle mr-2 text-2xl"></i>
                <span class="font-semibold text-sm truncate">
                    <?php if($userRole === 'admin'): ?>
                        Admin: <?php echo e(auth()->user()->name); ?>

                    <?php else: ?>
                        <?php echo e(auth()->user()->name); ?>

                    <?php endif; ?>
                </span>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <nav class="flex-1 overflow-y-auto overflow-x-hidden pr-2 pb-10"> 
        <ul class="space-y-2 w-full flex flex-col min-h-full"> 

            <li>
                <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center px-3 py-2 rounded <?php echo e(request()->routeIs('dashboard') ? 'bg-[#446634]' : 'bg-[#3b5d28]'); ?> font-semibold hover:bg-[#446634] transition-colors text-white no-underline w-full">
                    <i class="bi bi-speedometer2 mr-2"></i> Dashboard
                </a>
            </li>

            <li>
                <a href="<?php echo e(route('athletes.index')); ?>" class="flex items-center px-3 py-2 rounded <?php echo e(request()->routeIs('athletes.index', 'athletes.show', 'student.athlete') ? 'bg-[#446634]' : 'bg-[#3b5d28]'); ?> font-semibold hover:bg-[#446634] transition-colors text-white no-underline w-full">
                    <i class="bi bi-person-walking mr-2"></i> Student–Athletes
                </a>
            </li>

            <?php
                $attendanceRoute = ($userRole === 'admin')
                    ? (Route::has('admin.attendance') ? route('admin.attendance') : '#')
                    : (Route::has('coach.attendance.index') ? route('coach.attendance.index') : '#');
            ?>
            <li>
                <a href="<?php echo e($attendanceRoute); ?>" class="flex items-center px-3 py-2 rounded <?php echo e(request()->is('*attendance*') ? 'bg-[#446634]' : 'bg-[#3b5d28]'); ?> font-semibold hover:bg-[#446634] transition-colors text-white no-underline w-full">
                    <i class="bi bi-calendar2-week mr-2"></i> Attendance
                </a>
            </li>

            <?php
                $reportsRoute = ($userRole === 'admin')
                    ? (Route::has('admin.reports') ? route('admin.reports') : '#')
                    : (Route::has('coach.reports.index') ? route('coach.reports.index') : '#');
            ?>
            <li>
                <a href="<?php echo e($reportsRoute); ?>" class="flex items-center px-3 py-2 rounded <?php echo e(request()->is('*reports*') ? 'bg-[#446634]' : 'bg-[#3b5d28]'); ?> font-semibold hover:bg-[#446634] transition-colors text-white no-underline w-full">
                    <i class="bi bi-journal-text mr-2"></i> Reports
                </a>
            </li>

            <?php if(auth()->check() && $userRole === 'admin'): ?>
                <li>
                    <a href="<?php echo e(route('coaches.index')); ?>" class="flex items-center px-3 py-2 rounded <?php echo e(request()->routeIs('coaches.index') ? 'bg-[#446634]' : 'bg-[#3b5d28]'); ?> font-semibold hover:bg-[#446634] transition-colors text-white no-underline w-full">
                        <i class="bi bi-people-fill mr-2"></i> Coaches
                    </a>
                </li>
            <?php endif; ?>
            
            <li>
                <a href="<?php echo e(route('achievements.index')); ?>" class="flex items-center px-3 py-2 rounded <?php echo e(request()->routeIs('achievements.index') ? 'bg-[#446634]' : 'bg-[#3b5d28]'); ?> font-semibold hover:bg-[#446634] transition-colors text-white no-underline w-full">
                    <i class="bi bi-trophy-fill mr-2 text-yellow-400"></i> Achievements
                </a>
            </li>

            <?php if(auth()->check() && $userRole === 'admin'): ?>
                <li>
                    <!-- UPDATED: route('admin.approvals.pending') -->
                    <a href="<?php echo e(route('admin.approvals.pending')); ?>" class="flex items-center px-3 py-2 rounded <?php echo e(request()->routeIs('admin.approvals.pending') ? 'bg-[#446634]' : 'bg-[#3b5d28]'); ?> font-semibold hover:bg-[#446634] transition-colors text-white no-underline w-full">
                        <i class="bi bi-bell-fill mr-2"></i> Athlete Approvals
                    </a>
                </li>

                <li>
                    <a href="<?php echo e(route('admin.blockchain')); ?>" class="flex items-center px-3 py-2 rounded <?php echo e(request()->routeIs('admin.blockchain') ? 'bg-[#446634]' : 'bg-[#3b5d28]'); ?> font-semibold hover:bg-[#446634] transition-colors text-white no-underline w-full">
                        <i class="bi bi-shield-lock-fill mr-2 text-green-400"></i> Security Ledger
                    </a>
                </li>

                <li>
                    <a href="<?php echo e(route('admin.general')); ?>" class="flex items-center px-3 py-2 rounded <?php echo e(request()->routeIs('admin.general') ? 'bg-[#446634]' : 'bg-[#3b5d28]'); ?> font-semibold hover:bg-[#446634] transition-colors text-white no-underline w-full">
                        <i class="bi bi-person-gear mr-2"></i> Admin
                    </a>
                </li>
            <?php endif; ?>

            <li class="mt-12 pb-16 relative" style="z-index: 100; pointer-events: auto;">
                <a href="#" 
                   onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();" 
                   class="flex justify-center items-center w-full px-4 py-3 rounded bg-red-600 active:bg-red-800 transition text-white font-bold shadow-md border border-red-700"
                   style="touch-action: manipulation; -webkit-tap-highlight-color: transparent;">
                    Logout
                </a>
                
                <form id="sidebar-logout-form" method="POST" action="<?php echo e(route('logout')); ?>" style="display: none;">
                    <?php echo csrf_field(); ?>
                </form>
            </li>

        </ul>
    </nav>
</aside><?php /**PATH D:\xampp\htdocs\AthleteX\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>