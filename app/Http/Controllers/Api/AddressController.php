<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            $request->user()->addresses()->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label'       => 'required|string|max:255',
            'nom_complet' => 'required|string|max:255',
            'telephone'   => 'required|string|max:20',
            'ville'       => 'required|string|max:255',
            'quartier'    => 'required|string|max:255',
            'adresse'     => 'required|string|max:500',
            'instructions'=> 'nullable|string|max:1000',
            'is_default'  => 'boolean',
        ]);

        $validated['user_id'] = $request->user()->id;

        if ($validated['is_default'] ?? false) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = Address::create($validated);

        return response()->json($address, 201);
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'label'       => 'required|string|max:255',
            'nom_complet' => 'required|string|max:255',
            'telephone'   => 'required|string|max:20',
            'ville'       => 'required|string|max:255',
            'quartier'    => 'required|string|max:255',
            'adresse'     => 'required|string|max:500',
            'instructions'=> 'nullable|string|max:1000',
            'is_default'  => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json($address);
    }

    public function destroy(Request $request, Address $address)
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $address->delete();

        return response()->json(['message' => 'Adresse supprimée']);
    }
}
