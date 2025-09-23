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
            'image' => 'required|string', // Base64 encoded image
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
    public function index(Request $request)
    {
        $query = MaskDetection::query();

        // Filter by date
        if ($request->has('date')) {
            $query->byDate($request->date);
        }

        // Filter by compliance status
        if ($request->has('compliance')) {
            if ($request->compliance === 'compliant') {
                $query->compliant();
            } elseif ($request->compliance === 'non_compliant') {
                $query->nonCompliant();
            }
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $detections = $query->orderBy('detected_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $detections
        ]);
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
    public function statistics(Request $request)
    {
        $query = MaskDetection::query();

        // Filter by date range if provided
        if ($request->has('start_date')) {
            $query->where('detected_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('detected_at', '<=', $request->end_date);
        }

        $stats = [
            'total_detections' => $query->count(),
            'total_persons_detected' => $query->sum('total_persons'),
            'total_wearing_mask' => $query->sum('wearing_mask'),
            'total_not_wearing_mask' => $query->sum('not_wearing_mask'),
            'compliance_rate' => 0,
            'average_confidence' => $query->avg('confidence_avg'),
            'detections_today' => MaskDetection::byDate(today())->count(),
            'compliant_detections' => $query->compliant()->count(),
            'non_compliant_detections' => $query->nonCompliant()->count()
        ];

        // Calculate compliance rate
        if ($stats['total_persons_detected'] > 0) {
            $stats['compliance_rate'] = round(
                ($stats['total_wearing_mask'] / $stats['total_persons_detected']) * 100,
                2
            );
        }

        return response()->json([
            'success' => true,
            'data' => $stats
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
