<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'status',
        'uid',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // JWT methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function profile() { return $this->hasOne(Profile::class, 'user_id');}
    public function roles() { return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id'); }
    public function createdBrands() { return $this->hasMany(Brand::class, 'created_by_id'); }
    public function updatedBrands() { return $this->hasMany(Brand::class, 'updated_by_id'); }
    public function createdCategory() { return $this->hasMany(Category::class, 'created_by_id'); }
    public function updatedCategory() { return $this->hasMany(Category::class, 'updated_by_id'); }
    public function createdSubCategory() { return $this->hasMany(SubCategory::class, 'created_by_id'); }
    public function updatedSubCategory() { return $this->hasMany(SubCategory::class, 'updated_by_id'); }
    public function createdProduct() { return $this->hasMany(Product::class, 'created_by_id'); }
    public function updatedProduct() { return $this->hasMany(Product::class, 'updated_by_id'); }
    public function createdSlug() { return $this->hasMany(Slug::class, 'created_by_id'); }
    public function updatedSlug() { return $this->hasMany(Slug::class, 'updated_by_id'); }
    public function createdTag() { return $this->hasMany(Tag::class, 'created_by_id'); }
    public function updatedTag() { return $this->hasMany(Tag::class, 'updated_by_id'); }
    public function createdInformation() { return $this->hasMany(Information::class, 'created_by_id'); }
    public function updatedInformation() { return $this->hasMany(Information::class, 'updated_by_id'); }
    public function createdVariationSize() { return $this->hasMany(VariationSize::class, 'created_by_id'); }
    public function updatedVariationSize() { return $this->hasMany(VariationSize::class, 'updated_by_id'); }
    public function createdVariationMaterial() { return $this->hasMany(VariationMaterial::class, 'created_by_id'); }
    public function updatedVariationMaterial() { return $this->hasMany(VariationMaterial::class, 'updated_by_id'); }
    public function createdVariationColor() { return $this->hasMany(VariationColor::class, 'created_by_id'); }
    public function updatedVariationColor() { return $this->hasMany(VariationColor::class, 'updated_by_id'); }
    public function createdVariationCapacity() { return $this->hasMany(VariationCapacity::class, 'created_by_id'); }
    public function updatedVariationCapacity() { return $this->hasMany(VariationCapacity::class, 'updated_by_id'); }
}
