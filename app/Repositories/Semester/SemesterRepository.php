<?php

namespace App\Repositories\Semester;

use App\Models\Semester;
use App\Repositories\Saas\SaaSRepository;

class SemesterRepository extends SaaSRepository implements SemesterInterface {

    public function __construct(Semester $model) {
        parent::__construct($model);
    }

    public function default($schoolId = null) {
        $currentMonth = (int)date('m');
        $semesters = $this->defaultModel()->withTrashed()->get();
        $currentSemester = null;
        foreach ($semesters as $semester) {
            $semesterRange = [];
            if ($semester->start_month < $semester->end_month) {
                for ($i = $semester->start_month; $i <= $semester->end_month; $i++) {
                    $semesterRange[] = $i;
                }
            } else {
                for ($i = $semester->start_month; $i <= 12; $i++) {
                    $semesterRange[] = $i;
                }

                for ($i = 1; $i <= $semester->end_month; $i++) {
                    $semesterRange[] = $i;
                }
            }

            if (in_array($currentMonth, $semesterRange)) {
                $currentSemester = $semester;
                break;
            }
        }

        return $currentSemester;
    }
}
