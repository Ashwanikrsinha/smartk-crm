<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use Yajra\DataTables\DataTables;
use App\Notifications\NewTaskNotification;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $tasks = Task::with('assignee', 'assignor')
                       ->where(function($query) {  
                        $query->where('assignor_id', auth()->id())
                            ->orWhere('assignee_id', auth()->id());
                        })->select();
  
            return DataTables::of($tasks)
                    ->editColumn('task_number', function ($task) {
                        return "T-{$task->task_number}";
                    })
                    ->editColumn('title', function ($task) {
                        return substr($task->title, 0, 30).'...';
                    })
                    ->editColumn('deadline_time', function ($task) {
                        return $task->deadline_time->format('d M, Y - h:i A');
                    })
                    ->editColumn('completed_at', function ($task) {
                        return isset($task->completed_at)
                              ? $task->completed_at->format('d M, Y - h:i: A')
                              : 'Pending';
                    })
                  ->addColumn('action', function ($task) {
                      return view('tasks.buttons')->with(['task' => $task]);
                  })->make(true);
        }

        return view('tasks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::active()->orderBy('username')->pluck('username', 'id');
        return view('tasks.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['task_number'] = Task::taskNumber();
        $validatedData['assignor_id'] = auth()->id();
        $validatedData['created_at'] = now();

        $task = Task::create($validatedData);
        $task->load('assignor');
        
        $assignee = User::findOrFail($request->assignee_id);   
        $assignee->notify(new NewTaskNotification($task));        
   
        return  back()->with('success', 'Task Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $comments = $task->comments()->with('user')->paginate()->fragment('comments');
        return view('tasks.show', compact('task', 'comments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        $users = User::active()->orderBy('username')->pluck('username', 'id');
        return view('tasks.edit', compact('task', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(TaskRequest $request, Task $task)
    {
        $validatedData = $request->validated();
        $validatedData['completed_at'] = $request->has('is_completed') ? now() : null;
        $task->update($validatedData);
   
        return back()->with('success', 'Task Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return back()->with('success', 'Task Deleted');
    }
}
