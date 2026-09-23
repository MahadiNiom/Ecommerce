<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Variant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the authenticated user's dashboard.
     */
    public function __invoke(): View
    {
        $user = auth()->user();

        return view('dashboard', [
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'productsCount' => Product::count(),
            'variantsCount' => Variant::count(),
            'productVariantsCount' => ProductVariant::count(),
        ]);
    }
}
