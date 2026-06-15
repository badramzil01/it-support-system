<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\KnowledgeBase;
use App\Models\AIResponse;
use App\Models\Ticket;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Notification as AppNotification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== KPI Cards =====
        $totalUsers    = User::count();
        $totalTickets  = Ticket::count();
        $openTickets   = Ticket::where('status', 'open')->count();
        $escalated     = Ticket::where('is_escalated', true)->count();
        $urgent        = Ticket::where('is_urgent', true)->count();
        $conversations = Conversation::count();
        $aiResolutions = AIResponse::count();
        $resolved      = Ticket::whereIn('status', ['resolved','closed'])->count();
        $satisfaction  = $this->computeSatisfaction();

        // ===== Charts =====
        // Tickets par jour (7 derniers jours)
        $ticketsPerDay = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $ticketsPerDay->push([
                'date' => $date,
                'label' => now()->subDays($i)->locale('fr')->isoFormat('dd'),
                'count' => Ticket::whereDate('created_at', $date)->count(),
            ]);
        }
        $ticketsPerDayLabels = $ticketsPerDay->pluck('label')->toArray();
        $ticketsPerDayData   = $ticketsPerDay->pluck('count')->toArray();

        // Catégories
        $categoryCounts = Ticket::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
        $categoryLabels = $categoryCounts->pluck('category')->map(fn($c) => $c ?: '—')->toArray();
        $categoryData   = $categoryCounts->pluck('total')->toArray();

        // Priorités
        $priorityCounts = Ticket::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->get();
        $priorityLabels = $priorityCounts->pluck('priority')->map(fn($p) => ucfirst($p ?: '—'))->toArray();
        $priorityData   = $priorityCounts->pluck('total')->toArray();

        // ===== Latest items =====
        $latestTickets      = Ticket::with('user')->latest()->take(8)->get();
        $latestConversations = Conversation::with('user', 'latestMessage')
            ->withCount('messages')
            ->latest('updated_at')
            ->take(8)
            ->get();

        // ===== System status =====
        $systemStatus = $this->getSystemStatus();

        return view('admin.dashboard', compact(
            'totalUsers','totalTickets','openTickets','escalated','urgent',
            'conversations','aiResolutions','satisfaction','resolved',
            'ticketsPerDayLabels','ticketsPerDayData',
            'categoryLabels','categoryData',
            'priorityLabels','priorityData',
            'latestTickets','latestConversations',
            'systemStatus'
        ));
    }

    private function computeSatisfaction(): float
    {
        // Best effort: count positive feedback / total
        try {
            $total = Ticket::whereNotNull('feedback')->count();
            if ($total === 0) return 0;
            $positive = Ticket::where('feedback', 'positive')->count();
            return round(($positive / max(1, $total)) * 100, 1);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function getSystemStatus(): array
    {
        // Laravel
        $laravelStatus = [
            'name' => 'Laravel',
            'status' => 'online',
            'version' => app()->version(),
            'response' => '< 1ms',
            'last_sync' => now()->format('d/m/Y H:i'),
            'last_error' => null,
            'icon' => 'server',
        ];

        // MySQL
        try {
            DB::connection()->getPdo();
            $mysqlStatus = [
                'name' => 'MySQL',
                'status' => 'online',
                'version' => DB::select('SELECT VERSION() as v')[0]->v ?? '—',
                'response' => '~2ms',
                'last_sync' => now()->format('d/m/Y H:i'),
                'last_error' => null,
                'icon' => 'database',
            ];
        } catch (\Throwable $e) {
            $mysqlStatus = [
                'name' => 'MySQL',
                'status' => 'offline',
                'version' => '—',
                'response' => '—',
                'last_sync' => '—',
                'last_error' => $e->getMessage(),
                'icon' => 'database',
            ];
        }

        // n8n (webhook)
        $n8nUrl = config('services.n8n.url') ?: config('app.n8n_webhook');
        $n8nStatus = [
            'name' => 'n8n',
            'status' => $n8nUrl ? 'online' : 'unknown',
            'version' => '—',
            'response' => '—',
            'last_sync' => '—',
            'last_error' => null,
            'icon' => 'flow',
        ];

        // Jira
        $jiraUrl = config('services.jira.url');
        $jiraStatus = [
            'name' => 'Jira',
            'status' => $jiraUrl ? 'online' : 'unknown',
            'version' => '—',
            'response' => '—',
            'last_sync' => '—',
            'last_error' => null,
            'icon' => 'ticket',
        ];

        // Gmail
        $gmailUser = config('services.gmail.user') ?: config('mail.from.address');
        $gmailStatus = [
            'name' => 'Gmail',
            'status' => $gmailUser ? 'online' : 'unknown',
            'version' => '—',
            'response' => '—',
            'last_sync' => '—',
            'last_error' => null,
            'icon' => 'mail',
        ];

        // OpenAI / OpenRouter
        $openai = config('services.openai.key') ?: config('services.openrouter.key') ?: env('OPENAI_API_KEY');
        $aiStatus = [
            'name' => 'OpenAI / OpenRouter',
            'status' => $openai ? 'online' : 'unknown',
            'version' => '—',
            'response' => '—',
            'last_sync' => '—',
            'last_error' => null,
            'icon' => 'sparkles',
        ];

        return [$laravelStatus, $mysqlStatus, $n8nStatus, $jiraStatus, $gmailStatus, $aiStatus];
    }

    // ===================================================================
    // USERS
    // ===================================================================
    public function ListeUsers(Request $request)
    {
        $users = User::with('roles')->get();
        $editUser = null;
        $roles = Role::all();

        if ($request->has('edit')) {
        $editUser = User::findOrFail($request->edit);
        }

        return view('admin.users.index', compact('users', 'editUser','roles'));
    }

    public function EnregistrerUser(Request $request)
    { 
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'nullable'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (!empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        return redirect()
            ->route('admin.ui.users.index')
            ->with('success', 'Utilisateur créé avec succès');
    }

    public function ModifierUser(Request $request, $id)
    { 

        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'role' => 'nullable'
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles($validated['role'] ?? "");

        return redirect()
            ->route('admin.ui.users.index')
            ->with('success', 'Utilisateur modifié avec succès');
    }

    public function SupprimerUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.ui.users.index')->with('success', 'Utilisateur supprimé');
    }

    // ===================================================================
    // KNOWLEDGE BASE (kept for legacy compat)
    // ===================================================================
    public function ListeBase(Request $request)
    {
        $kbs = KnowledgeBase::all();
        $kb=null;
        if ($request->has('edit')) {
            $kb = KnowledgeBase::findOrFail($request->edit);
        }
        return view('admin.responses.knowledgebase',compact('kbs','kb'));
    }

    public function EnregistrerBase(Request $request)
    {
        $request->validate([
            'problem_keywords' => 'required',
            'solution' => 'required',
            'source' => 'nullable'
        ]);
        KnowledgeBase::create([
            'problem_keywords' => $request->problem_keywords,
            'solution' => $request->solution,
            'source' => $request->source,
            'usage_count' => 0
        ]);
        return back()->with('success', 'Ajouté avec succès');
    }

    public function ModifierBase(Request $request, $id)
    {
        $item = KnowledgeBase::findOrFail($id);
        $item->update([
            'problem_keywords' => $request->problem_keywords,
            'solution' => $request->solution,
            'source' => $request->source,
        ]);
        return redirect()->route('admin.ui.knowledge.index');
    }

    public function SupprimerBase($id)
    {
        KnowledgeBase::findOrFail($id)->delete();
        return redirect()->route('admin.ui.knowledge.index')->with('success', 'base supprimé');
    }

    // ===================================================================
    // AI RESPONSES (legacy)
    // ===================================================================
    public function ListeRespAi(Request $request)
    {
        $responsesAis = AIResponse::all();
        $Rai=null;
        if ($request->has('edit')) {
            $Rai = AIResponse::findOrFail($request->edit);
        }
        return view('admin.responses.responsesAi',compact('responsesAis','Rai'));
    }

    public function EnregistrerRespAi(Request $request)
    {
        $request->validate([
            'problem_keywords' => 'required',
            'solution' => 'required',
            'source' => 'nullable'
        ]);
        AIResponse::create([
            'problem_keywords' => $request->problem_keywords,
            'solution' => $request->solution,
            'source' => $request->source,
            'usage_count' => 0
        ]);
        return back()->with('success', 'Ajouté avec succès');
    }

    public function ModifierRAI(Request $request, $id)
    {
        $item = AIResponse::findOrFail($id);
        $item->update([
            'problem_keywords' => $request->problem_keywords,
            'solution' => $request->solution,
            'source' => $request->source,
        ]);
        return redirect()->route('admin.responsesAi.index');
    }

    public function SupprimerRai($id)
    {
        AIResponse::findOrFail($id)->delete();
        return redirect()->route('admin.responsesAi.index')->with('success', 'responses Ai supprimé');
    }

    public function ListeTickets()
    {
        $tickets = Ticket::all();
        return view('admin.responses.tickets', compact('tickets'));
    }

    public function AiMonitoring()
    {
        $ai = AIResponse::latest()->take(50)->get();
        return view('admin.ai.index', compact('ai'));
    }

    // ===================================================================
    // ROLES
    // ===================================================================
    public function ListeRole(Request $request)
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        $role= null;
        $p=null;
        if ($request->has('edit')) {
            $role = Role::with('permissions')->findOrFail($request->edit);
        }
        if ($request->has('editp')) {
            $p = Permission::findOrFail($request->editp);
        }
        return view('admin.users.roles',compact('roles','permissions','role','p'));
    }

    public function EnregistrerRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array'
        ]);
        $role = Role::create(['name' => $validated['name']]);
        $role->syncPermissions($request->permissions ?? []);
        return redirect()->route('admin.role_permissions')->with('success', 'Role créé avec succès');
    }

    public function EnregistrerPermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);
        Permission::create(['name' => $validated['name']]);
        return redirect()->route('admin.role_permissions')->with('success', 'Permission créé avec succès');
    }

    public function ModifierRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
        ]);
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);
        return redirect()->route('admin.role_permissions')->with('success', 'Role modifié avec succès');
    }

    public function ModifierPermission(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);
        $permission->update(['name' => $request->name]);
        return redirect()->route('admin.role_permissions')->with('success', 'Permission modifié avec succès');
    }

    public function SupprimerRole(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.role_permissions')->with('success', 'Role supprimé');
    }
    public function SupprimerPermission(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.role_permissions')->with('success', 'Permission supprimée');
    }
}
