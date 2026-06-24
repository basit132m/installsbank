<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManagerPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    public function index()
    {
        $managers = User::where('role', 'manager')->with('managerPermissions')->latest()->get();
        return view('admin.managers.index', compact('managers'));
    }

    public function create()
    {
        return view('admin.managers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'manager',
            'status' => 'active',
        ]);

        ManagerPermission::create(array_merge(
            ['user_id' => $user->id],
            $this->extractPermissions($request)
        ));

        return redirect()->route('admin.managers.index')->with('success', 'Manager created successfully.');
    }

    public function editPermissions(User $user)
    {
        $permissions = $user->managerPermissions ?: new ManagerPermission();
        return view('admin.managers.permissions', compact('user', 'permissions'));
    }

    public function updatePermissions(Request $request, User $user)
    {
        ManagerPermission::updateOrCreate(
            ['user_id' => $user->id],
            $this->extractPermissions($request)
        );
        return redirect()->route('admin.managers.index')->with('success', 'Permissions updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'Manager deleted.');
    }

    private function extractPermissions(Request $request): array
    {
        $perms = [
            'can_manage_publishers', 'can_view_publishers',
            'can_manage_test_periods', 'can_manage_publisher_websites',
            'can_manage_contracts', 'can_manage_contract_requests',
            'can_manage_rate_increase_requests',
            'can_manage_rates', 'can_manage_install_rates',
            'can_manage_tracking', 'can_manage_blacklisted_domains',
            'can_manage_withdrawals', 'can_view_withdrawals',
            'can_manage_ad_presets',
            'can_manage_advertisers', 'can_manage_campaigns',
            'can_view_fraud_alerts', 'can_resolve_fraud_alerts',
            'can_manage_support', 'can_manage_live_chat',
            'can_view_stats',
            'can_send_broadcast_emails',
        ];
        $result = [];
        foreach ($perms as $perm) {
            $result[$perm] = $request->boolean($perm);
        }
        return $result;
    }
}
