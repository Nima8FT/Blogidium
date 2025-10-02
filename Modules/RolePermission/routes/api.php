<?php

use Illuminate\Support\Facades\Route;
use Modules\RolePermission\Http\Controllers\PermissionController;
use Modules\RolePermission\Http\Controllers\RoleController;
use Modules\RolePermission\Http\Controllers\RolePermissionController;
use Modules\RolePermission\Http\Controllers\UserRoleController;

Route::middleware('jwt.auth')->group(function () {
    Route::apiResource('roles', RoleController::class)->names('roles');
    Route::apiResource('permissions', PermissionController::class)->names('permissions');
    Route::post('role/{role}/addpermissions', [RolePermissionController::class, 'addPermissions'])->name('role.permissions.add');
    Route::post('role/{role}/deletepermissions', [RolePermissionController::class, 'removePermissions'])->name('role.permissions.remove');
    Route::post('user/{user}/addrole', [UserRoleController::class, 'addRole'])->name('user.role.add');
    Route::post('user/{user}/removerole', [UserRoleController::class, 'removeRole'])->name('user.role.remove');
});
