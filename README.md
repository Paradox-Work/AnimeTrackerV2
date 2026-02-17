# 🎬 Anime Tracker V2

A full-stack web application for tracking anime, discovering new shows, and managing your personal watchlist. Built with Laravel 12, Blade, Tailwind CSS, and Alpine.js.

![Anime Tracker Demo](image.png) <!-- Add your screenshot here -->

## ✨ Features

- 🔐 **User Authentication** - Register, login, password reset
- 🔍 **Anime Search** - Search through AniList database
- 📋 **Personal Lists** - Create custom lists (Watching, Completed, Plan to Watch)
- 📊 **Progress Tracking** - Track episodes watched per anime
- ⭐ **Rating System** - Rate anime you've watched
- 📱 **Responsive Design** - Works on desktop and mobile
- 🎨 **Modern UI** - Clean interface with Tailwind CSS

## 🚀 **Tech Stack**

### Backend
- **Laravel 12** - PHP framework
- **Laravel Breeze** - Authentication scaffolding (Blade stack)
- **Laravel Sanctum** - API authentication
- **MySQL** - Database (via Laragon)
- **GraphQL Client** - For AniList API integration

### Frontend
- **Blade Templates** - Laravel's templating engine
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Vite** - Build tool and asset bundler

## 📁 **Project Structure**

```
anime-tracker/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AnimeController.php    # Anime CRUD
│   │   │   ├── ListController.php     # User lists
│   │   │   └── ProfileController.php  # User profile
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Anime.php                   # Anime model
│   │   ├── UserList.php                 # User lists
│   │   └── UserProgress.php              # Episode tracking
│   └── Services/
│       └── AniList/
│           └── AniListService.php        # GraphQL API wrapper
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php             # Main layout
│   │   ├── auth/                          # Auth pages
│   │   ├── profile/                        # Profile pages
│   │   ├── anime/
│   │   │   ├── index.blade.php             # Anime list
│   │   │   ├── show.blade.php               # Anime details
│   │   │   └── search.blade.php              # Search page
│   │   └── lists/
│   │       └── index.blade.php               # User lists
│   └── css/
│       └── app.css                           # Tailwind imports
├── routes/
│   ├── web.php                                # Web routes
│   └── auth.php                               # Auth routes
├── package.json                                # Frontend dependencies
├── tailwind.config.js                          # Tailwind config
└── vite.config.js                              # Vite config
```

## 🛠️ **Installation Guide**

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL (via Laragon)
- Git

### Step-by-Step Installation

```bash
# 1. Clone the repository
git clone https://github.com/Paradox-Work/AnimeTrackerV2.git
cd AnimeTrackerV2

# 2. Install PHP dependencies
composer install

# 3. Install JavaScript dependencies
npm install

# 4. Environment setup
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure database in .env file
# DB_DATABASE=anime_tracker
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Run migrations
php artisan migrate

# 8. Build frontend assets (development)
npm run dev

# 9. Start the development server
php artisan serve
# Or use Laragon's URL
```

### For Production Build

```bash
npm run build
```

## 📦 **What's Included**

After running `php artisan breeze:install blade`, you get:

### Backend Scaffolding
- ✅ Authentication controllers and routes
- ✅ User model with `HasApiTokens` trait
- ✅ Email verification support
- ✅ Password reset functionality

### Frontend Scaffolding
- ✅ `package.json` with Tailwind, Alpine.js, Vite
- ✅ `tailwind.config.js` configured
- ✅ `vite.config.js` with Laravel plugin
- ✅ Complete authentication views (login, register, password reset)
- ✅ User profile management pages
- ✅ Responsive dashboard layout

### Directory Structure Created
```
resources/views/
├── layouts/
│   └── app.blade.php          # Main layout with navbar
├── profile/
│   ├── edit.blade.php         # Profile edit page
│   └── partials/               # Profile components
├── auth/
│   ├── login.blade.php         # Login page
│   ├── register.blade.php      # Registration page
│   ├── forgot-password.blade.php # Password reset
│   └── confirm-password.blade.php # Password confirmation
├── components/                  # Reusable Blade components
└── dashboard.blade.php          # User dashboard
```

## 🔌 **API Integration (AniList)**

We use GraphQL to fetch anime data from AniList:

```php
// app/Services/AniListService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class AniListService
{
    protected $endpoint = 'https://graphql.anilist.co';
    
    public function searchAnime($search)
    {
        $query = '
        query ($search: String) {
            Page(page: 1, perPage: 20) {
                media(search: $search, type: ANIME) {
                    id
                    title { romaji english }
                    episodes
                    coverImage { large }
                }
            }
        }';
        
        return Http::post($this->endpoint, [
            'query' => $query,
            'variables' => ['search' => $search]
        ])->json();
    }
}
```

## 🎯 **Database Schema**

```sql
-- Users table (from Breeze)
users
├── id
├── name
├── email
├── password
└── ...

-- Custom tables you'll create
anime
├── id
├── anilist_id (unique)
├── title
├── title_japanese
├── description
├── episodes
├── cover_image
├── banner_image
├── average_score
└── genres (JSON)

user_lists
├── id
├── user_id
├── name (watching/completed/plan_to_watch)
├── is_default
└── is_public

user_progress
├── id
├── user_id
├── anime_id
├── list_id
├── episodes_watched
├── score
├── start_date
└── finish_date
```

## 🚦 **Routes Structure**

```php
// routes/web.php

// Public routes
Route::get('/', [AnimeController::class, 'index'])->name('home');
Route::get('/anime/{id}', [AnimeController::class, 'show'])->name('anime.show');
Route::get('/search', [AnimeController::class, 'search'])->name('anime.search');

// Protected routes (require login)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User lists
    Route::resource('lists', ListController::class);
    Route::post('/anime/{anime}/add-to-list', [ListController::class, 'addAnime']);
    Route::put('/progress/{anime}', [ProgressController::class, 'update']);
    
    // Profile management (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
```

## 🎨 **UI Components (Based on Your Design)**

```blade
{{-- Example: Anime grid component --}}
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
    @foreach($anime as $show)
        <div class="relative group">
            <img src="{{ $show->coverImage }}" class="rounded-lg">
            <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black">
                <h3 class="text-white font-bold">{{ $show->title }}</h3>
                <p class="text-gray-300 text-sm">Ep {{ $show->episodes }}</p>
            </div>
        </div>
    @endforeach
</div>
```

## 🔧 **Troubleshooting**

### Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| `npm install` fails | Delete `node_modules` and `package-lock.json`, then run again |
| `package.json` missing | You installed API stack, reinstall with: `php artisan breeze:install blade --force` |
| Vite not compiling | Check Node version: `node --version` (needs v16+) |
| 419 page expired | Clear sessions: `php artisan cache:clear` |

## 📚 **Development Commands**

```bash
# Start development servers (run in separate terminals)
npm run dev        # For frontend assets
php artisan serve  # For Laravel backend

# Database commands
php artisan migrate
php artisan migrate:fresh  # Reset database
php artisan db:seed        # Seed with test data

# Cache commands
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Make new components
php artisan make:controller AnimeController
php artisan make:model Anime -m
php artisan make:middleware IsAdmin
```

## 🔄 **Version History**

- **v1.0.0** - Initial setup with Laravel 12
- **v1.0.1** - Added Breeze authentication (Blade stack)
- **v1.0.2** - Configured Tailwind CSS and Vite
- **v1.1.0** - AniList API integration (upcoming)

## 📝 **What I Learned**

During setup, I encountered these important lessons:

1. **Breeze Stack Selection Matters** 
   - ❌ API stack = No frontend files, no package.json
   - ✅ Blade stack = Full web interface with Tailwind

2. **Package Compatibility**
   - Laravel 12 requires up-to-date packages
   - Check package requirements before installing

3. **GraphQL Client Choice**
   - `mll-lab/laravel-graphql-playground` is for older Laravel versions
   - Use `gmostafa/php-graphql-client` or Laravel HTTP client for v12

4. **Asset Compilation**
   - Vite is now the default (not Mix)
   - Run `npm run dev` during development
   - Run `npm run build` for production

## 🤝 **Contributing**

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 **License**

This project is licensed under the MIT License.

## 🙏 **Acknowledgments**

- [Laravel](https://laravel.com) - The PHP framework
- [Tailwind CSS](https://tailwindcss.com) - CSS framework
- [AniList](https://anilist.co) - Anime database API
- [Laragon](https://laragon.org) - Local development environment

---

**Happy Coding!** 🎉
