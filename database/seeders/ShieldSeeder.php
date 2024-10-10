<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_cliente","view_any_cliente","create_cliente","update_cliente","restore_cliente","restore_any_cliente","replicate_cliente","reorder_cliente","delete_cliente","delete_any_cliente","force_delete_cliente","force_delete_any_cliente","view_compra","view_any_compra","create_compra","update_compra","restore_compra","restore_any_compra","replicate_compra","reorder_compra","delete_compra","delete_any_compra","force_delete_compra","force_delete_any_compra","view_producto","view_any_producto","create_producto","update_producto","restore_producto","restore_any_producto","replicate_producto","reorder_producto","delete_producto","delete_any_producto","force_delete_producto","force_delete_any_producto","view_proveedor","view_any_proveedor","create_proveedor","update_proveedor","restore_proveedor","restore_any_proveedor","replicate_proveedor","reorder_proveedor","delete_proveedor","delete_any_proveedor","force_delete_proveedor","force_delete_any_proveedor","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_venta","view_any_venta","create_venta","update_venta","restore_venta","restore_any_venta","replicate_venta","reorder_venta","delete_venta","delete_any_venta","force_delete_venta","force_delete_any_venta"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
