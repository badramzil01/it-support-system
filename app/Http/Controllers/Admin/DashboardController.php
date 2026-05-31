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



class DashboardController extends Controller
{
    public function index()
    {
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', 'open')->count();
        $resolvedTickets = Ticket::where('status', 'resolved')->count();
        $escalatedTickets = Ticket::where('is_escalated', 1)->count();
        $urgentTickets = Ticket::where('priority', 'urgent')->count();
        $avgConfidence = Ticket::avg('confidence');
        $byCategory = Ticket::select('category')
            ->selectRaw('count(*) as total')
            ->groupBy('category')
            ->get();
        return view('admin.dashboard',compact('totalTickets','openTickets','resolvedTickets','escalatedTickets','urgentTickets','avgConfidence','byCategory'));
    }

    public function ListeUsers(Request $request)
    {
        $users = User::with('roles')->get();
        $user = null;
        $roles = Role::all();

        if ($request->has('edit')) {
        $user = User::findOrFail($request->edit);
        }

        return view('admin.users.index', compact('users', 'user','roles'));
    }

    public function EnregistrerUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'roles' => 'nullable|array'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (!empty($validated['roles'])) {
            $user->assignRole($validated['roles']);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès');
    }

    public function ModifierUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'roles' => 'nullable|array'
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $user->syncRoles($validated['roles'] ?? []);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur modifié avec succès');
    }

    public function SupprimerUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé');
    }


///////////////////////////////////////////////////
           /*KNOWLEDGE BASE*/
//////////////////////////////////////////////////
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
        /* ActivityLogger::log(
        action: 'CREATE_USER',
        model: 'User',
        modelId: $user->id,
        description: 'Création d\'un utilisateur'
        );*/

        return redirect()->route('admin.kb.index');
    }

    public function SupprimerBase($id)
    {
        KnowledgeBase::findOrFail($id)->delete();
        return redirect()->route('admin.kb.index')->with('success', 'base supprimé');
    }


///////////////////////////////////////////////////
           /*AI Response*/
//////////////////////////////////////////////////
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
        $ai = AiResponse::latest()->take(50)->get();

        return view('admin.ai.index', compact('ai'));
    }


    ////////////////////////////////////////////
                 /*roles*/
    ////////////////////////////////////////////
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

        $role = Role::create([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()
            ->route('admin.role_permissions')
            ->with('success', 'Role créé avec succès');
    }
    public function EnregistrerPermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $role = Permission::create([
            'name' => $validated['name'],
        ]);

        return redirect()
            ->route('admin.role_permissions')
            ->with('success', 'Permission créé avec succès');
    }

    public function ModifierRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        $role->syncPermissions( $request->permissions ?? []);

        return redirect()
            ->route('admin.role_permissions')
            ->with('success', 'Role modifié avec succès');
    }
     public function ModifierPermission(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update([
            'name' => $request->name,
        ]);


        return redirect()
            ->route('admin.role_permissions')
            ->with('success', 'Permission modifié avec succès');
    }

    public function SupprimerRole(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.role_permissions')->with('success', 'Utilisateur supprimé');
    }
    public function SupprimerPermission(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.role_permissions')->with('success', 'Utilisateur supprimé');
    }

}
