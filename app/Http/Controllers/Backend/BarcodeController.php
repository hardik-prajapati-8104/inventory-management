<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SparePart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarcodeController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    public function show(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.view')) {
            abort(403);
        }

        $parts = SparePart::where('id', $id)->get();

        if ($parts->isEmpty()) {
            abort(404);
        }

        return view('backend.barcodes.print', compact('parts'));
    }

    /**
     * Bulk label sheet — reads ?ids[]=1&ids[]=2..., used by the "Print
     * Selected Barcodes" button on the Spare Parts index.
     */
    public function bulk(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('spare-part.view')) {
            abort(403);
        }

        $ids = $request->get('ids', []);
        $parts = SparePart::whereIn('id', $ids)->get();

        if ($parts->isEmpty()) {
            session()->flash('error', 'No spare parts were selected to print.');
            return redirect()->route('admin.spare-parts.index');
        }

        return view('backend.barcodes.print', compact('parts'));
    }
}
