# Laravel Todo API 📝

A modern, feature-rich Todo application backend API built with Laravel. Provides comprehensive task management with priority levels, status tracking, and real-time statistics. Perfect for learning Laravel fundamentals and building production-ready REST APIs.

![Laravel](https://img.shields.io/badge/Laravel-10.x-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange?style=flat-square&logo=mysql)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## ✨ Features

### 🎯 Core Functionality
- **Complete CRUD Operations** - Create, Read, Update, Delete tasks
- **Priority System** - Three-level priority (Low, Medium, High) with database validation
- **Status Management** - Boolean completion tracking with timestamps
- **Smart Filtering** - Filter by status, priority, and date ranges

### 🚀 Advanced Features
- **Toggle API** - Quick completion status switching
- **Statistics Dashboard** - Real-time progress tracking and analytics
- **Time Tracking** - Automatic creation and completion timestamps
- **Human-Readable Dates** - "2 hours ago" format using Carbon

### 🏗️ Technical Features
- **RESTful API Design** - Standard HTTP methods and status codes
- **JSON Responses** - Consistent API structure with error handling
- **Database Validation** - ENUM constraints and type casting
- **Model Relationships** - Eloquent ORM with optimized queries
- **CORS Support** - Ready for frontend integration

## 🛠️ Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL 8.0+ or PostgreSQL
- Laravel 10.x

### Quick Start

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/laravel-todo-api.git
   cd laravel-todo-api
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   ```bash
   # Edit .env file with your database credentials
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_todo
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed sample data (optional)**
   ```bash
   php artisan db:seed --class=TaskSeeder
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

Your API will be available at `http://localhost:8000/api/tasks`

## 📡 API Endpoints

| Method | Endpoint | Description | Parameters |
|--------|----------|-------------|------------|
| `GET` | `/api/tasks` | Get all tasks | `status`, `priority` |
| `POST` | `/api/tasks` | Create new task | `title`, `description`, `priority` |
| `GET` | `/api/tasks/{id}` | Get specific task | - |
| `PUT` | `/api/tasks/{id}` | Update task | `title`, `description`, `priority`, `completed` |
| `PATCH` | `/api/tasks/{id}/toggle` | Toggle completion | - |
| `DELETE` | `/api/tasks/{id}` | Delete task | - |
| `GET` | `/api/tasks/stats` | Get statistics | - |

## 📊 API Usage Examples

### Create a new task
```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Learn Laravel",
    "description": "Complete Laravel tutorial",
    "priority": "high"
  }'
```

### Get all tasks with filtering
```bash
# Get only pending tasks
curl "http://localhost:8000/api/tasks?status=pending"

# Get high priority tasks
curl "http://localhost:8000/api/tasks?priority=high"
```

### Toggle task completion
```bash
curl -X PATCH http://localhost:8000/api/tasks/1/toggle
```

### Get statistics
```bash
curl http://localhost:8000/api/tasks/stats
```

## 📝 Response Format

### Success Response
```json
{
    "success": true,
    "message": "Task created successfully",
    "data": {
        "id": 1,
        "title": "Learn Laravel",
        "description": "Complete Laravel tutorial",
        "priority": "high",
        "completed": false,
        "completed_at": null,
        "created_at": "2024-01-20T10:30:00.000000Z",
        "formatted_created_at": "2 hours ago"
    }
}
```

### Statistics Response
```json
{
    "success": true,
    "data": {
        "total": 10,
        "completed": 6,
        "pending": 4,
        "progress_percentage": 60,
        "by_priority": {
            "high": 3,
            "medium": 4,
            "low": 3
        }
    }
}
```

## 🏗️ Database Schema

### Tasks Table
| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT | Primary key |
| `title` | VARCHAR(255) | Task title (required) |
| `description` | TEXT | Task description (optional) |
| `priority` | ENUM | Priority level: low, medium, high |
| `completed` | BOOLEAN | Completion status |
| `completed_at` | TIMESTAMP | Completion timestamp |
| `created_at` | TIMESTAMP | Creation timestamp |
| `updated_at` | TIMESTAMP | Last update timestamp |

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/TaskTest.php
```

### Sample Data Generation
```bash
# Create 10 random tasks
php artisan tinker
>>> Task::factory()->count(10)->create()

# Create completed tasks
>>> Task::factory()->completed()->count(5)->create()

# Create high priority tasks
>>> Task::factory()->highPriority()->count(3)->create()
```

## 🌐 Frontend Integration

### JavaScript Example
```javascript
class TodoAPI {
    constructor() {
        this.baseURL = 'http://localhost:8000/api/tasks';
    }

    async getAllTasks(filters = {}) {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${this.baseURL}?${params}`);
        return await response.json();
    }

    async createTask(taskData) {
        const response = await fetch(this.baseURL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(taskData)
        });
        return await response.json();
    }

    async toggleTask(id) {
        const response = await fetch(`${this.baseURL}/${id}/toggle`, {
            method: 'PATCH',
            headers: { 'Accept': 'application/json' }
        });
        return await response.json();
    }
}

// Usage
const api = new TodoAPI();
const tasks = await api.getAllTasks({ status: 'pending' });
```

## 📂 Project Structure

```
├── app/
│   ├── Http/Controllers/
│   │   └── TaskController.php      # Main API controller
│   └── Models/
│       └── Task.php               # Eloquent model
├── database/
│   ├── factories/
│   │   └── TaskFactory.php        # Test data factory
│   ├── migrations/
│   │   └── create_tasks_table.php # Database schema
│   └── seeders/
│       └── TaskSeeder.php         # Sample data seeder
├── routes/
│   └── api.php                    # API routes
└── tests/
    └── Feature/
        └── TaskTest.php           # API tests
```

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Configure production database
- [ ] Set up proper web server (Nginx/Apache)
- [ ] Enable HTTPS
- [ ] Configure CORS for production domains
- [ ] Set up monitoring and logging

### Docker Support (Optional)
```dockerfile
FROM php:8.1-fpm

# Install dependencies and configure
COPY . /var/www/html
WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader
RUN php artisan config:cache
RUN php artisan route:cache

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 👨‍💻 Author

**Your Name**
- GitHub: [@hirushanirmal301](https://github.com/yourusername)
- LinkedIn: [Your LinkedIn](https://linkedin.com/in/yourprofile)
- Email: hirushanirmal737@gmail.com

## 🙏 Acknowledgments

- Laravel Framework team for the amazing framework
- PHP community for continuous support
- Contributors who helped improve this project

---

⭐ **Star this repository if you found it helpful!**

**Built with ❤️ using Laravel**
