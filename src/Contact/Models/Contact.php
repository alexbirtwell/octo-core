<?php

namespace Octo\Contact\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Octo\Contact\database\factories\ContactFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Octo\ObservableModel;
use Spatie\Tags\HasTags;

class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasTags;
    use ObservableModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contact_type',
        'contact_id',
        'status',
        'name',
        'properties',
        'nickname',
        'email',
        'phone',
        'mobile_phone',
        'mobile_phone_is_whatsapp',
        'birthday',
        'gender',
        'favorite',
        'notificable',
        'loggable',
    ];

    /**
     * The default model attributes
     *
     * @var array
     */
    protected $attributes = [
       'properties' => '{"description": ""}',
       'status' => true
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'properties' => AsArrayObject::class,
        'mobile_phone_is_whatsapp' => 'boolean',
        'favorite' => 'boolean',
        'notificable' => 'boolean',
        'loggable' => 'boolean',
    ];

    /**
     * The attributes of kind date
     *
     * @var array
     */
    protected $dates = [
        'birthday',
    ];

    /**
     * The factory associated with the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public static function newFactory()
    {
        return ContactFactory::new();
    }
}
