<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Addon
 *
 * @property int $id
 * @property string $name
 * @property float $price Daily price
 * @property int $feature_id
 * @property int $status 0 => Inactive, 1 => Active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AddonSubscription> $addon_subscription
 * @property-read int|null $addon_subscription_count
 * @property-read \App\Models\Feature $feature
 * @method static \Illuminate\Database\Eloquent\Builder|Addon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Addon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Addon onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Addon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereFeatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addon withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Addon withoutTrashed()
 */
	class Addon extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AddonSubscription
 *
 * @property int $id
 * @property int $school_id
 * @property int $feature_id
 * @property float $price
 * @property string $start_date
 * @property string $end_date
 * @property int $status 0 => Discontinue next billing, 1 => Continue
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Addon|null $addon
 * @property-read \App\Models\Feature $feature
 * @property-read mixed $days
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription owner()
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription whereFeatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AddonSubscription withoutTrashed()
 */
	class AddonSubscription extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Announcement
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $session_year_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AnnouncementClass> $announcement_class
 * @property-read int|null $announcement_class_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $file
 * @property-read int|null $file_count
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement subjectTeacher()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereUpdatedAt($value)
 */
	class Announcement extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AnnouncementClass
 *
 * @property int $id
 * @property int|null $announcement_id
 * @property int|null $class_section_id
 * @property int|null $class_subject_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSection|null $class_section
 * @property-read \App\Models\ClassSubject|null $class_subject
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementClass owner()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementClass query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementClass whereAnnouncementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementClass whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementClass whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementClass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementClass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementClass whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnouncementClass whereUpdatedAt($value)
 */
	class AnnouncementClass extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Assignment
 *
 * @mixin Builder
 * @property int $id
 * @property int $class_section_id
 * @property int $class_subject_id
 * @property string $name
 * @property string|null $instructions
 * @property string $due_date
 * @property int|null $points
 * @property int $resubmission
 * @property int|null $extra_days_for_resubmission
 * @property int $session_year_id
 * @property int $school_id
 * @property \App\Models\User $created_by teacher_user_id
 * @property int|null $edited_by teacher_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\ClassSubject $class_subject
 * @property-read \App\Models\User $editec
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $file
 * @property-read int|null $file_count
 * @property-read mixed $created_by_teacher
 * @property-read mixed $edited_by_teacher
 * @property-read \App\Models\AssignmentSubmission|null $submission
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment assignmentTeachers()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereEditedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereExtraDaysForResubmission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereResubmission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereUpdatedAt($value)
 */
	class Assignment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AssignmentSubmission
 *
 * @property int $id
 * @property int $assignment_id
 * @property int $student_id
 * @property int $session_year_id
 * @property string|null $feedback
 * @property int|null $points
 * @property int $status 0 = Pending/In Review , 1 = Accepted , 2 = Rejected , 3 = Resubmitted
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Assignment $assignment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $file
 * @property-read int|null $file_count
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\User $student
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission owner()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereUpdatedAt($value)
 */
	class AssignmentSubmission extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Attendance
 *
 * @property int $id
 * @property int $class_section_id
 * @property int $student_id user_id
 * @property int $session_year_id
 * @property int $type 0=Absent, 1=Present
 * @property string $date
 * @property string $remark
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereUpdatedAt($value)
 */
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ClassSchool
 *
 * @property int $id
 * @property string $name
 * @property int $include_semesters 0 - no 1 - yes
 * @property int $medium_id
 * @property int|null $shift_id
 * @property int|null $stream_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $all_subjects
 * @property-read int|null $all_subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcement
 * @property-read int|null $announcement_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSection> $class_sections
 * @property-read int|null $class_sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassTeacher> $class_teachers
 * @property-read int|null $class_teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $core_subjects
 * @property-read int|null $core_subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ElectiveSubjectGroup> $elective_subject_groups
 * @property-read int|null $elective_subject_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $elective_subjects
 * @property-read int|null $elective_subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesClassType> $fees_class
 * @property-read int|null $fees_class_count
 * @property-read mixed $full_name
 * @property-read \App\Models\Mediums $medium
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @property-read \App\Models\Shift|null $shift
 * @property-read \App\Models\Stream|null $stream
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubjectTeacher> $subject_teachers
 * @property-read int|null $subject_teachers_count
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereIncludeSemesters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereStreamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool withoutTrashed()
 */
	class ClassSchool extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ClassSection
 *
 * @property int $id
 * @property int $class_id
 * @property int $section_id
 * @property int $medium_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcement
 * @property-read int|null $announcement_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \App\Models\ClassSchool $class
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassTeacher> $class_teachers
 * @property-read int|null $class_teachers_count
 * @property-read mixed $full_name
 * @property-read mixed $name
 * @property-read \App\Models\Mediums $medium
 * @property-read \App\Models\Section $section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $students
 * @property-read int|null $students_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubjectTeacher> $subject_teachers
 * @property-read int|null $subject_teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $subjects
 * @property-read int|null $subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Timetable> $timetable
 * @property-read int|null $timetable_count
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection classTeacher()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection withoutTrashed()
 */
	class ClassSection extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ClassSubject
 *
 * @property int $id
 * @property int $class_id
 * @property int $subject_id
 * @property string $type Compulsory / Elective
 * @property int|null $elective_subject_group_id if type=Elective
 * @property int|null $semester_id
 * @property int|null $virtual_semester_id
 * @property int $school_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\Semester|null $semester
 * @property-read \App\Models\Subject $subject
 * @property-read \App\Models\ElectiveSubjectGroup|null $subjectGroup
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubjectTeacher> $subjectTeachers
 * @property-read int|null $subject_teachers_count
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject currentSemesterData()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject subjectTeacher($class_section_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereElectiveSubjectGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereVirtualSemesterId($value)
 */
	class ClassSubject extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ClassTeacher
 *
 * @property int $id
 * @property int $class_section_id
 * @property int $teacher_id user_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read mixed $class_id
 * @property-read \App\Models\User $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|ClassTeacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassTeacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassTeacher owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassTeacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassTeacher whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassTeacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassTeacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassTeacher whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassTeacher whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassTeacher whereUpdatedAt($value)
 */
	class ClassTeacher extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CompulsoryFee
 *
 * @property int $id
 * @property int $student_id user_id
 * @property int|null $payment_transaction_id
 * @property string $type
 * @property int|null $installment_id
 * @property string $mode
 * @property string|null $cheque_no
 * @property float $amount
 * @property float|null $due_charges
 * @property int|null $fees_paid_id
 * @property string $status
 * @property string $date
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesAdvance> $advance_fees
 * @property-read int|null $advance_fees_count
 * @property-read \App\Models\FeesPaid|null $fees_paid
 * @property-read mixed $mode_name
 * @property-read \App\Models\FeesInstallment|null $installment_fee
 * @property-read \App\Models\User $student
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee owner()
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereChequeNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereDueCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereFeesPaidId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereInstallmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee wherePaymentTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CompulsoryFee withoutTrashed()
 */
	class CompulsoryFee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ElectiveSubjectGroup
 *
 * @property int $id
 * @property int $total_subjects
 * @property int $total_selectable_subjects
 * @property int $class_id
 * @property int|null $semester_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $subjects
 * @property-read int|null $subjects_count
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereTotalSelectableSubjects($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereTotalSubjects($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereUpdatedAt($value)
 */
	class ElectiveSubjectGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Exam
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $class_id
 * @property int $session_year_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int $publish
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read mixed $exam_status
 * @property-read mixed $exam_status_name
 * @property-read mixed $has_timetable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamMarks> $marks
 * @property-read int|null $marks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamResult> $results
 * @property-read int|null $results_count
 * @property-read \App\Models\Semester $semester
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamTimetable> $timetable
 * @property-read int|null $timetable_count
 * @method static \Illuminate\Database\Eloquent\Builder|Exam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam query()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam wherePublish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam withoutTrashed()
 */
	class Exam extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamMarks
 *
 * @property int $id
 * @property int $exam_timetable_id
 * @property int $student_id user_id
 * @property int $class_subject_id
 * @property float $obtained_marks
 * @property string|null $teacher_review
 * @property int $passing_status 1=Pass, 0=Fail
 * @property int $session_year_id
 * @property string|null $grade
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ClassSubject $class_subject
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $subject
 * @property-read int|null $subject_count
 * @property-read \App\Models\ExamTimetable $timetable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereExamTimetableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereObtainedMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks wherePassingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereTeacherReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereUpdatedAt($value)
 */
	class ExamMarks extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamResult
 *
 * @property int $id
 * @property int $exam_id
 * @property int $class_section_id
 * @property int $student_id user_id
 * @property int $total_marks
 * @property float $obtained_marks
 * @property float $percentage
 * @property string $grade
 * @property int $session_year_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Exam $exam
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereObtainedMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereTotalMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereUpdatedAt($value)
 */
	class ExamResult extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamTimetable
 *
 * @property int $id
 * @property int $exam_id
 * @property int $class_subject_id
 * @property float $total_marks
 * @property float $passing_marks
 * @property string $date
 * @property string $start_time
 * @property string $end_time
 * @property int $session_year_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\ClassSubject $class_subject
 * @property-read \App\Models\Exam $exam
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamMarks> $exam_marks
 * @property-read int|null $exam_marks_count
 * @property-read mixed $subject_with_name
 * @property-read \App\Models\SessionYear $session_year
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable wherePassingMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereTotalMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereUpdatedAt($value)
 */
	class ExamTimetable extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Expense
 *
 * @property int $id
 * @property int|null $category_id
 * @property string|null $ref_no
 * @property int|null $staff_id
 * @property int $basic_salary
 * @property float $paid_leaves
 * @property int|null $month
 * @property int|null $year
 * @property string $title
 * @property string|null $description
 * @property float $amount
 * @property string $date
 * @property int $school_id
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExpenseCategory|null $category
 * @method static \Illuminate\Database\Eloquent\Builder|Expense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense query()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereBasicSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePaidLeaves($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereRefNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereYear($value)
 */
	class Expense extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExpenseCategory
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Expense> $expense
 * @property-read int|null $expense_count
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpenseCategory withoutTrashed()
 */
	class ExpenseCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExtraStudentData
 *
 * @property int $id
 * @property int $student_id user_id
 * @property int $form_field_id
 * @property string|null $data
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\FormField $form_field
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData whereFormFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraStudentData withoutTrashed()
 */
	class ExtraStudentData extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Faq
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Faq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq query()
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faq whereUpdatedAt($value)
 */
	class Faq extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Feature
 *
 * @property int $id
 * @property string $name
 * @property int $is_default 0 => No, 1 => Yes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereUpdatedAt($value)
 */
	class Feature extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Fee
 *
 * @property int $id
 * @property string $name
 * @property string $due_date
 * @property float $due_charges in percentage (%)
 * @property int $class_id
 * @property int $school_id
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesClassType> $fees_class_type
 * @property-read int|null $fees_class_type_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesPaid> $fees_paid
 * @property-read int|null $fees_paid_count
 * @property-read mixed $compulsory_fees
 * @property-read mixed $include_fee_installments
 * @property-read mixed $optional_fees
 * @property-read mixed $total_compulsory_fees
 * @property-read mixed $total_optional_fees
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesInstallment> $installments
 * @property-read int|null $installments_count
 * @property-read \App\Models\SessionYear $session_year
 * @method static \Illuminate\Database\Eloquent\Builder|Fee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereDueCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee withoutTrashed()
 */
	class Fee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FeesAdvance
 *
 * @property int $id
 * @property int $compulsory_fee_id
 * @property int $student_id user_id
 * @property int $parent_id user_id
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FeesAdvance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesAdvance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesAdvance query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesAdvance whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesAdvance whereCompulsoryFeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesAdvance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesAdvance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesAdvance whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesAdvance whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesAdvance whereUpdatedAt($value)
 */
	class FeesAdvance extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FeesClassType
 *
 * @property int $id
 * @property int $class_id
 * @property int $fees_id
 * @property int $fees_type_id
 * @property float $amount
 * @property int $optional 0 - No, 1 - Yes
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\FeesType $fees_type
 * @property-read mixed $fees_type_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OptionalFee> $optional_fees_paid
 * @property-read int|null $optional_fees_paid_count
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClassType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClassType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClassType owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClassType query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClassType whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClassType whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClassType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClassType whereFeesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClassType whereFeesTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClassType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClassType whereOptional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClassType whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClassType whereUpdatedAt($value)
 */
	class FeesClassType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FeesInstallment
 *
 * @property int $id
 * @property string $name
 * @property string $due_date
 * @property int $due_charges in percentage (%)
 * @property int $fees_id
 * @property int $session_year_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CompulsoryFee> $compulsory_fees
 * @property-read int|null $compulsory_fees_count
 * @property-read \App\Models\SessionYear $session_year
 * @method static \Illuminate\Database\Eloquent\Builder|FeesInstallment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesInstallment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesInstallment owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesInstallment query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesInstallment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesInstallment whereDueCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesInstallment whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesInstallment whereFeesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesInstallment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesInstallment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesInstallment whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesInstallment whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesInstallment whereUpdatedAt($value)
 */
	class FeesInstallment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FeesPaid
 *
 * @property int $id
 * @property int $fees_id
 * @property int $student_id user_id
 * @property int $is_fully_paid 0 - No, 1 - Yes
 * @property int $is_used_installment 0 - No, 1 - Yes
 * @property float $amount
 * @property string $date
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CompulsoryFee> $compulsory_fee
 * @property-read int|null $compulsory_fee_count
 * @property-read \App\Models\Fee $fees
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OptionalFee> $optional_fee
 * @property-read int|null $optional_fee_count
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\User $student
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereFeesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereIsFullyPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereIsUsedInstallment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid withoutTrashed()
 */
	class FeesPaid extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FeesType
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesClassType> $fees_class
 * @property-read int|null $fees_class_count
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType withoutTrashed()
 */
	class FeesType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\File
 *
 * @property int $id
 * @property string $modal_type
 * @property int $modal_id
 * @property string|null $file_name
 * @property string|null $file_thumbnail
 * @property string $type 1 = File Upload, 2 = Youtube Link, 3 = Video Upload, 4 = Other Link
 * @property string $file_url
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read mixed $file_extension
 * @property-read mixed $type_detail
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $modal
 * @method static \Illuminate\Database\Eloquent\Builder|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File owner()
 * @method static \Illuminate\Database\Eloquent\Builder|File query()
 * @method static \Illuminate\Database\Eloquent\Builder|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFileThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereModalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereModalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUpdatedAt($value)
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FormField
 *
 * @property int $id
 * @property string $name
 * @property string $type text,number,textarea,dropdown,checkbox,radio,fileupload
 * @property int $is_required
 * @property array|mixed $default_values values of radio,checkbox,dropdown,etc
 * @property string|null $other extra HTML attributes
 * @property int $school_id
 * @property int $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\School $school
 * @method static \Illuminate\Database\Eloquent\Builder|FormField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereDefaultValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField withoutTrashed()
 */
	class FormField extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Grade
 *
 * @property int $id
 * @property float $starting_range
 * @property float $ending_range
 * @property string $grade
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereEndingRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereStartingRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereUpdatedAt($value)
 */
	class Grade extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Holiday
 *
 * @property int $id
 * @property string $date
 * @property string $title
 * @property string|null $description
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $default_date_format
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday query()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereUpdatedAt($value)
 */
	class Holiday extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Language
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $file
 * @property int $status 1=>active
 * @property int $is_rtl
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereIsRtl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereUpdatedAt($value)
 */
	class Language extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Leave
 *
 * @property int $id
 * @property int $user_id
 * @property string $reason
 * @property string $from_date
 * @property string $to_date
 * @property int $status 0 => Pending, 1 => Approved, 2 => Rejected
 * @property int $school_id
 * @property int $leave_master_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LeaveDetail> $leave_detail
 * @property-read int|null $leave_detail_count
 * @property-read \App\Models\LeaveMaster $leave_master
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Leave newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Leave newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Leave owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Leave query()
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereFromDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereLeaveMasterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereToDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereUserId($value)
 */
	class Leave extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LeaveDetail
 *
 * @property int $id
 * @property int $leave_id
 * @property string $date
 * @property string $type
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $leave_date
 * @property-read \App\Models\Leave $leave
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveDetail owner()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveDetail whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveDetail whereLeaveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveDetail whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveDetail whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveDetail whereUpdatedAt($value)
 */
	class LeaveDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LeaveMaster
 *
 * @property int $id
 * @property float $leaves Leaves per month
 * @property string $holiday
 * @property int $session_year_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Leave> $leave
 * @property-read int|null $leave_count
 * @property-read \App\Models\School $school
 * @property-read \App\Models\SessionYear $session_year
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveMaster newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveMaster newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveMaster owner()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveMaster query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveMaster whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveMaster whereHoliday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveMaster whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveMaster whereLeaves($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveMaster whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveMaster whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveMaster whereUpdatedAt($value)
 */
	class LeaveMaster extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Lesson
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $class_section_id
 * @property int $class_subject_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\ClassSubject $class_subject
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $file
 * @property-read int|null $file_count
 * @property-read mixed $class_section_with_medium
 * @property-read mixed $subject_with_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LessonTopic> $topic
 * @property-read int|null $topic_count
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson query()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereUpdatedAt($value)
 */
	class Lesson extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LessonTopic
 *
 * @property int $id
 * @property int $lesson_id
 * @property string $name
 * @property string|null $description
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $file
 * @property-read int|null $file_count
 * @property-read \App\Models\Lesson $lesson
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic owner()
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic query()
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereUpdatedAt($value)
 */
	class LessonTopic extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Mediums
 *
 * @property int $id
 * @property string $name
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums withoutTrashed()
 */
	class Mediums extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OnlineExam
 *
 * @property int $id
 * @property int $class_section_id
 * @property int $class_subject_id
 * @property string $title
 * @property int $exam_key
 * @property int $duration in minutes
 * @property string $start_date
 * @property string $end_date
 * @property int $session_year_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\ClassSubject $class_subject
 * @property-read mixed $class_section_with_medium
 * @property-read mixed $exam_status_name
 * @property-read mixed $subject_with_name
 * @property-read mixed $total_marks
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OnlineExamQuestionChoice> $question_choice
 * @property-read int|null $question_choice_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OnlineExamStudentAnswer> $student_answers
 * @property-read int|null $student_answers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StudentOnlineExamStatus> $student_attempt
 * @property-read int|null $student_attempt_count
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam currentSemesterData()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam owner()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereExamKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam withoutTrashed()
 */
	class OnlineExam extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OnlineExamQuestion
 *
 * @property int $id
 * @property int $class_section_id
 * @property int $class_subject_id
 * @property string $question
 * @property string|null $image_url
 * @property string|null $note
 * @property int $school_id
 * @property int $last_edited_by teacher_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\ClassSubject $class_subject
 * @property-read mixed $class_section_with_medium
 * @property-read mixed $subject_with_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OnlineExamQuestionOption> $options
 * @property-read int|null $options_count
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion currentSemesterData()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion owner()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereLastEditedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereUpdatedAt($value)
 */
	class OnlineExamQuestion extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OnlineExamQuestionChoice
 *
 * @property int $id
 * @property int $online_exam_id
 * @property int $question_id
 * @property int|null $marks
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\OnlineExam $online_exam
 * @property-read \App\Models\OnlineExamQuestion $questions
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice owner()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereOnlineExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereUpdatedAt($value)
 */
	class OnlineExamQuestionChoice extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OnlineExamQuestionOption
 *
 * @property int $id
 * @property int $question_id
 * @property string $option
 * @property int $is_answer 1 - yes, 0 - no
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption owner()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereIsAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereUpdatedAt($value)
 */
	class OnlineExamQuestionOption extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OnlineExamStudentAnswer
 *
 * @property int $id
 * @property int $student_id user_id
 * @property int $online_exam_id
 * @property int $question_id
 * @property int $option_id
 * @property string $submitted_date
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\OnlineExam $online_exam
 * @property-read \App\Models\OnlineExamQuestionChoice $user_submitted_questions
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer owner()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereOnlineExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereSubmittedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereUpdatedAt($value)
 */
	class OnlineExamStudentAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OptionalFee
 *
 * @property int $id
 * @property int $student_id user_id
 * @property int $class_id
 * @property int|null $payment_transaction_id
 * @property int|null $fees_class_id
 * @property string $mode
 * @property string|null $cheque_no
 * @property float $amount
 * @property int|null $fees_paid_id
 * @property string $date
 * @property int $school_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\FeesClassType|null $fees_class_type
 * @property-read \App\Models\FeesPaid|null $fees_paid
 * @property-read mixed $mode_name
 * @property-read \App\Models\User $student
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee owner()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee query()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereChequeNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereFeesClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereFeesPaidId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee wherePaymentTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalFee withoutTrashed()
 */
	class OptionalFee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Package
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $tagline
 * @property float $student_charge
 * @property float $staff_charge
 * @property int $status 0 => Unpublished, 1 => Published
 * @property int $is_trial
 * @property int $highlight 0 => No, 1 => Yes
 * @property int $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PackageFeature> $package_feature
 * @property-read int|null $package_feature_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscription
 * @property-read int|null $subscription_count
 * @method static \Illuminate\Database\Eloquent\Builder|Package newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Package query()
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereHighlight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereIsTrial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereStaffCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereStudentCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereTagline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Package withoutTrashed()
 */
	class Package extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PackageFeature
 *
 * @property int $id
 * @property int $package_id
 * @property int $feature_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Feature $feature
 * @method static \Illuminate\Database\Eloquent\Builder|PackageFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageFeature whereFeatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageFeature wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageFeature whereUpdatedAt($value)
 */
	class PackageFeature extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentConfiguration
 *
 * @property int $id
 * @property string $payment_method
 * @property string $api_key
 * @property string $secret_key
 * @property string $webhook_secret_key
 * @property string|null $currency_code
 * @property int $status 0 - Disabled, 1 - Enabled
 * @property int|null $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration owner()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration whereSecretKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentConfiguration whereWebhookSecretKey($value)
 */
	class PaymentConfiguration extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentTransaction
 *
 * @property int $id
 * @property int $user_id
 * @property float $amount
 * @property string $payment_gateway
 * @property string|null $order_id order_id / payment_intent_id
 * @property string|null $payment_id
 * @property string|null $payment_signature
 * @property string $payment_status
 * @property int|null $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\School|null $school
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction owner()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction wherePaymentGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction wherePaymentSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereUserId($value)
 */
	class PaymentTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUpdatedAt($value)
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PromoteStudent
 *
 * @property int $id
 * @property int $student_id user_id
 * @property int $class_section_id
 * @property int $session_year_id
 * @property int $result 1=>Pass,0=>fail
 * @property int $status 1=>continue,0=>leave
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent owner()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudent whereUpdatedAt($value)
 */
	class PromoteStudent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property int|null $school_id
 * @property int $custom_role
 * @property int $editable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCustomRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\School
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string $support_phone
 * @property string $support_email
 * @property string $tagline
 * @property string $logo
 * @property int|null $admin_id user_id
 * @property int $status 0 => Deactivate, 1 => Active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feature> $addon
 * @property-read int|null $addon_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubscriptionFeature> $features
 * @property-read int|null $features_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscription
 * @property-read int|null $subscription_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|School newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|School newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|School onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|School query()
 * @method static \Illuminate\Database\Eloquent\Builder|School whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereSupportEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereSupportPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereTagline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|School withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|School withoutTrashed()
 */
	class School extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SchoolSetting
 *
 * @property int $id
 * @property string $name
 * @property string $data
 * @property string|null $type datatype like string , file etc
 * @property int $school_id
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSetting owner()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSetting whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSetting whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSetting whereType($value)
 */
	class SchoolSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Section
 *
 * @property int $id
 * @property string $name
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSchool> $classes
 * @property-read int|null $classes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Section owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Section withoutTrashed()
 */
	class Section extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Semester
 *
 * @property int $id
 * @property string $name
 * @property int $start_month
 * @property int $end_month
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSubject> $class_subjects
 * @property-read int|null $class_subjects_count
 * @property-read mixed $current
 * @property-read mixed $end_month_name
 * @property-read mixed $start_month_name
 * @method static \Illuminate\Database\Eloquent\Builder|Semester newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Semester newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Semester onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Semester owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Semester query()
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereEndMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereStartMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Semester withoutTrashed()
 */
	class Semester extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SessionYear
 *
 * @property int $id
 * @property string $name
 * @property int $default
 * @property string $start_date
 * @property string $end_date
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesInstallment> $fee_installments
 * @property-read int|null $fee_installments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Semester> $semesters
 * @property-read int|null $semesters_count
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear owner()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear query()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear withoutTrashed()
 */
	class SessionYear extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Shift
 *
 * @property int $id
 * @property string $name
 * @property string $start_time
 * @property string $end_time
 * @property int $status
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift query()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift withoutTrashed()
 */
	class Shift extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Slider
 *
 * @property int $id
 * @property string $image
 * @property string|null $link
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider query()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereUpdatedAt($value)
 */
	class Slider extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Staff
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $qualification
 * @property float $salary
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcement
 * @property-read int|null $announcement_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Expense> $expense
 * @property-read int|null $expense_count
 * @property-read mixed $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Leave> $leave
 * @property-read int|null $leave_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Staff newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Staff newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Staff query()
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereQualification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereUserId($value)
 */
	class Staff extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\StaffSupportSchool
 *
 * @property int $id
 * @property int $user_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\School $school
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|StaffSupportSchool newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StaffSupportSchool newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StaffSupportSchool owner()
 * @method static \Illuminate\Database\Eloquent\Builder|StaffSupportSchool query()
 * @method static \Illuminate\Database\Eloquent\Builder|StaffSupportSchool whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffSupportSchool whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffSupportSchool whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffSupportSchool whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffSupportSchool whereUserId($value)
 */
	class StaffSupportSchool extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Stream
 *
 * @property int $id
 * @property string $name
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Stream newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream query()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream withoutTrashed()
 */
	class Stream extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\StudentOnlineExamStatus
 *
 * @property int $id
 * @property int $student_id user_id
 * @property int $online_exam_id
 * @property int $status 1 - in progress 2 - completed
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\OnlineExam $online_exam
 * @property-read \App\Models\User $student_data
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus owner()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereOnlineExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereUpdatedAt($value)
 */
	class StudentOnlineExamStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\StudentSubject
 *
 * @property int $id
 * @property int $student_id user_id
 * @property int $class_subject_id
 * @property int $class_section_id
 * @property int $session_year_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ClassSubject $class_subject
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject owner()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereUpdatedAt($value)
 */
	class StudentSubject extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Students
 *
 * @property int $id
 * @property int $user_id
 * @property int $class_section_id
 * @property string $admission_no
 * @property int|null $roll_number
 * @property string $admission_date
 * @property int $school_id
 * @property int $guardian_id
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcement
 * @property-read int|null $announcement_count
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesPaid> $fees_paid
 * @property-read int|null $fees_paid_count
 * @property-read mixed $first_name
 * @property-read mixed $full_name
 * @property-read mixed $last_name
 * @property-read \App\Models\User $guardian
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Students newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Students newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Students onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Students owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Students query()
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereAdmissionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereAdmissionNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereGuardianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereRollNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Students withoutTrashed()
 */
	class Students extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Subject
 *
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string $bg_color
 * @property string $image
 * @property int $medium_id
 * @property string $type Theory / Practical
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSubject> $class_subjects
 * @property-read int|null $class_subjects_count
 * @property-read mixed $name_with_type
 * @property-read \App\Models\Mediums $medium
 * @method static \Illuminate\Database\Eloquent\Builder|Subject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject withoutTrashed()
 */
	class Subject extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SubjectTeacher
 *
 * @property int $id
 * @property int $class_section_id
 * @property int $subject_id
 * @property int $teacher_id user_id
 * @property int $class_subject_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read mixed $subject_with_name
 * @property-read \App\Models\Semester $semester
 * @property-read \App\Models\Subject $subject
 * @property-read \App\Models\User $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher owner()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereUpdatedAt($value)
 */
	class SubjectTeacher extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Subscription
 *
 * @property int $id
 * @property int $school_id
 * @property int $package_id
 * @property string $name
 * @property float $student_charge
 * @property float $staff_charge
 * @property string $start_date
 * @property string $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feature> $features
 * @property-read int|null $features_count
 * @property-read mixed $bill_date
 * @property-read mixed $due_date
 * @property-read mixed $extra_billing_status
 * @property-read mixed $status
 * @property-read \App\Models\Package $package
 * @property-read \App\Models\School $school
 * @property-read \App\Models\SubscriptionBill|null $subscription_bill
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubscriptionFeature> $subscription_feature
 * @property-read int|null $subscription_feature_count
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStaffCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereStudentCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUpdatedAt($value)
 */
	class Subscription extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SubscriptionBill
 *
 * @property int $id
 * @property int $subscription_id
 * @property string|null $description
 * @property float $amount
 * @property int $total_student
 * @property int $total_staff
 * @property int|null $payment_transaction_id
 * @property string $due_date
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Subscription $subscription
 * @property-read \App\Models\PaymentTransaction|null $transaction
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill owner()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill wherePaymentTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill whereTotalStaff($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill whereTotalStudent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionBill whereUpdatedAt($value)
 */
	class SubscriptionBill extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SubscriptionFeature
 *
 * @property int $id
 * @property int $subscription_id
 * @property int $feature_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Feature $feature
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionFeature whereFeatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionFeature whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionFeature whereUpdatedAt($value)
 */
	class SubscriptionFeature extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SystemSetting
 *
 * @property int $id
 * @property string $name
 * @property string $data
 * @property string|null $type datatype like string , file etc
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereType($value)
 */
	class SystemSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Timetable
 *
 * @property int $id
 * @property int|null $subject_teacher_id
 * @property int $class_section_id
 * @property int|null $subject_id
 * @property string $start_time
 * @property string $end_time
 * @property string|null $note
 * @property string $day
 * @property string $type
 * @property int|null $semester_id
 * @property int $school_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read mixed $title
 * @property-read \App\Models\Subject|null $subject
 * @property-read \App\Models\SubjectTeacher|null $subject_teacher
 * @property-read \App\Models\User|null $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable currentSemesterData()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable query()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereSubjectTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereUpdatedAt($value)
 */
	class Timetable extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $mobile
 * @property string $email
 * @property string $password
 * @property string|null $gender
 * @property string|null $image
 * @property string|null $dob
 * @property string|null $current_address
 * @property string|null $permanent_address
 * @property string|null $occupation
 * @property int $status
 * @property int $reset_request
 * @property string|null $fcm_id
 * @property int|null $school_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $child
 * @property-read int|null $child_count
 * @property-read \App\Models\ClassSection|null $class_section_teacher
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CompulsoryFee> $compulsory_fees
 * @property-read int|null $compulsory_fees_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamMarks> $exam_marks
 * @property-read int|null $exam_marks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamResult> $exam_result
 * @property-read int|null $exam_result_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExtraStudentData> $extra_student_details
 * @property-read int|null $extra_student_details_count
 * @property-read \App\Models\FeesPaid|null $fees_paid
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesPaid> $fees_paids
 * @property-read int|null $fees_paids_count
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $guardianRelationChild
 * @property-read int|null $guardian_relation_child_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StudentOnlineExamStatus> $online_exam_attempts
 * @property-read int|null $online_exam_attempts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OptionalFee> $optional_fees
 * @property-read int|null $optional_fees_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\School|null $school
 * @property-read \App\Models\Staff|null $staff
 * @property-read \App\Models\Students|null $student
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubjectTeacher> $subjectTeachers
 * @property-read int|null $subject_teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StaffSupportSchool> $support_school
 * @property-read int|null $support_school_count
 * @property-read \App\Models\Staff|null $teacher
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Timetable> $timetable
 * @property-read int|null $timetable_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\UserStatusForNextCycle|null $user_status
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User owner()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFcmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePermanentAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereResetRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserStatusForNextCycle
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatusForNextCycle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatusForNextCycle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatusForNextCycle owner()
 * @method static \Illuminate\Database\Eloquent\Builder|UserStatusForNextCycle query()
 */
	class UserStatusForNextCycle extends \Eloquent {}
}

