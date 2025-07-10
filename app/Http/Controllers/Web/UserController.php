<?php

namespace App\Http\Controllers\Web;

use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $users = User::query()
            ->select(['code', 'name', 'email', 'profile_image', 'role_id', 'is_active', 'created_at'])
            ->with('role:id,code,name')
            ->orderBy('name')
            ->orderBy('id')
            ->get();
        
        $roles = UserRole::query()
            ->select(['id', 'code', 'name'])
            ->orderBy('id')
            ->get();

        return view('users.index', compact('users', 'roles', 'user'));
    }


    public function store(StoreRequest $request)
    {
        DB::beginTransaction();

        dd($request->name);

        try {
            do {
                $code = NumberHelper::randomDigit(6);
            } while (DB::table('users')->where('code', $code)->exists());

            $user = User::create(array_merge(
                $request->only(['name', 'email', 'phone', 'address']),
                [
                    'code' => $code,
                    'password' => bcrypt('password'),
                    'profile_image' => 'assets/images/user-profiles/' . rand(1, 10) . '.jpg',
                    'role_id' => $roleId,
                ]
            ));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create user: ' . $e->getMessage());
        }

        return redirect()->route('users.index')->with('success', 'User has been created');
    }

    public function edit($code)
    {
        $user = User::where('code', $code)->firstOrFail();
        $roles = DB::table('user_roles')->get();
        
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateRequest $request, $code)
    {
        $user = User::where('code', $code)->first();

        if (is_null($user)) {
            return back()->with('error', 'User not found');
        }

        DB::beginTransaction();

        try {
            $updateData = [
                'name' => $request->name,
                'is_active' => $request->isActive,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }

            $user->update($updateData);

            if (!$user->is_active) {
                $user->tokens()->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update user: ' . $e->getMessage());
        }

        return redirect()->route('users.index')->with('success', 'User has been updated');
    }

    public function show($code)
    {
        $user = User::where('code', $code)
                   ->with(['role:id,code,name', 'tenants'])
                   ->firstOrFail();
                   
        return view('users.show', compact('user'));
    }
}