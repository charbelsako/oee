<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceNote;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function store(Request $request)
    {
        if ($request->has('notes')) {
            $notes = $request->notes;
            $device_note = DeviceNote::query()->create(['notes'=>$notes]);
            return response()->json([
                'status' => true,
                'data' => $device_note,
                'message' => 'Added Successfully!!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'Added Unsuccessfully!!'
            ]);
        }
    }
}
