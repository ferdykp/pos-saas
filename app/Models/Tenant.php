<?php

// app/Models/Tenant.php

namespace App\Models;

use App\Support\BusinessProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'business_type',
        'business_modules',
        'slug',
        'email',
        'phone',
        'address',
        'img_logo',
        'status',
    ];

    protected $casts = ['business_modules' => 'array'];

    public function businessType(): string
    {
        return BusinessProfile::normalize($this->business_type);
    }

    public function businessModules(): array
    {
        return $this->business_modules ?? BusinessProfile::defaults($this->businessType());
    }

    public function hasBusinessModule(string $module): bool
    {
        return in_array($module, $this->businessModules(), true);
    }

    public function catalogLabel(): string
    {
        return BusinessProfile::catalog($this->businessType());
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getSetting($key, $default = null)
    {
        $setting = $this->settings()->where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    // =========================================================================
    // HELPER SUBSCRIPTION & PAKET
    // =========================================================================

    /**
     * Mendapatkan objek Plan yang sedang aktif digunakan tenant
     */
    public function currentPlan(): ?Plan
    {
        $activeSubscription = $this->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>=', now()->startOfDay())
            ->latest()
            ->first();

        return $activeSubscription ? $activeSubscription->plan : null;
    }

    /**
     * Cek apakah tenant melebihi kuota transaksi bulanan (Khusus Paket Starter: Max 100)
     */
    public function isTransactionLimitReached(): bool
    {
        $plan = $this->currentPlan();

        // Jika tidak memiliki paket aktif sama sekali, anggap terblokir
        if (! $plan) {
            return true;
        }

        // Pembatasan Khusus Paket Starter (Maksimal 100 Transaksi / Bulan)
        if ($plan->slug === 'starter') {
            $monthlyOrdersCount = $this->orders()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            return $monthlyOrdersCount >= 100;
        }

        // Paket Growth & Scale = Unlimited Transaction
        return false;
    }
}
