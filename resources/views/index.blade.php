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
                        <div id="total-tasks" class="text-2xl font-bold text-purple-400">{{ $total }}</div>
                        <div class="text-xs text-gray-400">Total</div>
                    </div>
                    <div>
                        <div id="completed-tasks" class="text-2xl font-bold text-green-400">{{ $completed }}</div>
                        <div class="text-xs text-gray-400">Done</div>
                    </div>
                    <div>
                        <div id="pending-tasks" class="text-2xl font-bold text-orange-400">{{ $pending }}</div>
                        <div class="text-xs text-gray-400">Pending</div>
                    </div>
                </div>
            </div>
            
            <!-- Add Task Form -->
            <form action="{{ route('task.store') }}" method="POST">
                @csrf
                <div class="glass-effect rounded-2xl p-6 mb-6 fade-in">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input 
                            type="text" 
                            id="task-input" 
                            name="title"
                            placeholder="What needs to be done?" 
                            class="flex-1 bg-gray-800 border border-gray-600 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                            required
                        >
                        <select name="priority" id="priority-select" class="bg-gray-800 border border-gray-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="low">Low Priority</option>
                            <option value="medium">Medium Priority</option>
                            <option value="high">High Priority</option>
                        </select>
                        <button 
                            type="submit"
                            id="add-btn" 
                            class="floating-action text-white px-6 py-3 rounded-xl font-medium transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                        >
                            Add Task
                        </button>
                    </div>
                </div>
            </form>
            
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
                <!-- Task Cards -->
                @foreach($tasks as $task)
                <div class="task-item glass-effect rounded-xl p-4 priority-{{ $task->priority }} fade-in m-3 {{ $task->completed ? 'completed' : '' }}" data-task-id="{{ $task->id }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 flex-1">
                            <!-- Checkbox -->
                            <input 
                                type="checkbox"
                                class="task-checkbox w-5 h-5 text-purple-600 bg-gray-700 border-gray-600 rounded focus:ring-purple-500 focus:ring-2"
                                {{ $task->completed ? 'checked' : '' }}
                                data-task-id="{{ $task->id }}"
                            >
                            
                            <!-- Task Content -->
                            <div class="flex-1 task-content {{ $task->completed ? 'completed' : '' }}">
                                <p class="text-white">{{ $task->title }}</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-700 text-gray-300">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ $task->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Button -->
                        <button 
                            class="delete-btn text-red-400 hover:text-red-300 p-2 rounded-lg hover:bg-red-900/20 transition-all"
                            data-task-id="{{ $task->id }}"
                            title="Delete task"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Empty State -->
            <div id="empty-state" class="text-center py-12 {{ count($tasks) > 0 ? 'hidden' : '' }}">
                <div class="text-6xl mb-4">📝</div>
                <h3 class="text-xl font-semibold mb-2">No tasks yet</h3>
                <p class="text-gray-400">Add your first task to get started!</p>
            </div>
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
        let taskItems = document.querySelectorAll('.task-item');
        const filterBtns = document.querySelectorAll('.filter-btn');
        const emptyState = document.getElementById('empty-state');
        
        // Initialize progress bar
        updateProgress();
        
        // Handle checkbox changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('task-checkbox')) {
                const taskItem = e.target.closest('.task-item');
                const taskContent = taskItem.querySelector('.task-content');
                
                // Toggle completed class
                if (e.target.checked) {
                    taskItem.classList.add('completed');
                    taskContent.classList.add('completed');
                } else {
                    taskItem.classList.remove('completed');
                    taskContent.classList.remove('completed');
                }
                
                // Update progress
                updateProgress();
                
                // Get task ID and update status
                const taskId = e.target.dataset.taskId;
                if (taskId) {
                    updateTaskStatus(taskId, e.target.checked);
                }
            }
        });
        
        // Handle delete button clicks
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-btn')) {
                e.preventDefault();
                const deleteBtn = e.target.closest('.delete-btn');
                const taskId = deleteBtn.dataset.taskId;
                const taskItem = deleteBtn.closest('.task-item');
                const taskTitle = taskItem.querySelector('.task-content p').textContent;
                
                // Show confirmation dialog
                if (confirm(`Are you sure you want to delete "${taskTitle}"?\n\nThis action cannot be undone.`)) {
                    deleteTask(taskId, taskItem);
                }
            }
        });
        
        // Filter buttons
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active state
                filterBtns.forEach(b => b.classList.remove('active', 'bg-purple-600'));
                this.classList.add('active', 'bg-purple-600');
                
                // Apply filter
                const filter = this.dataset.filter;
                taskItems = document.querySelectorAll('.task-item'); // Update task items
                taskItems.forEach(taskItem => {
                    if (filter === 'all') {
                        taskItem.style.display = 'block';
                    } else if (filter === 'completed') {
                        taskItem.style.display = taskItem.classList.contains('completed') ? 'block' : 'none';
                    } else if (filter === 'pending') {
                        taskItem.style.display = !taskItem.classList.contains('completed') ? 'block' : 'none';
                    }
                });
                
                updateProgress();
            });
        });
        
        // Update progress function
        function updateProgress() {
            taskItems = document.querySelectorAll('.task-item'); // Update task items count
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
                emptyState.style.display = 'block';
            } else {
                emptyState.classList.add('hidden');
                emptyState.style.display = 'none';
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
                // Revert checkbox state on error
                const checkbox = document.querySelector(`input[data-task-id="${taskId}"]`);
                if (checkbox) {
                    checkbox.checked = !isCompleted;
                }
                alert('Failed to update task status. Please try again.');
            });
        }
        
        // Delete task function
        function deleteTask(taskId, taskItem) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Add slide-out animation
            taskItem.classList.add('slide-out');
            
            fetch('/tasks/' + taskId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    // Wait for animation to complete before removing
                    setTimeout(function() {
                        taskItem.remove();
                        updateProgress();
                    }, 300);
                } else {
                    // Remove animation class if deletion failed
                    taskItem.classList.remove('slide-out');
                    alert('Failed to delete task: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(function(error) {
                console.error('Error deleting task:', error);
                // Remove animation class if deletion failed
                taskItem.classList.remove('slide-out');
                alert('Failed to delete task. Please try again.');
            });
        }
    });
    </script>
</body>
</html>