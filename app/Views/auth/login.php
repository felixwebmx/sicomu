<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SICOMU | Iniciar Sesión</title>

    <?= csrf_meta() ?>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/vendor/bootstrap-icons/bootstrap-icons.css') ?>">
    <!-- OverlayScrollbars -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/vendor/overlayscrollbars/overlayscrollbars.min.css') ?>">
    <!-- AdminLTE v4 core -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte.css') ?>">
</head>
<body class="login-page bg-body-secondary">

<div class="login-box">
    <div class="login-logo">
        <a href="<?= site_url('/') ?>"><b>SICOMU</b></a>
        <p class="text-muted small mb-0">Sistema Integral de Cobros Municipales de Uriangato</p>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Inicie sesión para continuar</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger py-2">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errores')): ?>
                <div class="alert alert-danger py-2">
                    <ul class="mb-0 ps-3">
                        <?php foreach (session()->getFlashdata('errores') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('login') ?>" method="post">
                <?= csrf_field() ?>

                <div class="input-group mb-3">
                    <input type="text"
                           name="nombre_usuario"
                           class="form-control"
                           placeholder="Usuario"
                           value="<?= esc(old('nombre_usuario')) ?>"
                           autofocus
                           required>
                    <div class="input-group-text">
                        <i class="bi bi-person"></i>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="Contraseña"
                           required>
                    <div class="input-group-text">
                        <i class="bi bi-lock-fill"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary d-block w-100">
                            Iniciar sesión
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="<?= base_url('assets/adminlte/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- OverlayScrollbars JS -->
<script src="<?= base_url('assets/adminlte/vendor/overlayscrollbars/overlayscrollbars.browser.es6.min.js') ?>"></script>
<!-- AdminLTE v4 core -->
<script src="<?= base_url('assets/adminlte/js/adminlte.js') ?>"></script>

</body>
</html>