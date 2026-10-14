<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Prediksi cuaca & kualitas udara lokasi kebun (Open-Meteo, gratis tanpa API key).
 * Hasil di-cache 30 menit agar hemat request.
 */
class WeatherApiController extends Controller
{
    public function index()
    {
        $lat  = (float) config('services.farm.lat', -6.8915);
        $lon  = (float) config('services.farm.lon', 107.6107);
        $name = config('services.farm.location_name', 'ASR Farm');

        try {
            $data = Cache::remember("farm_weather_{$lat}_{$lon}", now()->addMinutes(30), function () use ($lat, $lon, $name) {
                $weather = Http::timeout(8)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude'  => $lat,
                    'longitude' => $lon,
                    'current'   => 'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m,is_day',
                    'daily'     => 'temperature_2m_max,temperature_2m_min,precipitation_probability_max',
                    'timezone'  => 'Asia/Jakarta',
                    'forecast_days' => 1,
                ])->throw()->json();

                $aqi = null;
                try {
                    $air = Http::timeout(6)->get('https://air-quality-api.open-meteo.com/v1/air-quality', [
                        'latitude'  => $lat,
                        'longitude' => $lon,
                        'current'   => 'us_aqi',
                        'timezone'  => 'Asia/Jakarta',
                    ])->json();
                    $aqi = $air['current']['us_aqi'] ?? null;
                } catch (\Throwable $e) {
                    $aqi = null;
                }

                $current = $weather['current'] ?? [];
                $daily   = $weather['daily'] ?? [];
                $code    = (int) ($current['weather_code'] ?? 0);

                return [
                    'location'            => $name,
                    'temperature'         => round((float) ($current['temperature_2m'] ?? 0)),
                    'feels_like'          => round((float) ($current['apparent_temperature'] ?? 0)),
                    'humidity'            => (int) ($current['relative_humidity_2m'] ?? 0),
                    'wind_speed'          => round((float) ($current['wind_speed_10m'] ?? 0), 1),
                    'is_day'              => (bool) ($current['is_day'] ?? 1),
                    'weather_code'        => $code,
                    'condition'           => $this->conditionLabel($code),
                    'icon'                => $this->iconKey($code),
                    'temp_max'            => isset($daily['temperature_2m_max'][0]) ? round($daily['temperature_2m_max'][0]) : null,
                    'temp_min'            => isset($daily['temperature_2m_min'][0]) ? round($daily['temperature_2m_min'][0]) : null,
                    'rain_chance'         => $daily['precipitation_probability_max'][0] ?? null,
                    'aqi'                 => $aqi,
                    'air_quality'         => $this->aqiLabel($aqi),
                    'updated_at'          => now('Asia/Jakarta')->toIso8601String(),
                ];
            });

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data cuaca: ' . $e->getMessage(),
            ], 503);
        }
    }

    private function conditionLabel(int $code): string
    {
        return match (true) {
            $code === 0                 => 'Cerah',
            $code === 1                 => 'Cerah Berawan',
            $code === 2                 => 'Berawan Sebagian',
            $code === 3                 => 'Berawan',
            in_array($code, [45, 48])   => 'Berkabut',
            in_array($code, [51, 53, 55, 56, 57]) => 'Gerimis',
            in_array($code, [61, 80])   => 'Hujan Ringan',
            in_array($code, [63, 81])   => 'Hujan Sedang',
            in_array($code, [65, 66, 67, 82]) => 'Hujan Lebat',
            in_array($code, [95, 96, 99]) => 'Badai Petir',
            default                     => 'Berawan',
        };
    }

    private function iconKey(int $code): string
    {
        return match (true) {
            $code === 0                 => 'clear',
            in_array($code, [1, 2])     => 'partly_cloudy',
            $code === 3                 => 'cloudy',
            in_array($code, [45, 48])   => 'fog',
            in_array($code, [51, 53, 55, 56, 57, 61, 63, 80, 81]) => 'rain',
            in_array($code, [65, 66, 67, 82]) => 'heavy_rain',
            in_array($code, [95, 96, 99]) => 'thunder',
            default                     => 'cloudy',
        };
    }

    private function aqiLabel($aqi): string
    {
        if ($aqi === null) return 'Tidak tersedia';
        return match (true) {
            $aqi <= 50  => 'Baik',
            $aqi <= 100 => 'Sedang',
            $aqi <= 150 => 'Tidak Sehat (Sensitif)',
            $aqi <= 200 => 'Tidak Sehat',
            default     => 'Sangat Tidak Sehat',
        };
    }
}
