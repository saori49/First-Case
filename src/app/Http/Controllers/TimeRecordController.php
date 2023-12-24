<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeRecord;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class TimeRecordController extends Controller
{
  //home表示
    public function showHome()
    {
        return view('home');
    }

  //打刻処理
    public function startWork(Request $request)
    {
        $date = $request->input('date');
        $existingRecord = TimeRecord::where('user_id', auth()->user()->id)
            ->whereDate('date', now())
            ->first();

        if ($existingRecord) {
            Session::flash('error', '本日は既に勤務を開始しています。');
        } else {
            TimeRecord::create([
                'user_id'    => auth()->user()->id,
                'start_time' => now()->toTimeString(),
                'date'       => now()->toDateString(),
            ]);
            Session::flash('success', '勤務を開始しました。');
        }

        return redirect()->back();
    }

    public function endWork(Request $request)
    {
        $record = TimeRecord::where('user_id', auth()->user()->id)
            ->whereNull('end_time')
            ->first();

        if ($record) {
            $record->update([
                'end_time' => now()->toTimeString(),
                ]);
                Session::flash('success', '勤務を終了しました。');
            } else {
            Session::flash('error', '本日はまだ勤務を開始していません。');
        }

        return redirect()->back();
    }

    public function startBreak(Request $request)
    {
        TimeRecord::create([
            'user_id'     => auth()->user()->id,
            'break_start' => now()->toTimeString(),
            'date'        => now()->toDateString(),
        ]);

        Session::flash('success', '休憩を開始しました。');

        return redirect()->back();
    }

    public function endBreak(Request $request)
    {
        $record = TimeRecord::where('user_id', auth()->user()->id)
            ->whereNotNull('break_start')
            ->whereNull('break_end')
            ->latest('date')
            ->first();

        if ($record) {
            $record->update([
                'break_end' => now()->toTimeString(),
            ]);
            Session::flash('success', '休憩を終了しました。');
        } else {
            Session::flash('error', '休憩を開始していません。');
        }

        return redirect()->back();
    }

    //日付一覧ページ表示
    public function manage()
    {
        $timeRecords = TimeRecord::orderBy('date', 'desc')->paginate(5)->withQueryString();

        $totalWorkHours = $this->calculateTotalWorkHours($timeRecords);
        $totalBreakHours = $this->calculateTotalBreakHours($timeRecords);

        return view('attendance', compact('timeRecords', 'totalWorkHours', 'totalBreakHours'));

    }

    // 休憩・勤務時間取得
    private function calculateTotalWorkHours($timeRecords)
    {
        $totalWorkMinutes = 0;

        foreach ($timeRecords as $record) {
            $totalWorkMinutes += $this->calculateWorkMinutes($record);
        }

        $totalWorkHours = floor($totalWorkMinutes / 60);
        $totalWorkMinutes %= 60;

        return sprintf('%02d:%02d:00', $totalWorkHours, $totalWorkMinutes);
    }

    private function calculateWorkMinutes($record)
    {
        if ($record->start_time && $record->end_time) {
            $start = Carbon::parse($record->start_time);
            $end = Carbon::parse($record->end_time);

            return $end->diffInMinutes($start);
        }

        return 0;
    }

    private function calculateTotalBreakHours($timeRecords)
    {
        $totalBreakMinutes = 0;

        foreach ($timeRecords as $record) {
            $totalBreakMinutes += $this->calculateBreakMinutes($record);
        }

        $totalBreakHours = floor($totalBreakMinutes / 60);
        $totalBreakMinutes %= 60;

        return sprintf('%02d:%02d:00', $totalBreakHours, $totalBreakMinutes);
    }

    private function calculateBreakMinutes($record)
    {
        if ($record->break_start && $record->break_end) {
            $breakStart = Carbon::parse($record->break_start);
            $breakEnd = Carbon::parse($record->break_end);

            return $breakEnd->diffInMinutes($breakStart);
        }

        return 0;
    }
}

