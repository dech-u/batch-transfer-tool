<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TimetableCollection extends ResourceCollection {
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $response = array();
        foreach ($this->collection as $key => $row) {
            $response[$key] = array(
                "start_time"         => $row['start_time'],
                "end_time"           => $row['end_time'],
                "day"                => $row['day'],
                "subject"            => $row->subject,
                "teacher_first_name" => $row['subject_teacher'] ? $row['subject_teacher']['teacher']['first_name'] ?? "" : "",
                "teacher_last_name"  => $row['subject_teacher'] ? $row['subject_teacher']['teacher']['last_name'] ?? "" : "",
                "note" => $row['note']
            );
        }
        return $response;
    }
}
