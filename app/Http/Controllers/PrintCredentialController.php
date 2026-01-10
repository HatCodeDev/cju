<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class PrintCredentialController extends Controller
{
    /**
     * Gestiona la visualización de credenciales masivas o individuales.
     * Recibe IDs por query string para evitar formularios POST complejos desde acciones GET.
     */
    public function __invoke(Request $request): View
    {
        // Validar que vengan IDs
        $ids = explode(',', $request->query('ids', ''));

        $users = User::query()
            ->whereIn('id', $ids)
            ->with('roles') // Eager loading para optimizar
            ->get();

        return view('print.credentials', [
            'users' => $users,
            'generated_at' => now(),
        ]);
    }
}
