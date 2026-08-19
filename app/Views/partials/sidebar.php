<?php
$menu = [
    ['texto' => 'Dashboard', 'icono' => 'bi-speedometer2', 'url' => 'dashboard', 'permiso' => null],
    ['texto' => 'Caja', 'icono' => 'bi-cash-coin', 'url' => 'caja', 'permiso' => null],
    
    // ← REPORTES: ahora con submenú
    [
        'texto'   => 'Reportes',
        'icono'   => 'bi-bar-chart',
        'permiso' => 'reportes.ver',
        'submenu' => [
            ['texto' => 'Cobros', 'url' => 'reportes/cobros'],
        ],
    ],
    
    [
        'texto'   => 'Catálogos',
        'icono'   => 'bi-journal-bookmark',
        'permiso' => 'catalogos.ver',
        'submenu' => [
			['texto' => 'Cuentas SAP', 'url' => 'catalogos/sap'],
            ['texto' => 'Cuentas', 'url' => 'catalogos/cuentas'],
            ['texto' => 'Partidas', 'url' => 'catalogos/partidas'],
            ['texto' => 'Conceptos', 'url' => 'catalogos/conceptos'],
            ['texto' => 'Colonias', 'url' => 'catalogos/colonias'],
            ['texto' => 'Vialidades', 'url' => 'catalogos/vialidades'],
            ['texto' => 'Programas', 'url' => 'catalogos/programas'],
        ],
    ],
    ['texto' => 'Obras', 'icono' => 'bi-building', 'url' => 'obras', 'permiso' => 'obras.ver'],
    ['texto' => 'Vecinos', 'icono' => 'bi-people', 'url' => 'vecinos', 'permiso' => 'vecinos.ver'],
    ['texto' => 'Usuarios', 'icono' => 'bi-person-gear', 'url' => 'usuarios', 'permiso' => 'usuarios.administrar'],
    ['texto' => 'Roles y Permisos', 'icono' => 'bi-shield-lock', 'url' => 'roles', 'permiso' => 'roles.administrar'],
];

$rutaActual = uri_string();
?>
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="<?= site_url('dashboard') ?>" class="brand-link">
            <img src="<?= site_url('assets/adminlte/img/AdminLTELogo.png') ?>" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light">SICOMU</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" data-accordion="false" id="navigation">
                <?php foreach ($menu as $item): ?>
                    <?php
                        if ($item['permiso'] !== null && ! puede($item['permiso'])) {
                            continue;
                        }
                    ?>
                    <?php if (isset($item['submenu'])): ?>
                        <?php
                            $submenuActivo = false;
                            foreach ($item['submenu'] as $sub) {
                                if (str_starts_with($rutaActual, $sub['url'])) {
                                    $submenuActivo = true;
                                    break;
                                }
                            }
                        ?>
                        <li class="nav-item <?= $submenuActivo ? 'menu-open' : '' ?>">
                            <a href="#" class="nav-link <?= $submenuActivo ? 'active' : '' ?>">
                                <i class="nav-icon bi <?= $item['icono'] ?>"></i>
                                <p>
                                    <?= esc($item['texto']) ?>
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <?php foreach ($item['submenu'] as $sub): ?>
                                    <?php $activo = str_starts_with($rutaActual, $sub['url']) ? 'active' : ''; ?>
                                    <li class="nav-item">
                                        <a href="<?= site_url($sub['url']) ?>" class="nav-link <?= $activo ?>">
                                            <i class="nav-icon bi bi-circle"></i>
                                            <p><?= esc($sub['texto']) ?></p>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <?php $activo = str_starts_with($rutaActual, $item['url']) ? 'active' : ''; ?>
                        <li class="nav-item">
                            <a href="<?= site_url($item['url']) ?>" class="nav-link <?= $activo ?>">
                                <i class="nav-icon bi <?= $item['icono'] ?>"></i>
                                <p><?= esc($item['texto']) ?></p>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</aside>