<?php
protected $middleware = [
    // ... autres middleware
    \App\Http\Middleware\Cors::class,  // ← DOIT ÊTRE ICI
];