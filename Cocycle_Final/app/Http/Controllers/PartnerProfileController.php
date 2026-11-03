<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PartnerProfileController extends Controller
{
    private function getStore()
    {
        return Store::where('user_id', Auth::id())->first();
    }

    public function index()
    {
        $store = $this->getStore();
        if (!$store) {
           // Fallback if needed, but assuming check is done
           return redirect()->route('partner.home')->with('error', 'Store not found');
        }

        return view('partner.profile.index', compact('store'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'logo_image' => 'nullable|image|max:2048' // 2MB Max
        ]);

        $store = $this->getStore();
        if (!$store) abort(404);

        $store->store_name = $request->name;
        $store->description = $request->description;
        $store->phone = $request->phone;
        $store->email = $request->email;
        $store->address = $request->address;

        if ($request->hasFile('logo_image')) {
            $file = $request->file('logo_image');
            $filename = uniqid('logo_', true).'.'.$file->extension();
            $file->move(public_path('photos/logos'), $filename);
            $store->logo_url = 'photos/logos/'.$filename;
        }

        $store->save();
        Auth::user()->forceFill(['nama_toko' => $store->store_name])->save();

        return redirect()->route('partner.profile.index')->with('success', 'Profil toko berhasil diperbarui!');
    }
}
