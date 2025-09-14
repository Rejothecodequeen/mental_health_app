<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiaryEntry extends Model
{
    use HasFactory;

    // Make sure this is PUBLIC and not protected/private
    protected $fillable = ['user_id', 'title', 'content', 'entry_date'];

    // Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
