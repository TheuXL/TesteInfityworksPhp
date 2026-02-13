<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DisciplineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'course_id' => $this->course_id,
            'teacher_id' => $this->teacher_id,
            'course' => $this->whenLoaded('course', fn () => new CourseResource($this->course)),
            'teacher' => $this->whenLoaded('teacher', fn () => new TeacherResource($this->teacher)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
