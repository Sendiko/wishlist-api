<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WishlistController extends Controller
{
    private function getValidatedUserId(Request $request)
    {
        $userId = $request->header('X-User-ID');

        if (empty($userId)) {
            return response()->json([
                'status' => false,
                'message' => 'Header X-User-ID tidak ditemukan.',
            ], 400);
        }

        if (!User::find($userId)) {
            return response()->json([
                'status' => false,
                'message' => 'User ID yang diberikan tidak valid.',
            ], 404);
        }

        return $userId;
    }


    public function index(Request $request)
    {
        $userId = $request->header('X-User-ID');

        $data = Wishlist::with('kategori')
            ->where('userId', $userId)
            ->orderByRaw("FIELD(priority, 'High', 'Medium', 'Low') ASC")
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => "Data wishlist berhasil ditemukan",
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $userId = $request->header('X-User-ID');
        
        $rules = [
            'name' => 'required|string|max:255',
            'categoryId' => 'required|integer|exists:kategori,id',
            'price' => 'required|numeric|min:0',
            'priority' => ['required', Rule::in(['High', 'Medium', 'Low'])],
            'description' => 'nullable|string',
            'picture' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Gagal menambahkan wishlist",
                'errors' => $validator->errors()
            ], 400);
        }

        $imagePath = null;
        if ($request->hasFile('picture')) {
            $imagePath = $request->file('picture')->store('wishlist_pictures', 'public');
        }

        $dataWishlist = new Wishlist;
        $dataWishlist->userId = $userId;
        $dataWishlist->name = $request->name;
        $dataWishlist->categoryId = $request->categoryId;
        $dataWishlist->price = $request->price;
        $dataWishlist->priority = $request->priority;
        $dataWishlist->description = $request->description;
        $dataWishlist->picture = $imagePath;

        if ($dataWishlist->save()) {
            $dataWishlist->load('kategori');
            return response()->json([
                'status' => true,
                'message' => "Wishlist berhasil ditambahkan",
                'data' => $dataWishlist
            ], 201);
        } else {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            return response()->json(['status' => false, 'message' => "Gagal menyimpan wishlist"], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $userId = $request->header('X-User-ID');

        $data = Wishlist::with('kategori')->where('userId', $userId)->find($id);

        if ($data) {
            return response()->json(['status' => true, 'message' => "Data wishlist berhasil ditemukan", 'data' => $data], 200);
        } else {
            return response()->json(['status' => false, 'message' => "Data wishlist tidak ditemukan atau Anda tidak memiliki akses"], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $userId = $request->header('X-User-ID');
        
        $dataWishlist = Wishlist::where('userId', $userId)->find($id);

        if (empty($dataWishlist)) {
            return response()->json(['status' => false, 'message' => "Wishlist tidak ditemukan atau Anda tidak memiliki akses"], 404);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'categoryId' => 'required|integer|exists:kategori,id',
            'price' => 'required|numeric|min:0',
            'priority' => ['required', Rule::in(['High', 'Medium', 'Low'])],
            'description' => 'nullable|string',
            'picture' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => "Gagal melakukan update wishlist", 'errors' => $validator->errors()], 400);
        }

        $newImagePath = $dataWishlist->picture;
        if ($request->hasFile('picture')) {
            if ($dataWishlist->picture && Storage::disk('public')->exists($dataWishlist->picture)) {
                Storage::disk('public')->delete($dataWishlist->picture);
            }
            $newImagePath = $request->file('picture')->store('wishlist_pictures', 'public');
        }

        $dataWishlist->name = $request->name;
        $dataWishlist->categoryId = $request->categoryId;
        $dataWishlist->price = $request->price;
        $dataWishlist->priority = $request->priority;
        $dataWishlist->description = $request->description;
        $dataWishlist->picture = $newImagePath;

        if ($dataWishlist->save()) {
            $dataWishlist->load('kategori');
            return response()->json(['status' => true, 'message' => "Wishlist berhasil diperbarui", 'data' => $dataWishlist], 200);
        } else {
            if ($request->hasFile('picture') && $newImagePath !== $dataWishlist->getOriginal('picture') && Storage::disk('public')->exists($newImagePath)) {
                 Storage::disk('public')->delete($newImagePath);
            }
            return response()->json(['status' => false, 'message' => "Gagal memperbarui wishlist"], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $userId = $request->header('X-User-ID');

        $dataWishlist = Wishlist::where('userId', $userId)->find($id);

        if (empty($dataWishlist)) {
            return response()->json(['status' => false, 'message' => "Data wishlist tidak ditemukan atau Anda tidak memiliki akses"], 404);
        }

        $oldImagePath = $dataWishlist->picture;

        if ($dataWishlist->delete()) {
            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
            return response()->json(['status' => true, 'message' => 'Data wishlist berhasil dihapus'], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Gagal menghapus wishlist'], 500);
        }
    }
}