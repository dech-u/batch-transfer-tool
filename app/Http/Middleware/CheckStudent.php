<?php

namespace App\Http\Middleware;

use App\Models\ClassTeacher;
use App\Models\Staff;
use App\Models\Students;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckStudent {
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next) {
        $user_id = $request->user()->id;
        $class_section_id = ClassTeacher::where('teacher_id', $user_id)->pluck('class_section_id')->toArray();
        $student_class_section_id = Students::where('user_id', $request->student_id)->pluck('class_section_id')->first();
        if (!in_array($student_class_section_id, $class_section_id)) {
            return response()->json(array(
                'error'   => true,
                'message' => "Invalid Student ID Passed.",
                'code'    => 105,
            ));
        }
        return $next($request);
    }
}
