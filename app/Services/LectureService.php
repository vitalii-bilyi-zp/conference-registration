<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

use App\Models\Conference;

use Carbon\Carbon;

class LectureService
{
    const MAX_DURATION = 60;

    public function validateLectureTime($conferenceId, $lectureStart, $lectureEnd)
    {
        $conference = Conference::find($conferenceId);

        $error = $this->validateLectureStart($conference, $lectureStart);
        if (!isset($error)) {
            $error = $this->validateLectureEnd($conference, $lectureEnd);
        }
        if (!isset($error)) {
            $error = $this->validateLectureDuration($lectureStart, $lectureEnd);
        }
        if (!isset($error)) {
            $error = $this->validateLectureTimeConflicts($conference, $lectureStart, $lectureEnd);
        }

        return $error;
    }

    private function validateLectureStart(Conference $conference, $lectureStart)
    {
        $conferenceDate = Carbon::parse($conference->date)->format('Y-m-d');
        $conferenceStart = $conferenceDate . ' ' . $conference->day_start;
        $conferenceEnd = $conferenceDate . ' ' . $conference->day_end;
        $conferenceStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $conferenceStart);
        $conferenceEndDate = Carbon::createFromFormat('Y-m-d H:i:s', $conferenceEnd);
        $lectureStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $lectureStart);

        if ($lectureStartDate->lt($conferenceStartDate) || $lectureStartDate->gt($conferenceEndDate)) {
            return [
                'field' => 'lecture_start',
                'message' => trans('validation.lecture_start_time', ['start_date' => $conferenceStart, 'end_date' => $conferenceEnd])
            ];
        }

        return null;
    }

    private function validateLectureEnd(Conference $conference, $lectureEnd)
    {
        $conferenceDate = Carbon::parse($conference->date)->format('Y-m-d');
        $conferenceStart = $conferenceDate . ' ' . $conference->day_start;
        $conferenceEnd = $conferenceDate . ' ' . $conference->day_end;
        $conferenceStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $conferenceStart);
        $conferenceEndDate = Carbon::createFromFormat('Y-m-d H:i:s', $conferenceEnd);
        $lectureEndDate = Carbon::createFromFormat('Y-m-d H:i:s', $lectureEnd);

        if ($lectureEndDate->lt($conferenceStartDate) || $lectureEndDate->gt($conferenceEndDate)) {
            return [
                'field' => 'lecture_end',
                'message' => trans('validation.lecture_end_time', ['start_date' => $conferenceStart, 'end_date' => $conferenceEnd])
            ];
        }

        return null;
    }

    private function validateLectureDuration($lectureStart, $lectureEnd)
    {
        $lectureDuration = $this->getLectureDuration($lectureStart, $lectureEnd);

        if ($lectureDuration > LectureService::MAX_DURATION) {
            return [
                'field' => 'lecture_start',
                'message' => trans('validation.lecture_duration', ['duration' => LectureService::MAX_DURATION . ' minutes'])
            ];
        }

        return null;
    }

    private function validateLectureTimeConflicts(Conference $conference, $lectureStart, $lectureEnd)
    {
        $conflictLecture = $this->findLectureTimeConflict($conference, $lectureStart, $lectureEnd);
        if (!isset($conflictLecture)) {
            return null;
        }

        $conferenceDate = Carbon::parse($conference->date)->format('Y-m-d');
        $conferenceStart = $conferenceDate . ' ' . $conference->day_start;
        $conferenceEnd = $conferenceDate . ' ' . $conference->day_end;
        $lectureDuration = $this->getLectureDuration($lectureStart, $lectureEnd);

        $possibleLectureStart = $this->findPossibleLectureTime($conference, $conflictLecture->lecture_end, $conferenceEnd, $lectureDuration);
        if (!isset($possibleLectureStart)) {
            $possibleLectureStart = $this->findPossibleLectureTimeReverse($conference, $conferenceStart, $conflictLecture->lecture_start, $lectureDuration);
        }

        return [
            'field' => 'lecture_start',
            'message' => trans('validation.lecture_time_conflicts', ['possible_lecture_start' => $possibleLectureStart ?? '-'])
        ];
    }

    private function getLectureDuration($lectureStart, $lectureEnd)
    {
        $lectureStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $lectureStart);
        $lectureEndDate = Carbon::createFromFormat('Y-m-d H:i:s', $lectureEnd);

        return $lectureEndDate->diffInMinutes($lectureStartDate);
    }

    private function findLectureTimeConflict(Conference $conference, $lectureStart, $lectureEnd)
    {
        return $conference->lectures()
            ->where([
                ['lecture_start', '<=', $lectureStart],
                ['lecture_end', '>', $lectureStart],
            ])
            ->orWhere([
                ['lecture_start', '<', $lectureEnd],
                ['lecture_end', '>=', $lectureEnd],
            ])
            ->first();
    }

    private function findPossibleLectureTime(Conference $conference, $periodStart, $periodEnd, $lectureDuration)
    {
        $lectureStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $periodStart);
        $lectureEndDate = $lectureStartDate->copy()->addMinutes($lectureDuration);
        $periodEndDate = Carbon::createFromFormat('Y-m-d H:i:s', $periodEnd);

        while ($lectureEndDate->lte($periodEndDate)) {
            $conflictLecture = $this->findLectureTimeConflict(
                $conference,
                $lectureStartDate->format('Y-m-d H:i:s'),
                $lectureEndDate->format('Y-m-d H:i:s')
            );
            if (isset($conflictLecture)) {
                $lectureStartDate = $lectureEndDate;
                $lectureEndDate = $lectureStartDate->copy()->addMinutes($lectureDuration);
            } else {
                return $lectureStartDate->format('Y-m-d H:i:s');
            }
        }

        return null;
    }

    private function findPossibleLectureTimeReverse(Conference $conference, $periodStart, $periodEnd, $lectureDuration)
    {
        $lectureEndDate = Carbon::createFromFormat('Y-m-d H:i:s', $periodEnd);
        $lectureStartDate = $lectureEndDate->copy()->subMinutes($lectureDuration);
        $periodStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $periodStart);

        while ($lectureStartDate->gte($periodStartDate)) {
            $conflictLecture = $this->findLectureTimeConflict(
                $conference,
                $lectureStartDate->format('Y-m-d H:i:s'),
                $lectureEndDate->format('Y-m-d H:i:s')
            );
            if (isset($conflictLecture)) {
                $lectureEndDate = $lectureStartDate;
                $lectureStartDate = $lectureEndDate->copy()->subMinutes($lectureDuration);
            } else {
                return $lectureStartDate->format('Y-m-d H:i:s');
            }
        }

        return null;
    }

    public function storePresentation($file)
    {
        $hashFileName = null;

        if (isset($file)) {
            $filePath = $file->store('/', 'presentations');

            if (isset($filePath)) {
                $hashFileName = basename($filePath);
            }
        }

        return $hashFileName;
    }

    public function getPresentationPath($fileName)
    {
        if (!isset($fileName)) {
            return null;
        }

        return Storage::disk('presentations')->url($fileName);
    }

    public function deletePresentation($fileName)
    {
        if (!isset($fileName)) {
            return;
        }

        Storage::disk('presentations')->delete($fileName);
    }
}
