<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Redirección raíz al login
$routes->get('/', static function () {
    return redirect()->to('/login');
});

// ═══════════════════════════════════════════════════════════════
// RUTAS PÚBLICAS (sin autenticación)
// ═══════════════════════════════════════════════════════════════
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('logout', 'AuthController::logout');

// ═══════════════════════════════════════════════════════════════
// RUTAS PROTEGIDAS (requieren sesión activa)
// ═══════════════════════════════════════════════════════════════
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // ─── Dashboard ───
    $routes->get('dashboard', 'DashboardController::index');

    // ═══════════════════════════════════════════════════════════
    // MÓDULO: USUARIOS (permiso: usuarios.administrar)
    // ═══════════════════════════════════════════════════════════
    $routes->group('usuarios', ['filter' => 'permission:usuarios.administrar'], function ($routes) {
        $routes->get('/', 'UsuariosController::index');
        $routes->get('nuevo', 'UsuariosController::formulario');
        $routes->get('editar/(:num)', 'UsuariosController::formulario/$1');
        $routes->post('guardar', 'UsuariosController::guardar');
        $routes->post('guardar/(:num)', 'UsuariosController::guardar/$1');
        $routes->post('eliminar/(:num)', 'UsuariosController::eliminar/$1');
        $routes->post('desbloquear/(:num)', 'UsuariosController::desbloquear/$1');
    });

    // ═══════════════════════════════════════════════════════════
    // MÓDULO: ROLES Y PERMISOS (permiso: roles.administrar)
    // ═══════════════════════════════════════════════════════════
    $routes->group('roles', ['filter' => 'permission:roles.administrar'], function ($routes) {
        $routes->get('/', 'RolesController::index');
        $routes->get('nuevo', 'RolesController::formulario');
        $routes->get('editar/(:num)', 'RolesController::formulario/$1');
        $routes->post('guardar', 'RolesController::guardar');
        $routes->post('guardar/(:num)', 'RolesController::guardar/$1');
        $routes->post('eliminar/(:num)', 'RolesController::eliminar/$1');
    });

    // ═══════════════════════════════════════════════════════════
    // CATÁLOGOS: SOLO LECTURA (permiso: catalogos.ver)
    // ═══════════════════════════════════════════════════════════
    $routes->group('catalogos', ['filter' => 'permission:catalogos.ver'], function ($routes) {
        $routes->get('sap', 'Catalogos\CuentaSapController::index');
        $routes->get('cuentas', 'Catalogos\CuentasController::index');
        $routes->get('partidas', 'Catalogos\PartidasController::index');
        $routes->get('conceptos', 'Catalogos\ConceptosController::index');
        // Endpoint AJAX para select en cascada de conceptos
        $routes->get('ajax/partidas-por-cuenta/(:num)', 'Catalogos\PartidasController::porCuentaJson/$1');
		// Colonias y Vialidades (lectura)
        $routes->get('colonias', 'Catalogos\ColoniasController::index');
        $routes->get('vialidades', 'Catalogos\VialidadesController::index');
        // Endpoint AJAX para vialidades por colonia
        $routes->get('ajax/vialidades-por-colonia/(:num)', 'Catalogos\VialidadesController::porColoniaJson/$1');
		$routes->get('programas', 'Catalogos\ProgramaController::index');
		
    });

    // ═══════════════════════════════════════════════════════════
    // CATÁLOGOS: ESCRITURA (permiso: catalogos.administrar)
    // ═══════════════════════════════════════════════════════════
    $routes->group('catalogos', ['filter' => 'permission:catalogos.administrar'], function ($routes) {
        // ─ Códigos SAP ─
        $routes->get('sap', 'Catalogos\CuentaSapController::index');
        $routes->get('sap/nuevo', 'Catalogos\CuentaSapController::formulario');
        $routes->get('sap/editar/(:num)', 'Catalogos\CuentaSapController::formulario/$1');
        $routes->post('sap/guardar', 'Catalogos\CuentaSapController::guardar');
        $routes->post('sap/guardar/(:num)', 'Catalogos\CuentaSapController::guardar/$1');
        $routes->post('sap/eliminar/(:num)', 'Catalogos\CuentaSapController::eliminar/$1');
		
		// ─ Cuentas ─
        $routes->get('cuentas/nuevo', 'Catalogos\CuentasController::formulario');
        $routes->get('cuentas/editar/(:num)', 'Catalogos\CuentasController::formulario/$1');
        $routes->post('cuentas/guardar', 'Catalogos\CuentasController::guardar');
        $routes->post('cuentas/guardar/(:num)', 'Catalogos\CuentasController::guardar/$1');
        $routes->post('cuentas/eliminar/(:num)', 'Catalogos\CuentasController::eliminar/$1');

        // ─ Partidas ─
        $routes->get('partidas/nuevo', 'Catalogos\PartidasController::formulario');
        $routes->get('partidas/editar/(:num)', 'Catalogos\PartidasController::formulario/$1');
        $routes->post('partidas/guardar', 'Catalogos\PartidasController::guardar');
        $routes->post('partidas/guardar/(:num)', 'Catalogos\PartidasController::guardar/$1');
        $routes->post('partidas/eliminar/(:num)', 'Catalogos\PartidasController::eliminar/$1');
        $routes->get('ajax/siguiente-clave-partida/(:num)', 'Catalogos\PartidasController::siguienteClaveJson/$1');

        // ─ Conceptos ─
        $routes->get('conceptos/nuevo', 'Catalogos\ConceptosController::formulario');
        $routes->get('conceptos/editar/(:num)', 'Catalogos\ConceptosController::formulario/$1');
        $routes->post('conceptos/guardar', 'Catalogos\ConceptosController::guardar');
        $routes->post('conceptos/guardar/(:num)', 'Catalogos\ConceptosController::guardar/$1');
        $routes->post('conceptos/eliminar/(:num)', 'Catalogos\ConceptosController::eliminar/$1');
        $routes->get('ajax/siguiente-clave-concepto/(:num)', 'Catalogos\ConceptosController::siguienteClaveJson/$1');
		
		// ─ Colonias ─
        $routes->get('colonias/nuevo', 'Catalogos\ColoniasController::formulario');
        $routes->get('colonias/editar/(:num)', 'Catalogos\ColoniasController::formulario/$1');
        $routes->post('colonias/guardar', 'Catalogos\ColoniasController::guardar');
        $routes->post('colonias/guardar/(:num)', 'Catalogos\ColoniasController::guardar/$1');
        $routes->post('colonias/eliminar/(:num)', 'Catalogos\ColoniasController::eliminar/$1');

        // ─ Vialidades ─
        $routes->get('vialidades/nuevo', 'Catalogos\VialidadesController::formulario');
        $routes->get('vialidades/editar/(:num)', 'Catalogos\VialidadesController::formulario/$1');
        $routes->post('vialidades/guardar', 'Catalogos\VialidadesController::guardar');
        $routes->post('vialidades/guardar/(:num)', 'Catalogos\VialidadesController::guardar/$1');
        $routes->post('vialidades/eliminar/(:num)', 'Catalogos\VialidadesController::eliminar/$1');
		
		// ─ Programas ─
		$routes->get('programas/nuevo', 'Catalogos\ProgramaController::formulario');
		$routes->get('programas/editar/(:num)', 'Catalogos\ProgramaController::formulario/$1');
		$routes->post('programas/guardar', 'Catalogos\ProgramaController::guardar');
		$routes->post('programas/guardar/(:num)', 'Catalogos\ProgramaController::guardar/$1');
		$routes->post('programas/eliminar/(:num)', 'Catalogos\ProgramaController::eliminar/$1');
    });
	
	// ═══════════════════════════════════════════════════════════
	// MÓDULO: OBRAS
	// ═══════════════════════════════════════════════════════════

	// Lectura (permiso: obras.ver)
	$routes->group('obras', ['filter' => 'permission:obras.ver'], function ($routes) {
		$routes->get('/', 'ObrasController::index');
		$routes->get('ajax/por-programa/(:num)', 'ObrasController::porProgramaJson/$1');
		$routes->get('ajax/por-colonia/(:num)', 'ObrasController::porColoniaJson/$1');
	});

	// Escritura (permiso: obras.administrar)
	$routes->group('obras', ['filter' => 'permission:obras.administrar'], function ($routes) {
		$routes->get('nuevo', 'ObrasController::formulario');
		$routes->get('editar/(:num)', 'ObrasController::formulario/$1');
		$routes->post('guardar', 'ObrasController::guardar');
		$routes->post('guardar/(:num)', 'ObrasController::guardar/$1');
		$routes->post('eliminar/(:num)', 'ObrasController::eliminar/$1');
	});
	
	// ═══════════════════════════════════════════════════════════
	// MÓDULO: VECINOS
	// ═══════════════════════════════════════════════════════════

	// Lectura (permiso: vecinos.ver)
	$routes->group('vecinos', ['filter' => 'permission:vecinos.ver'], function ($routes) {
		$routes->get('/', 'VecinosController::index');
		$routes->get('por-obra/(:num)', 'VecinosController::porObra/$1');
		$routes->get('ajax/datos-obra/(:num)', 'VecinosController::datosObraJson/$1');
		$routes->get('ajax/listado', 'VecinosController::ajaxListado');
	});

	// Escritura (permiso: vecinos.administrar)
	$routes->group('vecinos', ['filter' => 'permission:vecinos.administrar'], function ($routes) {
		$routes->get('nuevo', 'VecinosController::formulario');
		$routes->get('editar/(:num)', 'VecinosController::formulario/$1');
		$routes->post('guardar', 'VecinosController::guardar');
		$routes->post('guardar/(:num)', 'VecinosController::guardar/$1');
		$routes->post('eliminar/(:num)', 'VecinosController::eliminar/$1');
	});
	
	// ═══════════════════════════════════════════════════════════════
	// MÓDULO: CAJA (apertura / panel / cierre / arqueo)
	// ═══════════════════════════════════════════════════════════════
	$routes->group('caja', function ($routes) {
		$routes->get('/', 'Caja\CajaController::index');
		$routes->post('abrir', 'Caja\CajaController::abrir');
		$routes->get('panel', 'Caja\CajaController::panel');
		$routes->get('cerrar', 'Caja\CajaController::cerrar');
		$routes->post('cerrar', 'Caja\CajaController::procesarCierre');
		$routes->get('arqueo/(:num)', 'Caja\CajaController::arqueo/$1');
		$routes->get('reporte-diario/(:num)', 'Caja\CajaController::reporteDiario/$1');
	});
	
	// Listado general de arqueos (protegido, opcional)
	$routes->group('caja', ['filter' => 'permission:cobros.crear'], function ($routes) {
		$routes->get('arqueos', 'Caja\CajaController::arqueos'); // ← HISTORIAL
	});
	
	// ═══════════════════════════════════════════════════════════════
	// MÓDULO: COBRO DE SERVICIOS (permiso: cobros.crear)
	// ═══════════════════════════════════════════════════════════════
	$routes->group('servicios/cobro', ['filter' => 'permission:cobros.crear'], function ($routes) {
		$routes->get('/', 'Servicios\CobroServicioController::index');
		$routes->get('buscar-concepto', 'Servicios\CobroServicioController::buscarConceptoJson');
		$routes->post('guardar', 'Servicios\CobroServicioController::guardar');
		$routes->get('historial', 'Servicios\CobroServicioController::historial');
		$routes->get('detalle/(:num)', 'Servicios\CobroServicioController::detalle/$1');
		$routes->post('cancelar/(:num)', 'Servicios\CobroServicioController::cancelar/$1');
		$routes->get('imprimir/(:num)', 'Servicios\CobroServicioController::imprimir/$1');
	});
	
	$routes->group('aportaciones/cobro', ['filter' => 'permission:cobros.crear'], function ($routes) {
		$routes->get('/', 'Aportaciones\CobroAportacionController::index');
		$routes->get('buscar-vecino', 'Aportaciones\CobroAportacionController::buscarVecinoJson');
		$routes->post('guardar', 'Aportaciones\CobroAportacionController::guardar');
		$routes->get('historial', 'Aportaciones\CobroAportacionController::historial');
		$routes->get('detalle/(:num)', 'Aportaciones\CobroAportacionController::detalle/$1');
		$routes->post('cancelar/(:num)', 'Aportaciones\CobroAportacionController::cancelar/$1');
	});
	
	    // ═══════════════════════════════════════════════════════════
    // MÓDULO: REPORTES DE COBROS
    // ═══════════════════════════════════════════════════════════
    $routes->group('reportes', ['filter' => 'permission:cobros.crear'], function ($routes) {
        $routes->get('cobros', 'Reportes\ReporteCobrosController::index');
        $routes->post('cobros/servicios/excel', 'Reportes\ReporteCobrosController::excelServicios');
        $routes->post('cobros/aportaciones/excel', 'Reportes\ReporteCobrosController::excelAportaciones');
        $routes->post('cobros/vecinos/excel', 'Reportes\ReporteCobrosController::excelVecinosPorObra');
        $routes->post('cobros/arqueos/excel', 'Reportes\ReporteCobrosController::excelArqueos');
    });
});