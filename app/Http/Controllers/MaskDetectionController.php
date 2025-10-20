<?php

namespace App\Http\Controllers;

use App\Models\MaskDetection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MaskDetectionController extends Controller
{
    /**
     * Store mask detection results
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|string', // Base64 encoded image
            'detection_results' => 'required|array',
            'total_persons' => 'required|integer|min:0',
            'wearing_mask' => 'required|integer|min:0',
            'not_wearing_mask' => 'required|integer|min:0',
            'confidence_avg' => 'nullable|numeric|between:0,100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $imagePath = null;

            // Decode dan simpan gambar jika ada
            if ($request->has('image') && !empty($request->image)) {
                $imagePath = $this->saveBase64Image($request->image);
            }

            $maskDetection = MaskDetection::create([
                'image_path' => $imagePath,
                'detection_results' => $request->detection_results,
                'total_persons' => $request->total_persons,
                'wearing_mask' => $request->wearing_mask,
                'not_wearing_mask' => $request->not_wearing_mask,
                'confidence_avg' => $request->confidence_avg,
                'detected_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detection saved successfully',
                'data' => $maskDetection
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save detection',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all detections with optional filters
     */
    public function index()
    {
        $detections = MaskDetection::latest()->paginate(10);

        // Statistik sederhana
        $todayTotal   = MaskDetection::whereDate('detected_at', today())->count();
        $todayMask    = MaskDetection::whereDate('detected_at', today())->sum('wearing_mask');
        $todayNoMask  = MaskDetection::whereDate('detected_at', today())->sum('not_wearing_mask');

        return view('mask-detections.index', compact(
            'detections',
            'todayTotal',
            'todayMask',
            'todayNoMask'
        ));
    }

    /**
     * Get detection by ID
     */
    public function show($id)
    {
        $detection = MaskDetection::find($id);

        if (!$detection) {
            return response()->json([
                'success' => false,
                'message' => 'Detection not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $detection
        ]);
    }

    /**
     * Get detection statistics
     */
    public function statistics()
    {
        $total = \App\Models\MaskDetection::count();

        if ($total == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Detection not found'
            ]);
        }

        $totalPersons = \App\Models\MaskDetection::sum('total_persons');
        $withMask = \App\Models\MaskDetection::sum('wearing_mask');
        $withoutMask = \App\Models\MaskDetection::sum('not_wearing_mask');
        $avgConfidence = \App\Models\MaskDetection::avg('confidence_avg');

        return response()->json([
            'success' => true,
            'data' => [
                'total_records' => $total, // jumlah row deteksi
                'total_persons_detected' => $totalPersons,
                'total_wearing_mask' => $withMask,
                'total_not_wearing_mask' => $withoutMask,
                'compliance_rate' => $totalPersons > 0 ? round(($withMask / $totalPersons) * 100, 2) : 0,
                'avg_confidence' => round($avgConfidence, 2),
                'last_detection' => \App\Models\MaskDetection::latest('detected_at')->first(),
            ]
        ]);
    }

    /**
     * Delete detection
     */
    public function destroy($id)
    {
        $detection = MaskDetection::find($id);

        if (!$detection) {
            return response()->json([
                'success' => false,
                'message' => 'Detection not found'
            ], 404);
        }

        // Delete image file if exists
        if ($detection->image_path && Storage::disk('public')->exists($detection->image_path)) {
            Storage::disk('public')->delete($detection->image_path);
        }

        $detection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Detection deleted successfully'
        ]);
    }

    /**
     * Save base64 encoded image to storage
     */
    private function saveBase64Image($base64Image)
    {
        try {
            // Remove data:image/jpeg;base64, prefix if exists
            $image = preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $base64Image);
            $image = base64_decode($image);

            $fileName = 'mask_detection_' . time() . '_' . uniqid() . '.jpg';
            $path = 'detections/' . $fileName;

            Storage::disk('public')->put($path, $image);

            return $path;
        } catch (\Exception $e) {
            throw new \Exception('Failed to save image: ' . $e->getMessage());
        }
    }
}
