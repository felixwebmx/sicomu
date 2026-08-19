<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
				<a class="nav-link" href="#" data-lte-toggle="fullscreen" aria-label="Toggle fullscreen">
					<i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
					<i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none"></i>
				</a>
            </li>
			<li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-person-circle"></i>
                    <?= esc(session()->get('nombre_completo')) ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <span class="dropdown-item-text text-muted small">
                            Rol: <?= esc(implode(', ', session()->get('roles') ?? [])) ?>
                        </span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('logout') ?>">
                            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>