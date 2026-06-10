<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Ticket;
use App\Models\Message;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\Solution;
use App\Models\KnowledgeBase;
use App\Models\User;
use App\Observers\GlobalObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🎫 Support system
        Ticket::observe(GlobalObserver::class);
        Message::observe(GlobalObserver::class);
        Conversation::observe(GlobalObserver::class);

        // 📚 Content system
        Category::observe(GlobalObserver::class);
        Solution::observe(GlobalObserver::class);
        KnowledgeBase::observe(GlobalObserver::class);

        // 👤 Users (optionnel mais utile pour audit sécurité)
        User::observe(GlobalObserver::class);
        }
}
