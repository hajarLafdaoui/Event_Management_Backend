<?php

namespace App\Imports;

use App\Models\GuestList;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class GuestsImport implements ToModel, WithHeadingRow
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function model(array $row)
    {
        return new GuestList([
            'event_id' => $this->eventId,
            'first_name' => $row['first_name'] ?? $row['first name'] ?? null,
            'last_name' => $row['last_name'] ?? $row['last name'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? $row['mobile'] ?? $row['telephone'] ?? null,
            'plus_one_allowed' => isset($row['plus_one']) ? (bool)$row['plus_one'] : false,
            'plus_one_name' => $row['plus_one_name'] ?? null,
            'dietary_restrictions' => $row['dietary_restrictions'] ?? $row['dietary'] ?? null,
            'qr_code' => Str::random(20),
        ]);
    }
}