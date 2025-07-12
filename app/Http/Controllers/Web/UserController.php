<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $users = User::with('role:id,code,name')
            ->when($request->search && $request->search_type == 'name', function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->search && $request->search_type == 'username', function($query) use ($request) {
                $query->where('username', 'like', '%' . $request->search . '%');
            })
            ->when($request->role, function($query) use ($request) {
                $query->where('role_id', $request->role);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        $roles = UserRole::orderBy('id')->get(['id', 'code', 'name']);

        return view('users.index', compact('users', 'roles', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'username' => 'required|string|unique:users',
            'address' => 'required|string',
            'role_id' => 'required|exists:user_roles,id',
            'password' => 'required|string|min:3|confirmed'
        ]);

        DB::beginTransaction();

        try {
            User::create([
                'code' => fake()->numerify('#######'),
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'profile_image' => 'assets/images/user-profiles/'.rand(1, 10).'.jpg',
                'is_active' => true
            ]);

            DB::commit();
            return redirect()->route('users.index')->with('success', 'User created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create user: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$id,
            'address' => 'required|string',
            'role_id' => 'required|exists:user_roles,id',
            'is_active' => 'sometimes|boolean',
            'password' => 'nullable|string|min:3|confirmed'
        ]);

        $user = User::findOrFail($id);

        DB::beginTransaction();

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'role_id' => $request->role_id,
                'is_active' => $request->is_active ?? false,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            DB::commit();
            return redirect()->route('users.index')->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update user: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $user = User::with(['role:id,code,name', 'tenants'])->findOrFail($id);
        return view('users.show', compact('user'));
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $user = User::findOrFail($id);
            
            if ($user->id === auth()->id()) {
                return back()->with('error', 'You cannot delete your own account');
            }
            
            $user->delete();
            
            DB::commit();
            return redirect()->route('users.index')->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete user: '.$e->getMessage());
        }
    }
}