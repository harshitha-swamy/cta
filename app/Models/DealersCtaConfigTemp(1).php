<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealersCtaConfigTemp extends Model
{
    protected $table = 'dealers_cta_config_temp';

    protected $primaryKey = 'dealer_code';

    public $incrementing = false;  // since dealer_code is NOT auto-increment
    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'dealer_code',
        'button_class',
        'button_class_vdp',
        'button_class_cpov',
        'button_text',
        'button_text_vdp',
        'button_text_cpov',
        'button_image_url',
        'button_image_url_vdp',
        'button_image_url_cpov',
        'image_style',
        'image_style_vdp',
        'image_style_cpov',
        'dealer_type',
        'remove_used_cpov',
        'vehicle_type',
        'cpov_widget',
        't3_new_widget',
        'new_approach_enable'
    ];
}
