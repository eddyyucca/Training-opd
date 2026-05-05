<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Organization and People Development Training' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
</head>
<body>
<div class="wrapper">
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <aside class="main-sidebar" id="main-sidebar">
        <div class="sidebar-head">
            <a href="{{ route('dashboard') }}" class="brand-link">
                <span class="brand-mark">OP</span>
                <div>
                    <div class="brand-text">Organization and People Development</div>
                    <div class="brand-subtext">Training management</div>
                </div>
            </a>
            <div class="sidebar-user-card">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'User' }}</div>
                <div class="sidebar-user-role">{{ auth()->user()?->isOpd() ? 'Organization and People Development' : (auth()->user()?->department?->name ?: 'Department User') }}</div>
            </div>
        </div>

        <nav>
            <ul class="nav-sidebar">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <span>Employees</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('trainings.index') }}" class="nav-link {{ request()->routeIs('trainings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-book-reader"></i>
                        <span>Training</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.departments') }}" class="nav-link {{ request()->routeIs('reports.departments') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-sitemap"></i>
                        <span>Reports</span>
                    </a>
                </li>
                @if (auth()->user()?->isOpd())
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('masters.index') }}" class="nav-link {{ request()->routeIs('masters.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-sliders-h"></i>
                            <span>Master Data</span>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </aside>

    <div class="app-shell">
        <header class="main-header">
            <div class="main-header-bar">
                <button type="button" class="sidebar-toggle-btn lg:hidden" id="sidebar-toggle" aria-label="Open sidebar">
                    <span class="sidebar-toggle-icon" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                    <span>Menu</span>
                </button>
              
                <div class="ml-auto flex items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <div class="text-sm font-extrabold text-slate-900">{{ auth()->user()->name ?? 'User' }}</div>
                        <div class="text-xs text-slate-500">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-light btn-sm">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="page-hero">
                <div>
                    <h1 class="page-title">{{ $header ?? 'Dashboard' }}</h1>
                    @if (!empty($subtitle))
                        <p class="page-subtitle">{{ $subtitle }}</p>
                    @endif
                </div>
                @hasSection('page_actions')
                    <div class="page-meta">
                        @yield('page_actions')
                    </div>
                @endif
            </div>

            <section class="page-shell">
                @if ($errors->any())
                    <div class="alert alert-danger mt-4">
                        <strong>The submitted data is invalid.</strong>
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </section>
        </main>

        <footer class="main-footer">
            <div class="footer-grid">
                <div>
                    <strong class="block text-slate-900">Organization and People Development</strong>
                    <div>Internal training management system.</div>
                </div>
                <div class="footer-meta">
                    <span class="footer-chip"><i class="fas fa-user-shield"></i> {{ auth()->user()?->isOpd() ? 'Organization and People Development' : (auth()->user()?->department?->name ?: 'Department User') }}</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<div class="toast-wrap">
    @if (session('success'))
        <div class="toast show" id="success-toast">
            <div class="toast-header">
                <strong class="mr-auto">Success</strong>
                <button type="button" class="close" data-toast-close>&times;</button>
            </div>
            <div class="toast-body">{{ session('success') }}</div>
        </div>
    @endif
</div>

<div class="modal-shell" id="confirm-modal">
    <div class="modal-panel">
        <div class="content-card-label">Confirmation</div>
        <h3 class="card-title mb-2">Continue with this action?</h3>
        <p class="text-muted mb-4" id="confirm-modal-text">This change will be applied.</p>
        <div class="form-actions border-0 p-0 m-0">
            <button type="button" class="btn btn-default" id="confirm-cancel">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirm-submit">Yes, continue</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    (() => {
        const sidebar = document.getElementById('main-sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarBackdrop = document.getElementById('sidebar-backdrop');
        const confirmModal = document.getElementById('confirm-modal');
        const confirmText = document.getElementById('confirm-modal-text');
        const confirmSubmit = document.getElementById('confirm-submit');
        const confirmCancel = document.getElementById('confirm-cancel');
        let pendingForm = null;

        const closeSidebar = () => {
            sidebar?.classList.remove('is-open');
            sidebarBackdrop?.classList.remove('is-open');
        };

        const openSidebar = () => {
            sidebar?.classList.add('is-open');
            sidebarBackdrop?.classList.add('is-open');
        };

        sidebarToggle?.addEventListener('click', () => {
            if (sidebar?.classList.contains('is-open')) {
                closeSidebar();
                return;
            }

            openSidebar();
        });

        sidebarBackdrop?.addEventListener('click', closeSidebar);

        $('select').each(function () {
            const $select = $(this);

            if ($select.hasClass('no-enhance')) {
                return;
            }

            $select.select2({
                width: '100%',
                placeholder: $select.find('option:first').text() || 'Select an option',
                allowClear: ! $select.prop('multiple'),
            });
        });

        document.querySelectorAll('form[data-confirm]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                pendingForm = form;
                confirmText.textContent = form.dataset.confirmMessage || 'This change will be applied.';
                confirmModal.classList.add('is-open');
            });
        });

        confirmCancel?.addEventListener('click', () => {
            pendingForm = null;
            confirmModal.classList.remove('is-open');
        });

        confirmModal?.addEventListener('click', (event) => {
            if (event.target === confirmModal) {
                pendingForm = null;
                confirmModal.classList.remove('is-open');
            }
        });

        confirmSubmit?.addEventListener('click', () => {
            if (pendingForm) {
                pendingForm.submit();
            }
        });

        const toast = document.getElementById('success-toast');

        if (toast) {
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3500);
        }

        document.querySelectorAll('[data-toast-close]').forEach((button) => {
            button.addEventListener('click', () => {
                toast?.classList.remove('show');
            });
        });
    })();
</script>
@stack('scripts')
</body>
</html>
