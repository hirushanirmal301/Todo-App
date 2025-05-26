<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();

        $completed = Task::where('completed', true)->count();

        $pending = Task::where('completed', false)->count(); 

        $total = Task::count();

        return view('index',[
            'tasks' => $tasks,
            'completed' => $completed,
            'pending' => $pending,
            'total' => $total
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request -> validate([
            'title' => 'required|string|max:50',
            'priority' => 'required|string|in:low,medium,high',
        ]);

        $task = Task::create([
            'title' => $validate['title'],
            'priority' => $validate['priority'],
        ]);

        $completedCount = Task::where('completed', true)->count();
        $pendingCount = Task::where('completed', false)->count();

        return redirect()->route('task.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        try {
            $task->delete();
            return response()->json(['success' => true, 'message' => 'Task deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete task'], 500);
        }
    }

    public function updateStatus(Task $task, Request $request)
    {
        $task->completed = $request->completed;
        
        if ($request->completed) {
            $task->completed_at = now();
        } else {
            $task->completed_at = null;
        }
        
        $task->save();
        
        return response()->json(['success' => true]);
    }
}