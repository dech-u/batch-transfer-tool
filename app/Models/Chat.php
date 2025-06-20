<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $fillable = ['sender_id','receiver_id'];
    protected $appends = ['last_message','user'];


    /**
     * Get all of the message for the Chat
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function message()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the receiver that owns the Chat
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the receiver that owns the Chat
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getLastMessageAttribute()
    {
        
        $message = Message::where('chat_id', $this->id)->latest()->first();
        if ($message) {
            return $message->message;
        }
        
        return '';
    }

    public function getUserAttribute()
    {
        $user = null;
        $role = request('role');
        if ($this->sender_id == Auth::user()->id) {
            $user = User::select('id', 'first_name', 'last_name', 'image')->with('roles')->where('id',$this->receiver_id)
            ->with('subjectTeachers.subject','class_teacher.class_section.class','class_teacher.class_section.section','class_teacher.class_section.medium');
            if ($role != 'Staff') {
                $user = $user->role($role);
            }
            $user = $user->first();
        } else {
            $user = User::select('id', 'first_name', 'last_name', 'image')->with('roles')->where('id',$this->sender_id)
            ->with('subjectTeachers.subject','class_teacher.class_section.class','class_teacher.class_section.section','class_teacher.class_section.medium');
            if ($role != 'Staff') {
                $user = $user->role($role);
            }
            $user = $user->first();
        }
        return $user;
    }
}
