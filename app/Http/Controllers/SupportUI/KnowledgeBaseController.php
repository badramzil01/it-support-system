<?php

namespace App\Http\Controllers\SupportUI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KnowledgeBase;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        $query = KnowledgeBase::query();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where('problem_keywords', 'like', "%{$s}%")->orWhere('solution', 'like', "%{$s}%");
        }

        if ($request->filled('category') && Schema::hasColumn('knowledge_base', 'category')) {
            $query->where('category', $request->input('category'));
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->query());

        if (Schema::hasColumn('knowledge_base', 'category')) {
            $categories = KnowledgeBase::select('category')->distinct()->pluck('category')->filter()->values();
        } else {
            $categories = collect();
        }

        return view('support.knowledgebase', compact('items','categories'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('support.knowledge.form', ['item' => null, 'users' => $users]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'problem_keywords' => 'required|string|max:4000',
            'solution' => 'required|string|max:10000',
            'category' => 'nullable|string|max:120',
            'author_id' => 'nullable|exists:users,id',
        ]);

        $kb = KnowledgeBase::create($data);
        return redirect()->route('support.ui.knowledge.index')->with('success', 'Element ajouté');
    }

    public function edit(KnowledgeBase $item)
    {
        $users = User::orderBy('name')->get();
        return view('support.knowledge.form', compact('item','users'));
    }

    public function update(Request $request, KnowledgeBase $item)
    {
        $data = $request->validate([
            'problem_keywords' => 'required|string|max:4000',
            'solution' => 'required|string|max:10000',
            'category' => 'nullable|string|max:120',
            'author_id' => 'nullable|exists:users,id',
        ]);

        $item->update($data);
        return redirect()->route('support.ui.knowledge.index')->with('success', 'Element mis à jour');
    }

    public function destroy(KnowledgeBase $item)
    {
        $item->delete();
        return redirect()->route('support.ui.knowledge.index')->with('success', 'Element supprimé');
    }
}

