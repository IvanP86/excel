<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedRow extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'failed_rows';

    public static function insertFailedRows($items, Task $task)
    {
        foreach($items as $item){
            FailedRow::create($item);
        }

        $task->update([
            'status' => TASK::STATUS_ERROR
        ]);
    }
}
