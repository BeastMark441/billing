<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InfrastructureService extends Model
{
    protected $fillable = [
        'infrastructure_category_id',
        'infrastructure_subcategory_id',
        'name',
        'slug',
        'description',
        'price',
        'specifications',
        'sort_order',
        'is_active',
        'one_per_user',
        'integration_type',
    ];

    protected $casts = [
        'specifications' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'one_per_user' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(InfrastructureCategory::class, 'infrastructure_category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(InfrastructureSubcategory::class, 'infrastructure_subcategory_id');
    }

    public function getDisplaySpecificationsAttribute(): array
    {
        $specs = is_array($this->specifications) ? $this->specifications : [];
        if ($specs === []) {
            return [];
        }

        $candidateKeys = [
            'ram' => ['озу', 'ram', 'memory'],
            'cpu' => ['cpu', 'процессор'],
            'disk' => ['диск', 'disk', 'storage'],
        ];

        $chosenByCanonical = [];
        foreach ($candidateKeys as $canonical => $candidates) {
            foreach ($candidates as $candidate) {
                foreach ($specs as $key => $value) {
                    $rawKey = is_string($key) ? $key : '';
                    $normalized = $rawKey !== '' ? mb_strtolower(trim($rawKey)) : '';
                    if ($normalized === $candidate) {
                        $chosenByCanonical[$canonical] = $rawKey;
                        break 2;
                    }
                }
            }
        }

        $hiddenKeys = [
            'egg_id',
            'nest_id',
            'io',
            'swap',
            'spaw',
            'startup',
            'docker_image',
            'environment',
            'allocations',
            'databases',
            'backups',
            'proxmox',
        ];
        $hiddenKeys = array_fill_keys($hiddenKeys, true);

        $result = [];
        foreach ($specs as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            $normalized = mb_strtolower(trim($key));
            if ($normalized !== '' && isset($hiddenKeys[$normalized])) {
                continue;
            }

            $canonical = null;
            foreach ($candidateKeys as $c => $candidates) {
                if (in_array($normalized, $candidates, true)) {
                    $canonical = $c;
                    break;
                }
            }

            if ($canonical !== null) {
                if (($chosenByCanonical[$canonical] ?? null) !== $key) {
                    continue;
                }

                $label = match ($canonical) {
                    'ram' => 'ОЗУ',
                    'cpu' => 'CPU',
                    'disk' => 'Диск',
                    default => $key,
                };

                $result[$label] = $this->formatSpecValue($canonical, $value);
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    protected function formatSpecValue(string $canonical, mixed $value): string
    {
        if (! is_scalar($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $valueStr = (string) $value;

        if ($canonical === 'ram' || $canonical === 'disk') {
            if (is_numeric($value)) {
                $mb = (float) $value;
                if ($mb >= 1024) {
                    $gb = $mb / 1024;
                    $formatted = rtrim(rtrim(number_format($gb, 1, '.', ''), '0'), '.');
                    return $formatted.' ГБ';
                }

                return (string) ((int) $mb).' МБ';
            }

            $valueStr = preg_replace('/\s*(GB|ГБ)\s*/iu', ' ГБ', $valueStr);
            $valueStr = preg_replace('/\s*(MB|МБ)\s*/iu', ' МБ', $valueStr);

            return trim($valueStr);
        }

        if ($canonical === 'cpu') {
            if (is_numeric($value)) {
                return (string) ((int) $value).'%';
            }

            $valueStr = preg_replace('/\s*core(s)?\s*/iu', ' Core', $valueStr);
            $valueStr = preg_replace('/\s*%\s*/u', '%', $valueStr);

            return trim($valueStr);
        }

        return trim($valueStr);
    }
}
