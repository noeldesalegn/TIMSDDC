<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InterviewerAppointment;
use App\Models\User;

class InterviewerScheduleController extends Controller
{
    public function index(Request $request)
    {


        return view('interviewer.schedule');
    }

    public function events(Request $request)
    {
        $user = $request->user();
        $start = $request->query('start');
        $end = $request->query('end');

        $q = InterviewerAppointment::where('user_id', $user->id);
        if ($start && $end) {
            $q->where(function ($sub) use ($start, $end) {
                $sub->whereBetween('start_at', [$start, $end])
                    ->orWhereBetween('end_at', [$start, $end]);
            });
        }
        $events = $q->with('taxpayer')->orderBy('start_at')->get()->map(function ($a) {
            return [
                'id' => $a->id,
                'title' => $a->title,
                'start' => $a->start_at->format('Y-m-d\TH:i:s'),
                'end'   => $a->end_at->format('Y-m-d\TH:i:s'),
                'extendedProps' => [
                    'status' => $a->status,
                    'location' => $a->location,
                    'taxpayer' => $a->taxpayer ? [
                        'id' => $a->taxpayer->id,
                        'name' => $a->taxpayer->name,
                        'email' => $a->taxpayer->email,
                    ] : null,
                ],
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'notes' => ['nullable','string'],
            'start_at' => ['required','date_format:Y-m-d\TH:i:s'],
            'end_at' => ['required','date_format:Y-m-d\TH:i:s','after:start_at'],
            'taxpayer_id' => ['nullable','integer','exists:users,id'],
            'taxpayer_email' => ['nullable','email'],
            'location' => ['nullable','string','max:255'],
            'contact_phone' => ['nullable','string','max:50'],
        ]);

        $taxpayerId = $data['taxpayer_id'] ?? null;
        if (!$taxpayerId && !empty($data['taxpayer_email'])) {
            $taxpayer = User::where('email', $data['taxpayer_email'])->where('role','taxpayer')->first();
            $taxpayerId = $taxpayer?->id;
        }

        $appointment = InterviewerAppointment::create([
            'user_id' => $user->id,
            'interviewer_id' => $user->id,
            'taxpayer_id' => $taxpayerId,
            'title' => $data['title'],
            'notes' => $data['notes'] ?? null,
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
            'status' => 'scheduled',
            'location' => $data['location'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'appointment' => $appointment
        ]);
    }

    public function update(Request $request, InterviewerAppointment $appointment)
    {
        abort_if($request->user()->id !== $appointment->user_id, 403);

        $data = $request->validate([
            'title' => ['sometimes','string','max:255'],
            'notes' => ['sometimes','nullable','string'],
            'start_at' => ['sometimes','date'],
            'end_at' => ['sometimes','date','after:start_at'],
            'status' => ['sometimes','string','in:scheduled,cancelled,completed'],
            'location' => ['sometimes','nullable','string','max:255'],
            'contact_phone' => ['sometimes','nullable','string','max:50'],
        ]);

        $appointment->fill($data)->save();
        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, InterviewerAppointment $appointment)
    {
        abort_if($request->user()->id !== $appointment->user_id, 403);
        $appointment->delete();
        return response()->json(['ok' => true]);
    }
}
