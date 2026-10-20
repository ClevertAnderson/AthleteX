@extends('layouts.app')

@section('content')

<div class="container-fluid p-0">
    <div class="row g-0">
        
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 p-3 md:min-h-screen" style="background-color: #dbead5; border-right: 1px solid #c4d79b;">
            @include('admin.partials.sidebar')
        </div>

        <div class="col-md-9 col-lg-10 p-4" style="background-color: #EBF1DE;">
            
            <div class="card-header fw-bold fst-italic border-bottom border-secondary mb-4 p-2" style="background-color: #bfbfbf; font-family: 'Courier New';">
                ADMIN > USERS & SECURITY
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Create New Coach User Section -->
            <div class="card shadow-sm border border-secondary rounded-0 mb-4">
                <div class="card-header bg-info text-white fw-bold p-3">
                    <i class="fas fa-user-plus"></i> Create New Coach User Account
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.createCoachUser') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">Coach Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                                    placeholder="e.g., John Doe" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                    placeholder="coach@example.com" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                    placeholder="Min. 8 characters" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label fw-bold">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" 
                                    placeholder="Confirm password" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="coach_sport" class="form-label fw-bold">Sport Event <span class="text-danger">*</span></label>
                                <select id="coach_sport" name="coach_sport" class="form-select @error('coach_sport') is-invalid @enderror" required>
                                    <option value="">-- Select a Sport --</option>
                                    <?php foreach ($sports as$sport): ?>
                                        <option value="<?= $sport->name ?>" <?= old('coach_sport') == $sport->name ? 'selected' : '' ?>>
                                            <?= $sport->name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success fw-bold">
                                <i class="fas fa-plus"></i> Create Coach User Account
                            </button>
                        </div>
                    </form>

                    @if($errors->any())
                        <div class="alert alert-danger mt-3" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <strong>Error creating user:</strong>
                            <ul class="mb-0">
                                <?php foreach ($errors->all() as$error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Existing Users Table -->
            <div class="card shadow-sm border border-secondary rounded-0">
                <div class="card-header bg-secondary text-white fw-bold p-3 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-users"></i> Manage User Security Rights & Accounts</span>
                </div>
                <div class="card-body p-0">
                    <form action="{{ route('admin.updateUserPermissions') }}" method="POST" id="permissionsForm">
                    @csrf
    
                    <div class="table-responsive overflow-x-auto w-full">
                        <table class="table table-bordered table-hover text-center mb-0 align-middle text-nowrap" style="font-size: 0.85rem;">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="bg-light align-middle" style="width: 15%;">USER DETAILS</th>
                                    <th colspan="10" class="text-white" style="background-color: #4F6228;">SECURITY RIGHTS (Click to Change)</th>
                                    <th rowspan="2" class="bg-light align-middle" style="width: 10%;">ACTION</th>
                                </tr>
                                <tr style="background-color: #C4D79B;">
                                    <th>Admin</th><th>Athletes</th><th>Coaches</th><th>Sched</th><th>Achieve</th><th>Classes</th><th>Exams</th><th>Trans</th><th>Notifs</th><th>Dash</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($users) && count($users) > 0): ?>
                                    <?php foreach ($users as$user): ?>
                                    <tr>
                                        <td class="text-start bg-white">
                                            <div class="fw-bold text-dark"><?= $user->email ?></div>
                                            <div class="text-muted small"><?= $user->name ?></div>
                                            <?php if($user->role === 'coach'): ?>
                                                <span class="badge bg-info text-dark">Coach</span>
                                            <?php elseif($user->role === 'admin'): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <?php
                                            $modules = ['admin', 'athletes', 'coaches', 'scheduling', 'achievements', 'classes', 'exams', 'transactions', 'notifications', 'dashboard'];
                                        ?>
                                        
                                        <?php foreach ($modules as$module): ?>
                                        <?php
                                            $perm = $user->permissions[$module] ?? 'Hidden';
                                            if ($perm == 'Edit') {
                                                $btnClass = 'btn-success text-white';$btnText = 'Edit';
                                            } elseif ($perm == 'View') {
                                                $btnClass = 'btn-info text-dark';$btnText = 'View';
                                            } else {
                                                $btnClass = 'btn-secondary text-white';$btnText = 'Hide';
                                            }
                                        ?>
                                        <td class="p-2 align-middle" style="min-width: 70px;">
                                            <input type="hidden" name="permissions[<?= $user->id ?>][<?= $module ?>]" id="input_<?= $user->id ?>_<?= $module ?>" value="<?= $perm ?>">
                                            
                                            <button type="button" class="btn btn-sm w-100 fw-bold permission-toggle <?= $btnClass ?> shadow-sm"
                                                    data-user-id="<?= $user->id ?>"
                                                    data-module="<?= $module ?>"
                                                    data-current-state="<?= $perm ?>"
                                                    style="border-radius: 20px; font-size: 0.75rem; padding: 0.25rem 0; transition: all 0.2s ease-in-out;">
                                                <?= $btnText ?>
                                            </button>
                                        </td>
                                        <?php endforeach; ?>
                                        
                                        <!-- DELETE / REMOVE USER ACTION -->
                                        <td class="bg-white">
                                            <?php if($user->id !== auth()->id()): ?>
                                                <button type="button" class="btn btn-danger btn-sm px-2 py-1 text-white fw-bold shadow-sm" onclick="confirmDelete('<?= route('admin.deleteUser', $user->id) ?>', '<?=$user->name ?>')">
                                                    <i class="fas fa-trash-alt"></i> Del
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small fst-italic">Current</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="12" class="text-center text-muted p-4">No users found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                        <!-- Pagination Links Container -->
                        <div>
                            @if (method_exists($users, 'links'))
                                {{ $users->links('pagination::bootstrap-5') }}
                            @endif
                        </div>
                        
                        <button type="submit" class="btn btn-success fw-bold shadow-sm"><i class="fas fa-save"></i> Save Security Rights</button>
                    </div>
                </form>

                    <!-- Hidden Delete Form -->
                    <form id="deleteUserForm" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
            
            <div class="mt-3 text-muted small bg-white p-3 border rounded shadow-sm d-inline-block">
                <i class="fas fa-info-circle text-primary me-2"></i><strong>How to change permissions:</strong> Click the badge inside the table to cycle between states.
                <br>
                <span class="badge bg-secondary mt-2 me-2" style="border-radius: 20px;">Hide</span> = Module disappears from menu. <br>
                <span class="badge bg-info text-dark mt-1 me-2" style="border-radius: 20px;">View</span> = Read-only access. <br>
                <span class="badge bg-success mt-1 me-2" style="border-radius: 20px;">Edit</span> = Can add, edit, or delete data.
            </div>
            
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.permission-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                let currentState = this.getAttribute('data-current-state');
                let userId = this.getAttribute('data-user-id');
                let module = this.getAttribute('data-module');
                let inputField = document.getElementById(`input_${userId}_${module}`);

                let newState, newClass, newText;

                if (currentState === 'Hidden' || currentState === '') {
                    newState = 'View';
                    newClass = 'btn-info text-dark';
                    newText = 'View';
                } else if (currentState === 'View') {
                    newState = 'Edit';
                    newClass = 'btn-success text-white';
                    newText = 'Edit';
                } else {
                    newState = 'Hidden';
                    newClass = 'btn-secondary text-white';
                    newText = 'Hide';
                }

                this.setAttribute('data-current-state', newState);
                this.className = `btn btn-sm w-100 fw-bold permission-toggle shadow-sm ${newClass}`;
                this.innerText = newText;
                inputField.value = newState;
            });
        });
    });

    function confirmDelete(deleteUrl, userName) {
        if (confirm('Are you sure you want to remove user "' + userName + '"? This action cannot be undone.')) {
            var form = document.getElementById('deleteUserForm');
            form.action = deleteUrl;
            form.submit();
        }
    }
</script>
@endsection