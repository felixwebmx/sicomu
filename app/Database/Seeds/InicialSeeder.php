<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InicialSeeder extends Seeder
{
    public function run()
    {
        // Rol
        $rolId = $this->db->table('roles')->insert([
            'nombre'      => 'Administrador',
            'descripcion' => 'Acceso total al sistema',
            'created_at'  => date('Y-m-d H:i:s'),
        ]) ? $this->db->insertID() : null;

        // Permisos base
        $permisos = [
            ['clave' => 'usuarios.administrar', 'nombre' => 'Administrar usuarios', 'modulo' => 'Usuarios'],
            ['clave' => 'cobros.crear',         'nombre' => 'Registrar cobros',     'modulo' => 'Cobros'],
            ['clave' => 'reportes.ver',         'nombre' => 'Ver reportes',         'modulo' => 'Reportes'],
        ];

        foreach ($permisos as $permiso) {
            $permiso['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('permisos')->insert($permiso);
            $permisoId = $this->db->insertID();

            $this->db->table('rol_permiso')->insert([
                'rol_id'     => $rolId,
                'permiso_id' => $permisoId,
            ]);
        }

        // Usuario admin (password hasheado por el modelo al insertar vía Model,
        // aquí usamos query builder directo así que hasheamos manualmente)
        $usuarioId = $this->db->table('usuarios')->insert([
            'nombre_usuario'  => 'admin',
            'correo'          => 'admin@uriangato.gob.mx',
            'nombre_completo' => 'Administrador General',
            'password_hash'   => password_hash('CambiaEstaClave123!', PASSWORD_BCRYPT, ['cost' => 12]),
            'estatus'         => 'activo',
            'created_at'      => date('Y-m-d H:i:s'),
        ]) ? $this->db->insertID() : null;

        $this->db->table('usuario_rol')->insert([
            'usuario_id' => $usuarioId,
            'rol_id'     => $rolId,
        ]);
    }
}