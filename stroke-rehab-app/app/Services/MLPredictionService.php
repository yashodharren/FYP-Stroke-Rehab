<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class MLPredictionService
{
    protected $baseUrl = 'http://localhost:8001';
    protected $timeout = 30;

    /**
     * Predict recovery probability for a patient
     *
     * @param int $age
     * @param string $strokeType
     * @param string $deficitArea
     * @param string|null $medicalHistory
     * @return array
     * @throws Exception
     */
    public function predictRecovery(int $age, string $strokeType, string $deficitArea, ?string $medicalHistory = null): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/predict", [
                    'age' => $age,
                    'stroke_type' => $strokeType,
                    'deficit_area' => $deficitArea,
                    'medical_history' => $medicalHistory,
                ]);

            if ($response->failed()) {
                throw new Exception("ML Service returned error: {$response->status()}");
            }

            return $response->json();
        } catch (Exception $e) {
            \Log::error('ML Prediction Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if ML service is available
     *
     * @return bool
     */
    public function isServiceAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/health");
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get service status
     *
     * @return array
     */
    public function getServiceStatus(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/");
            return $response->json();
        } catch (Exception $e) {
            return [
                'status' => 'unavailable',
                'error' => $e->getMessage(),
            ];
        }
    }
}
