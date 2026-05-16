<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketDetail extends Model
{
    protected $fillable = ['ticket_id', 'description', 'attachment_path', 'report', 'start_date', 'end_date'];

    protected $casts = [
        'report' => 'array',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
