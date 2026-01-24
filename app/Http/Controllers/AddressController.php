<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Profile/Addresses', [
            'addresses' => $request->user()->addresses()->orderByDesc('is_default')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'country_code' => 'required|string|max:2',
            'is_default' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $request->user()->addresses()->create($validated);

        return Redirect::route('addresses.index');
    }

    public function update(Request $request, Address $address)
    {
        // Ensure the address belongs to the user
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'label' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'country_code' => 'required|string|max:2',
            'is_default' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return Redirect::route('addresses.index');
    }

    public function destroy(Request $request, Address $address)
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $address->delete();

        return Redirect::route('addresses.index');
    }
}
