<?php

namespace App\Http\Controllers;

use App\Enums\RoleName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
     $user = $request->user();

        return match (true) {
            $user->hasRole(RoleName::ADMIN->value) =>
                redirect()->route('admin.dashboard'),

            $user->hasRole(RoleName::PHARMACY_OWNER->value) =>
                redirect()->route('pharmacy.dashboard'),

            $user->hasRole(RoleName::CUSTOMER->value) =>
                redirect()->route('customer.dashboard'),

            default => abort(403, 'No application role has been assigned.'),
        };
    }
}