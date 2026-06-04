<?php

namespace App\Models;

class ClassSchedule extends Model {

    protected static string $table = 'class_schedules';

    public $academy_id  = null;
    public $modality_id = null;
    public $weekday     = null;
    public $start_time  = null;
    public $end_time    = null;
    public $notes       = null;

    protected function toArray(): array {
        return [
            'academy_id'  => $this->academy_id,
            'modality_id' => $this->modality_id ?: null,
            'weekday'     => $this->weekday,
            'start_time'  => $this->start_time,
            'end_time'    => $this->end_time    ?: null,
            'notes'       => $this->notes       ?: null,
            'is_active'   => 1,
        ];
    }
}