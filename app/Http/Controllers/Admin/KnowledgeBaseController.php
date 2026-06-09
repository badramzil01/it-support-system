<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        $query = KnowledgeBase::with(['author', 'lastEditor']);

        // Recherche globale
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('problem_keywords', 'like', "%{$s}%")
                  ->orWhere('solution', 'like', "%{$s}%")
                  ->orWhereHas('author', function ($a) use ($s) {
                      $a->where('name', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%");
                  });
            });
        }

        // Filtres
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('author_id')) {
            $query->where('author_id', (int) $request->input('author_id'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Tri
        $allowedSorts = ['id', 'created_at', 'updated_at', 'status', 'category', 'confidence', 'usage_count'];
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        if (!in_array($sort, $allowedSorts)) $sort = 'created_at';
        $query->orderBy($sort, $direction);

        $items = $query->paginate(20)->appends($request->query());

        // Statistiques
        $stats = $this->getStats();

        // Pour les filtres
        $categories = KnowledgeBase::select('category')->whereNotNull('category')->distinct()->pluck('category')->filter()->values();
        $statuses = ['active', 'draft', 'archived'];
        $authors = User::whereHas('knowledgeBaseItems')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $edit = null;
        if ($request->has('edit')) {
            $edit = KnowledgeBase::find($request->input('edit'));
        }

        return view('admin.knowledge.index', compact('items', 'edit', 'stats', 'categories', 'statuses', 'authors', 'sort', 'direction'));
    }

    public function show(KnowledgeBase $item)
    {
        $item->load(['author', 'lastEditor']);
        $related = KnowledgeBase::with('author')
            ->where('id', '!=', $item->id)
            ->where(function ($q) use ($item) {
                if ($item->category) $q->where('category', $item->category);
            })
            ->latest()->take(5)->get();
        return view('admin.knowledge.show', compact('item', 'related'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'problem_keywords' => 'required|string',
            'solution' => 'required|string',
            'source' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,draft,archived',
            'confidence' => 'nullable|numeric|min:0|max:100',
            'tags' => 'nullable|string',
        ]);
        $data['author_id'] = auth()->id();
        $data['last_modified_by'] = auth()->id();
        $data['status'] = $data['status'] ?? 'active';
        $data['source'] = $data['source'] ?? 'DB';
        $data['tags'] = $this->parseTags($data['tags'] ?? null);
        $data['usage_count'] = 0;

        KnowledgeBase::create($data);

        return redirect()->route('admin.ui.knowledge.index')->with('success', 'Solution ajoutée avec succès');
    }

    public function update(Request $request, KnowledgeBase $item)
    {
        $data = $request->validate([
            'problem_keywords' => 'required|string',
            'solution' => 'required|string',
            'source' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,draft,archived',
            'confidence' => 'nullable|numeric|min:0|max:100',
            'tags' => 'nullable|string',
        ]);
        $data['last_modified_by'] = auth()->id();
        $data['tags'] = $this->parseTags($data['tags'] ?? null);
        $item->update($data);

        return redirect()->route('admin.ui.knowledge.index')->with('success', 'Solution modifiée');
    }

    public function destroy(KnowledgeBase $item)
    {
        $item->delete();
        return back()->with('success', 'Solution supprimée');
    }

    /**
     * Page dédiée à un auteur
     * /admin/knowledge-base/authors/{id}
     */
    public function showAuthor(User $user)
    {
        $user->loadCount('knowledgeBaseItems');

        // Nombre de modifications (KnowledgeBase où user = last_modified_by)
        $modificationsCount = KnowledgeBase::where('last_modified_by', $user->id)->count();

        // Catégories les plus utilisées par cet auteur
        $topCategories = KnowledgeBase::where('author_id', $user->id)
            ->whereNotNull('category')
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        // Liste des solutions
        $items = KnowledgeBase::with('lastEditor')
            ->where('author_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        // Dernière activité
        $lastItem = KnowledgeBase::where('author_id', $user->id)->latest('updated_at')->first();
        $lastActivity = $lastItem?->updated_at;

        // Stats par statut
        $statusBreakdown = KnowledgeBase::where('author_id', $user->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Rôle de l'utilisateur
        $role = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->first() : 'User';

        return view('admin.knowledge.author', compact(
            'user', 'role', 'modificationsCount', 'topCategories', 'items', 'lastActivity', 'statusBreakdown'
        ));
    }

    public function getStats(): array
    {
        $total = KnowledgeBase::count();
        $active = KnowledgeBase::where('status', 'active')->count();
        $thisMonth = KnowledgeBase::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $authors = KnowledgeBase::whereNotNull('author_id')->distinct('author_id')->count('author_id');

        // Top contributeur
        $topContributorId = KnowledgeBase::select('author_id', DB::raw('count(*) as total'))
            ->whereNotNull('author_id')
            ->groupBy('author_id')
            ->orderByDesc('total')
            ->value('author_id');
        $topContributor = $topContributorId ? User::find($topContributorId) : null;

        return [
            'total' => $total,
            'active' => $active,
            'this_month' => $thisMonth,
            'authors' => $authors,
            'top_contributor' => $topContributor,
        ];
    }

    private function parseTags(?string $raw): ?array
    {
        if (!$raw) return null;
        $parts = preg_split('/[,;]+/', $raw) ?: [];
        $cleaned = array_values(array_filter(array_map('trim', $parts), fn ($v) => $v !== ''));
        return $cleaned ?: null;
    }
}
