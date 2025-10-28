<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = ['value'];
    public $timestamps = false;

    public function isImage($value): int|false
    {
        return preg_match('#\\.\\w+$#', $value);
    }
}
