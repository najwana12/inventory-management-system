<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\GoodsIn;
use App\Models\Supplier;
use App\Models\Item;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TransactionInController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::all();
        return view('admin.master.transaksi.masuk', compact('suppliers'));
    }

    public function list(Request $request): JsonResponse
    {
        // tampilkan semua transaksi di halaman transaksi
        $goodsins = GoodsIn::with('item', 'user', 'supplier')
            ->latest()
        ->get();

        if ($request->ajax()) {
            return DataTables::of($goodsins)
                ->addColumn('quantity', function ($data) {
                    $item = Item::with("unit")->find($data->item->id);
                    return $data->quantity . "/" . $item->unit->name;
                })
                ->addColumn("date_received", function ($data) {
                    return Carbon::parse($data->date_received)->format('d F Y');
                })
                ->addColumn("kode_barang", function ($data) {
                    return $data->item->code;
                })
                ->addColumn("supplier_name", function ($data) {
                    return $data->supplier->name;
                })
                ->addColumn("item_name", function ($data) {
                    return $data->item->name;
                })
               ->addColumn('tindakan', function ($data) {

                    $user = Auth::user();

                    if (!$user) {
                        return '-';
                    }

                    $roleName = strtolower($user->role ? $user->role->name : '');

                    // kalau validated
                    if ($data->status === 'validated') {

                        return "<span class='badge badge-success'>
                                    Validated
                                </span>";
                    }

                    // kalau rejected
                    if ($data->status === 'rejected') {

                        return "<span class='badge badge-danger'>
                                    Rejected
                                </span>";
                    }

                    // STAFF hanya lihat pending
                    if ($roleName === 'staff' && $data->status === 'pending') {

                        return "<span class='badge badge-warning'>
                                    Pending
                                </span>";
                    }

                    // ADMIN bisa edit hapus verify
                    if ($roleName === 'admin' && $data->status === 'pending') {

                        $button  = "<button class='ubah btn btn-warning m-1'
                                        id='{$data->id}'>
                                        <i class='fas fa-edit'></i>Edit
                                    </button>";

                        $button .= "<button class='hapus btn btn-danger m-1'
                                        data-id='{$data->id}'>
                                        <i class='fas fa-trash'></i>Delete
                                    </button>";

                        $button .= "<button class='verify btn btn-primary m-1'
                                        data-id='{$data->id}'>
                                        <i class='fas fa-check'></i>Verify
                                    </button>";

                        return $button;
                    }

                    // setelah verify
                    if ($data->status === 'verified') {

                        // SUPER ADMIN validate
                        if ($roleName === 'super_admin') {

                            $button  = "<button class='validasi btn btn-success m-1'
                                            data-id='{$data->id}'>";

                            $button .= "<i class='fas fa-check'></i> Validate";

                            $button .= "</button>";

                            $button .= "<button class='tolak btn btn-danger m-1'
                                            data-id='{$data->id}'>";

                            $button .= "<i class='fas fa-times'></i> Reject";

                            $button .= "</button>";

                            return $button;
                        }

                        return "<span class='badge badge-primary'>
                                    Verified
                                </span>";
                    }

                    return "<span class='badge badge-warning'>
                                Pending
                            </span>";
                })
                ->rawColumns(['tindakan'])
                ->make(true);
        }

        return response()->json([])->setStatusCode(400);
    }

    public function save(Request $request): JsonResponse
    {
        $data = [
            'user_id'        => $request->user_id,
            'supplier_id'    => $request->supplier_id,
            'date_received'  => $request->date_received,
            'quantity'       => $request->quantity,
            'invoice_number' => $request->invoice_number,
            'item_id'        => $request->item_id,
            'status'         => 'pending',
        ];

        GoodsIn::create($data);

        return response()->json([
            "message" => __("saved successfully")
        ])->setStatusCode(200);
    }

    public function detail(Request $request): JsonResponse
    {
        $id   = $request->id;
        $data = GoodsIn::with('supplier')->where('id', $id)->first();

        if (!$data) {
            return response()->json([
                "message" => __("data not found")
            ])->setStatusCode(404);
        }

        $barang = Item::with('category', 'unit')->find($data->item_id);

        $data['kode_barang']   = $barang->code;
        $data['satuan_barang'] = $barang->unit->name;
        $data['jenis_barang']  = $barang->category->name;
        $data['nama_barang']   = $barang->name;
        $data['supplier_id']   = $data->supplier_id;
        $data['id_barang']     = $barang->id;

        return response()->json([
            "data" => $data
        ])->setStatusCode(200);
    }

    public function update(Request $request): JsonResponse
    {

        if(strtolower(Auth::user()->role->name) !== 'admin'){
            return response()->json([
                "message" => __("access denied")
            ],403);
        }

        $id   = $request->id;
        $data = GoodsIn::find($id);

        if (!$data) {
            return response()->json([
                "message" => __("data not found")
            ])->setStatusCode(404);
        }

        if($data->status === 'validated'){
            return response()->json([
                "message" => __("validated data cannot be edited")
            ],400);
        }

        $data->user_id       = $request->user_id;
        $data->supplier_id   = $request->supplier_id;
        $data->date_received = $request->date_received;
        $data->quantity      = $request->quantity;
        $data->item_id       = $request->item_id;

        // balik pending setelah edit
        $data->status        = 'pending';

        $status = $data->save();

        if (!$status) {
            return response()->json([
                "message" => __("data failed to change")
            ])->setStatusCode(400);
        }

        return response()->json([
            "message" => __("data changed successfully")
        ])->setStatusCode(200);
    }

    public function delete(Request $request): JsonResponse
    {
        if(strtolower(Auth::user()->role->name) !== 'admin'){
            return response()->json([
                "message" => __("access denied")
            ],403);
        }

        $id   = $request->id;

        $data = GoodsIn::find($id);

        if (!$data) {
            return response()->json([
                "message" => __("data not found")
            ])->setStatusCode(404);
        }

        if($data->status === 'validated'){
            return response()->json([
                "message" => __("validated data cannot be deleted")
            ],400);
        }

        $status = $data->delete();

        if (!$status) {

            return response()->json([
                "message" => __("data failed to delete")
            ])->setStatusCode(400);
        }

        return response()->json([
            "message" => __("data deleted successfully")
        ])->setStatusCode(200);
    }

    // VALIDASI transaksi barang masuk
        public function validateIn(Request $request): JsonResponse
    {

        if(strtolower(Auth::user()->role->name) !== 'super_admin'){
            return response()->json([
                "message" => __("access denied")
            ],403);
        }

        $data = GoodsIn::find($request->id);

        if (!$data) {
            return response()->json([
                "message" => __("data not found")
            ], 404);
        }

        // kalau sudah validated
        if ($data->status === 'validated') {
            return response()->json([
                "message" => __("data already validated")
            ], 400);
        }

        // harus success dulu
        if ($data->status !== 'verified') {

            return response()->json([
                "message" => __("must be verified first")
            ], 400);
        }

        // ubah status
        $data->status = 'validated';
        $data->save();

        // TAMBAH STOCK BARANG
        $barang = Item::find($data->item_id);

        if ($barang) {

            // tambah quantity
            $barang->quantity += $data->quantity;

            // aktifkan barang otomatis kalau stock ada
            $barang->active = true;

            $barang->save();
        }

        return response()->json([
            "message" => __("validation success")
        ], 200);
    }

    public function verifyIn(Request $request): JsonResponse
    {

        if(strtolower(Auth::user()->role->name) !== 'admin'){
            return response()->json([
                "message" => __("access denied")
            ],403);
        }

        $data = GoodsIn::find($request->id);

        if (!$data) {
            return response()->json([
                "message" => __("data not found")
            ], 404);
        }

        // kalau sudah success / validated
      if (
            $data->status == 'verified' ||
            $data->status == 'validated' ||
            $data->status == 'rejected'
        ) {
            return response()->json([
                "message" => __("already verified")
            ], 400);
        }

        $data->update([
            'status' => 'verified'
        ]);

        return response()->json([
            "message" => __("verification success")
        ], 200);
    }

        public function reject(Request $request): JsonResponse
    {

        if(strtolower(Auth::user()->role->name) !== 'super_admin'){
            return response()->json([
                "message" => __("access denied")
            ],403);
        }

        $data = GoodsIn::find($request->id);

        if (!$data) {
            return response()->json([
                "message" => __("data not found")
            ], 404);
        }

        if ($data->status !== 'verified') {

            return response()->json([
                "message" => __("must be verified first")
            ], 400);
        }

        $data->status = 'rejected';
        $data->save();

        return response()->json([
            "message" => __("transaction rejected")
        ], 200);
    }

    public function listIn(Request $request): JsonResponse
    {
    $items = Item::with(['category', 'unit', 'brand'])->get();

        if ($request->ajax()) {

            return DataTables::of($items)

                ->addColumn('img', function ($data) {

                    if (empty($data->image)) {

                        return "<img src='" . asset('default.png') . "' 
                        style='width:100%;max-width:240px;aspect-ratio:1;
                        object-fit:cover;padding:1px;border:1px solid #ddd'/>";
                    }

                    return "<img src='" . asset('storage/barang/' . $data->image) . "' 
                    style='width:100%;max-width:240px;aspect-ratio:1;
                    object-fit:cover;padding:1px;border:1px solid #ddd'/>";
                })

                ->addColumn('category_name', function ($data) {
                    return $data->category->name ?? '-';
                })

                ->addColumn('unit_name', function ($data) {
                    return $data->unit->name ?? '-';
                })

                ->addColumn('brand_name', function ($data) {
                    return $data->brand->name ?? '-';
                })

                ->addColumn('tindakan', function ($data) {

                    $button  = "<button class='pilih-barang btn btn-primary'";
                    $button .= " data-id='".$data->id."'";
                    $button .= " data-kode='".$data->code."'";
                    $button .= " data-nama='".$data->name."'>";
                    $button .= "<i class='fas fa-check'></i> Pilih";
                    $button .= "</button>";

                    return $button;
                })

                ->rawColumns(['img', 'tindakan'])
                ->make(true);
        }

        return response()->json([])->setStatusCode(400);
    }
}