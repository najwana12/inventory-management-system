<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\GoodsOut;
use App\Models\GoodsIn;
use App\Models\Customer;
use App\Models\Item;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TransactionOutController extends Controller
{
    public function index(): View
    {
        $in_status = Item::where('active', 'true')->count();
        $customers = Customer::all();

        return view('admin.master.transaksi.keluar', compact('customers', 'in_status'));
    }

    public function list(Request $request): JsonResponse
    {
        // tampilkan semua transaksi keluar
        $goodsouts = GoodsOut::with('item', 'user', 'customer')
            ->latest()
            ->get();

        if ($request->ajax()) {

            return DataTables::of($goodsouts)

                ->addColumn('quantity', function ($data) {

                    $item = Item::with("unit")->find($data->item->id);

                    return $data->quantity . "/" . $item->unit->name;
                })

                ->addColumn("date_out", function ($data) {

                    return Carbon::parse($data->date_out)->format('d F Y');
                })

                ->addColumn("invoice_number", function ($data) {
                    return $data->invoice_number ?? '-';
                })

                ->addColumn("kode_barang", function ($data) {

                    return $data->item->code;
                })

                ->addColumn("customer_name", function ($data) {

                    return $data->customer->name ?? '-';
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

                    
                    // kalau sudah validated
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

                    //staff
                    if ($roleName === 'staff' && $data->status === 'pending') {

                        return "<span class='badge badge-warning'>
                                    Pending
                                </span>";
                    }

                    // ADMIN bisa verify pending
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

                    // setelah verify jadi success
                    if ($data->status === 'success') {

                        // SUPER ADMIN validate
                        if ($roleName === 'super_admin') {

                            $button  = "<button class='validasi-keluar btn btn-success m-1' data-id='{$data->id}'>";
                            $button .= "<i class='fas fa-check m-1'></i>Validate</button>";

                            $button .= "<button class='tolak btn btn-danger m-1' data-id='{$data->id}'>";
                            $button .= "<i class='fas fa-times m-1'></i>Reject</button>";

                            return $button;
                        }

                        return "<span class='badge badge-info'>
                                    Success
                                </span>";
                    }

                    // default pending
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
        $currentMonth = date('m', strtotime($request->date_out));
        $currentYear  = date('Y', strtotime($request->date_out));

        // stok hanya hitung transaksi validated
        $goodsInThisMonth = GoodsIn::whereMonth('date_received', $currentMonth)
            ->whereYear('date_received', $currentYear)
            ->where('status', 'validated')
            ->sum('quantity');

        $goodsOutThisMonth = GoodsOut::whereMonth('date_out', $currentMonth)
            ->whereYear('date_out', $currentYear)
            ->where('status', 'validated')
            ->sum('quantity');

        $totalStockThisMonth = max(0, $goodsInThisMonth - $goodsOutThisMonth);

        if ($request->quantity > $totalStockThisMonth || $totalStockThisMonth == 0) {

            return response()->json([
                "message" => __("insufficient stock this month")
            ])->setStatusCode(400);
        }

        $data = [

            'item_id'        => $request->item_id,
            'user_id'        => $request->user_id,
            'quantity'       => $request->quantity,
            'invoice_number' => $request->invoice_number,
            'date_out'       => $request->date_out,
            'customer_id'    => $request->customer_id,

            // default pending
            'status'         => 'pending',
        ];

        GoodsOut::create($data);

        return response()->json([
            "message" => __("saved successfully")
        ])->setStatusCode(200);
    }

    public function detail(Request $request): JsonResponse
    {
        $id   = $request->id;

        $data = GoodsOut::with('customer')
            ->where('id', $id)
            ->first();

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
        $data['customer_id']   = $data->customer_id;
        $data['id_barang']     = $barang->id;

        return response()->json([
            "data" => $data
        ])->setStatusCode(200);
    }

        public function update(Request $request): JsonResponse
        {
            $id   = $request->id;

            $data = GoodsOut::find($id);

            if (!$data) {

                return response()->json([
                    "message" => __("data not found")
                ])->setStatusCode(404);
            }

            $data->user_id     = $request->user_id;
            $data->customer_id = $request->customer_id;
            $data->date_out    = $request->date_out;
            $data->quantity    = $request->quantity;
            $data->item_id     = $request->item_id;

            // setelah diedit balik pending lagi
            $data->status      = 'pending';

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

            $data = GoodsOut::find($id);

            if (!$data) {

                return response()->json([
                    "message" => __("data not found")
                ])->setStatusCode(404);
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

    public function listOut(Request $request): JsonResponse
    {
        $items = Item::with('category', 'unit', 'brand')
           // ->where('active', 'true')
            // ->where('quantity', '>', 0)
            ->get();

            
        if ($request->ajax()) {

            return DataTables::of($items)

                ->addColumn('img', function($data){

                    if(empty($data->image)){
                        return "<img src='".asset('default.png')."' style='width:70px;height:70px;object-fit:cover;border:1px solid #ddd'/>";
                    }

                    return "<img src='".asset('storage/barang/'.$data->image)."' style='width:70px;height:70px;object-fit:cover;border:1px solid #ddd'/>";
                })

                ->addColumn('kode_barang', function ($data) {
                    return $data->code;
                })

                ->addColumn('nama_barang', function ($data) {
                    return $data->name;
                })

                ->addColumn('jenis_barang', function ($data) {
                    return $data->category->name ?? '-';
                })

                ->addColumn('satuan_barang', function ($data) {
                    return $data->unit->name ?? '-';
                })

                ->addColumn('merk_barang', function ($data) {
                    return $data->brand->name ?? '-';
                })

                ->addColumn('stok_awal', function ($data) {
                    return $data->quantity;
                })

                ->addColumn('tindakan', function ($data) {

                    $button  = "<button class='pilih-barang btn btn-primary'";
                    $button .= " data-id='".$data->id."'";
                    $button .= " data-kode='".$data->code."'";
                    $button .= " data-nama='".$data->name."'";
                    $button .= ">";
                    $button .= "<i class='fas fa-check'></i> Pilih";
                    $button .= "</button>";

                    return $button;
                })

                ->rawColumns(['img','tindakan'])
                ->make(true);
        }

        return response()->json([])->setStatusCode(400);
    }


    // VALIDASI transaksi keluar
    public function validateOut(Request $request): JsonResponse
    {
        if(strtolower(Auth::user()->role->name) !== 'super_admin'){
            return response()->json([
                "message" => __("access denied")
            ],403);
        }

        $id   = $request->id;

        $data = GoodsOut::find($id);

        if (!$data) {

            return response()->json([
                "message" => __("data not found")
            ])->setStatusCode(404);
        }

        // jika sudah divalidasi
        if ($data->status === 'validated') {

            return response()->json([
                "message" => __("data already validated")
            ])->setStatusCode(400);
        }

        // harus success dulu
        if ($data->status !== 'success') {

            return response()->json([
                "message" => __("must be verified first")
            ])->setStatusCode(400);
        }

        // validasi transaksi
        $data->status = 'validated';
        $data->save();

        // kurangi stok setelah validasi
        $barang = Item::find($data->item_id);

        if ($barang) {

            $barang->quantity -= $data->quantity;

            // supaya tidak minus
            if ($barang->quantity < 0) {
                $barang->quantity = 0;
            }

            $barang->save();
        }

        return response()->json([
            "message" => __("validation success")
        ])->setStatusCode(200);
    }


        public function verifyOut(Request $request): JsonResponse
    {

        if(strtolower(Auth::user()->role->name) !== 'admin'){
            return response()->json([
                "message" => __("access denied")
            ],403);
        }

        $data = GoodsOut::find($request->id);

        if (!$data) {
            return response()->json([
                "message" => __("data not found")
            ], 404);
        }

        // kalau sudah success / validated
        if ($data->status == 'success' || $data->status == 'validated') {

            return response()->json([
                "message" => __("already verified")
            ], 400);
        }

        $data->update([
            'status' => 'success'
        ]);

        return response()->json([
            "message" => __("verification success")
        ], 200);
    }

    
    // REJECT transaksi keluar
    public function reject(Request $request): JsonResponse
    {

        if(strtolower(Auth::user()->role->name) !== 'super_admin'){
            return response()->json([
                "message" => __("access denied")
            ],403);
        }

        $data = GoodsOut::find($request->id);

        if (!$data) {
            return response()->json([
                "message" => __("data not found")
            ], 404);
        }

        $data->status = 'rejected';
        $data->save();

        return response()->json([
            "message" => __("transaction rejected")
        ], 200);
    }
}

