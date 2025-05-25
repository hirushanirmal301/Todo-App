<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Todo App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .task-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .task-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .completed {
            opacity: 0.6;
            text-decoration: line-through;
        }
        
        .priority-high {
            border-left: 4px solid #ef4444;
        }
        
        .priority-medium {
            border-left: 4px solid #f59e0b;
        }
        
        .priority-low {
            border-left: 4px solid #10b981;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-out {
            animation: slideOut 0.3s ease-in-out forwards;
        }
        
        @keyframes slideOut {
            to { opacity: 0; transform: translateX(100px); }
        }
        
        .floating-action {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .floating-action:hover {
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
            transform: translateY(-2px);
        }
        
        .progress-bar {
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.5s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Background gradient -->
    <div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900 opacity-50"></div>
    
    <!-- Main container -->
    <div class="relative z-10 min-h-screen p-4">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8 pt-8">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-2">
                    TaskFlow
                </h1>
                <p class="text-gray-400">Stay organized, stay productive</p>
            </div>
            
            <!-- Stats Card -->
            <div class="glass-effect rounded-2xl p-6 mb-6 fade-in">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Today's Progress</h2>
                    <span id="progress-text" class="text-sm text-gray-400">0 of 0 completed</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2 mb-4">
                    <div id="progress-bar" class="progress-bar h-2 rounded-full" style="width: 0%;"></div>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div id="total-tasks" class="text-2xl font-bold text-purple-400">{{$total}}</div>
                        <div class="text-xs text-gray-400">Total</div>
                    </div>
                    <div>
                        <div id="completed-tasks" class="text-2xl font-bold text-green-400">{{$completed}}</div>
                        <div class="text-xs text-gray-400">Done</div>
                    </div>
                    <div>
                        <div id="pending-tasks" class="text-2xl font-bold text-orange-400">{{$pending}}</div>
                        <div class="text-xs text-gray-400">Pending</div>

                    </div>
                </div>
            </div>
            
            <form action="{{route('task.store')}}" method="POST">
                @csrf
                <div class="glass-effect rounded-2xl p-6 mb-6 fade-in">
                <div class="flex flex-col sm:flex-row gap-3">
                    <input 
                        type="text" 
                        id="task-input" 
                        name="title"
                        placeholder="What needs to be done?" 
                        class="flex-1 bg-gray-800 border border-gray-600 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                    >
                    <select name="priority" id="priority-select" class="bg-gray-800 border border-gray-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="low">Low Priority</option>
                        <option value="medium">Medium Priority</option>
                        <option value="high">High Priority</option>
                    </select>
                    <button 
                        id="add-btn" 
                        class="floating-action text-white px-6 py-3 rounded-xl font-medium transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                    >
                        Add Task
                    </button>
                </div>
            </div>
            </form>
            <!-- Add Task Form -->
            
            
            <!-- Filter Tabs -->
            <div class="flex justify-center mb-6">
                <div class="glass-effect rounded-xl p-1 inline-flex">
                    <button class="filter-btn active px-4 py-2 rounded-lg text-sm font-medium transition-all" data-filter="all">All</button>
                    <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-all" data-filter="pending">Pending</button>
                    <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-all" data-filter="completed">Completed</button>
                </div>
            </div>
            
            <!-- Tasks Container -->
            <div id="tasks-container" class="space-y-3">
                <!-- Tasks will be dynamically added here -->
            </div>
            
            <!-- Empty State -->
            <div id="empty-state" class="text-center py-12 hidden">
                <div class="text-6xl mb-4">📝</div>
                <h3 class="text-xl font-semibold mb-2">No tasks yet</h3>
                <p class="text-gray-400">Add your first task to get started!</p>
            </div>

            <!-- task card -->
@foreach($tasks as $task)
<div class="task-item glass-effect rounded-xl p-4 priority-{{$task->priority}} fade-in m-3" data-task-id="{{$task->id}}">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3 flex-1">
            <!-- Checkbox -->
            <input 
                type="checkbox"
                class="w-5 h-5 text-purple-600 bg-gray-700 border-gray-600 rounded focus:ring-purple-500 focus:ring-2"
                {{$task->completed ? 'checked' : ''}}
            >
            
            <!-- Task Content -->
            <div class="flex-1 {{$task->completed ? 'completed' : ''}}">
                <p class="text-white">{{$task->title}}</p>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-700 text-gray-300">
                        {{$task->priority}}
                    </span>
                    <span class="text-xs text-gray-400">
                        {{$task->created_at->diffForHumans()}}
                    </span>
                </div>
            </div>
        </div>

        <!-- Delete Button -->
        <button 
            class="text-red-400 hover:text-red-300 p-2 rounded-lg hover:bg-red-900/20 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    </div>
</div>
@endforeach
        </div> 
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Get references to DOM elements
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const totalTasksEl = document.getElementById('total-tasks');
    const completedTasksEl = document.getElementById('completed-tasks');
    const pendingTasksEl = document.getElementById('pending-tasks');
    const taskItems = document.querySelectorAll('.task-item');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const emptyState = document.getElementById('empty-state');
    
    // Initialize progress bar
    updateProgress();
    
    // Initialize checkboxes
    taskItems.forEach(taskItem => {
        const checkbox = taskItem.querySelector('input[type="checkbox"]');
        if (checkbox) {
            // Set initial state based on item class
            if (taskItem.querySelector('.flex-1').classList.contains('completed')) {
                checkbox.checked = true;
                taskItem.classList.add('completed');
            }
            
            // Add change event listener
            checkbox.addEventListener('change', function() {
                // Toggle completed class
                if (this.checked) {
                    taskItem.classList.add('completed');
                    taskItem.querySelector('.flex-1').classList.add('completed');
                } else {
                    taskItem.classList.remove('completed');
                    taskItem.querySelector('.flex-1').classList.remove('completed');
                }
                
                // Update progress
                updateProgress();
                
                // Get task ID
                const taskId = taskItem.dataset.taskId;
                if (taskId) {
                    updateTaskStatus(taskId, this.checked);
                }
            });
        }
    });
    
    // Filter buttons
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active state
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Apply filter
            const filter = this.dataset.filter;
            taskItems.forEach(taskItem => {
                if (filter === 'all') {
                    taskItem.style.display = 'block';
                } else if (filter === 'completed') {
                    taskItem.style.display = taskItem.classList.contains('completed') ? 'block' : 'none';
                } else if (filter === 'pending') {
                    taskItem.style.display = !taskItem.classList.contains('completed') ? 'block' : 'none';
                }
            });
        });
    });
    
    // Update progress function
    function updateProgress() {
        const totalTasks = taskItems.length;
        const completedTasks = document.querySelectorAll('.task-item.completed').length;
        const pendingTasks = totalTasks - completedTasks;
        
        // Update counters
        totalTasksEl.textContent = totalTasks;
        completedTasksEl.textContent = completedTasks;
        pendingTasksEl.textContent = pendingTasks;
        
        // Update progress text
        progressText.textContent = completedTasks + ' of ' + totalTasks + ' completed';
        
        // Update progress bar
        const progressPercentage = totalTasks === 0 ? 0 : (completedTasks / totalTasks) * 100;
        progressBar.style.width = progressPercentage + '%';
        
        // Show/hide empty state
        if (totalTasks === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }
    
    // Update task status on server
    function updateTaskStatus(taskId, isCompleted) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('/tasks/' + taskId + '/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                completed: isCompleted
            })
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .catch(function(error) {
            console.error('Error updating task status:', error);
        });
    }
});
</script>
    

   
</body>
</html>