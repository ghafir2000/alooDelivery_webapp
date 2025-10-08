<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExternalShop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;

class ExternalShopController extends Controller
{
    public function index()
    {
        $externalShops = ExternalShop::paginate(15); // قائمة المتاجر الخارجية
        return view('admin-views.vendor.external-shops.list', compact('externalShops'));
    }

    public function create()
    {
        return view('admin-views.vendor.external-shops.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $externalShop = new ExternalShop();
        $externalShop->name = $request->name;
        $externalShop->address = $request->address;
        $externalShop->uid = Str::random(32); // توليد UID عشوائي
        $externalShop->api_key = Str::random(64); // توليد API Key عشوائي
        $externalShop->save();

        Toastr::success('تم إضافة المتجر الخارجي بنجاح!');
        return redirect()->route('admin.vendors.external-shops.index');
    }

    public function edit($id)
    {
        $externalShop = ExternalShop::findOrFail($id);
        return view('admin-views.vendor.external-shops.edit', compact('externalShop'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $externalShop = ExternalShop::findOrFail($id);
        $externalShop->name = $request->name;
        $externalShop->address = $request->address;
        $externalShop->save();

        Toastr::success('تم تعديل المتجر الخارجي بنجاح!');
        return redirect()->route('admin.vendors.external-shops.index');
    }

    public function destroy($id)
    {
        $externalShop = ExternalShop::findOrFail($id);
        $externalShop->delete();

        Toastr::success('تم حذف المتجر الخارجي بنجاح!');
        return redirect()->route('admin.vendors.external-shops.index');
    }
}
