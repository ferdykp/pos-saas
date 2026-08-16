<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class User extends Authenticatable
{
    // HANYA gunakan Trait resmi di sini (Jangan ada VerifyEmail di baris use ini)
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'avatar',
        'password',
        'status',
        'role',
        'google_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Send email verification notification
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new class extends VerifyEmail {
            public function toMail($notifiable)
            {
                $verificationUrl = $this->verificationUrl($notifiable);

                return (new MailMessage)
                    ->subject('Konfirmasi Registrasi Akun GrowPOS Anda')
                    ->greeting('Halo ' . $notifiable->name . ',')
                    ->line('Terima kasih telah memilih GrowPOS sebagai mitra pertumbuhan bisnis Anda.')
                    ->line('Satu langkah terakhir untuk mengaktifkan seluruh fitur kasir pintar dan laporan otomatis: silakan konfirmasi bahwa alamat email ini adalah milik Anda.')
                    ->action('Verifikasi Email Sekarang', $verificationUrl)
                    ->line('Tautan verifikasi ini berlaku selama 60 menit demi keamanan akun Anda.')
                    ->line('Jika Anda tidak merasa mendaftar di layanan GrowPOS, Anda dapat mengabaikan email ini secara aman.')
                    ->salutation("Salam hangat,\nTim Ekosistem GrowPOS Indonesia");
            }
        });
    }
}
