<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Task;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\Purpose;
use App\Models\CustomerContact;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        $tasks = Task::with('assignee')
          ->select('id', 'title', 'task_number', 'assignee_id', 'completed_at', 'deadline_time')
          ->where(function ($query) {
              $query->where('assignor_id', auth()->id())->orWhere('assignee_id', auth()->id());
          })
          ->get();
          
        $purposes = Purpose::orderBy('name')->pluck('name', 'id');
        $leaves = Leave::usersOnLeaveToday()->with('user')->get();
        $news = News::latestFour()->with(['image', 'event'])->get();
        $contact_occassions = CustomerContact::withOccassionsToday()->get();
        $employee_occassions  = Employee::withOccassionsToday()->with('department')->get();
        $notification_count = auth()->user()->unreadNotifications->count();
                           
        return view(
            'dashboard.show',
            compact(
              'purposes', 
              'tasks', 
              'leaves', 
              'news', 
              'contact_occassions', 
              'employee_occassions', 
              'notification_count'
              )
        );
    }
}
