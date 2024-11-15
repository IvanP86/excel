<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'tasks';

    const STATUS_PROCESS = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_ERROR = 3;

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PROCESS => 'Импорт в процессе обработки',
            self::STATUS_SUCCESS => 'Импорт данных успешно завершен',
            self::STATUS_ERROR => 'Ошибка во время импорта'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'file_id', 'id');
    }

    public function failedRows(): HasMany
    {
        return $this->hasMany(FailedRow::class, 'task_id', 'id');
    }
}
