<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->hasRole('Guardian')){
            return array(
                'id'                => $this->id,
                'first_name'        => $this->first_name,
                'last_name'         => $this->last_name,
                'email'             => $this->email,
                'mobile'            => $this->mobile,
                'gender'            => $this->gender,
                'image'             => $this->image,
                'dob'               => $this->dob,
                'current_address'   => $this->current_address,
                'permanent_address' => $this->permanent_address,
                'occupation'        => $this->occupation,
                'status'            => $this->status,
                'fcm_id'            => $this->fcm_id,
                'email_verified_at' => $this->email_verified_at,
                'created_at'        => $this->created_at,
                'updated_at'        => $this->updated_at,
                'children'          => $this->child

            );
        }else{
            return array(
                'id'                => $this->id,
                'first_name'        => $this->first_name,
                'last_name'         => $this->last_name,
                'mobile'            => $this->mobile,
                'roll_number'       => $this->student->roll_number,
                'admission_no'      => $this->student->admission_no,
                'admission_date'    => $this->student->admission_date,
                'gender'            => $this->gender,
                'image'             => $this->image,
                'dob'               => $this->dob,
                'current_address'   => $this->current_address,
                'permanent_address' => $this->permanent_address,
                'occupation'        => $this->occupation,
                'status'            => $this->status,
                'fcm_id'            => $this->fcm_id,
                'school_id'         => $this->school_id,
                'session_year_id'   => $this->student->session_year_id,
                'email_verified_at' => $this->email_verified_at,
                'created_at'        => $this->created_at,
                'updated_at'        => $this->updated_at,
                'class_section'     => $this->student->class_section,
                'guardian'          => $this->student->guardian,
                'school'            => $this->school,
            );
        }
    }
}
