<?php

namespace App\Events;

class RowUpdated extends RowCreated
{
    public function broadcastAs(): string
    {
        return 'row.updated';
    }


}
