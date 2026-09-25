<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show customer profile page.
     */
    public function show()
    {
        $user = auth()->user();

        // Stats para sa profile
        $stats = [
            'total_orders' => Order::where('customer_id', $user->id)->count(),
            'delivered_orders' => Order::where('customer_id', $user->id)
                ->where('status', 'delivered')
                ->count(),
            'total_spent' => Order::where('customer_id', $user->id)
                ->where('status', 'delivered')
                ->sum('total_amount'),
        ];

        return view('customer.profile', compact('user', 'stats'));
    }

    /**
     * Update customer profile (name, phone).
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete customer account permanently.
     */
    public function destroy(Request $request)
    {
        $user = auth()->user();

        // 1. Validate password confirmation
        $request->validate([
            'password' => 'required|string',
            'confirm_delete' => 'required|accepted',
        ]);

        // 2. Check password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Incorrect password. Account not deleted.'
            ]);
        }

        // 3. Check kung may active orders
        $hasActiveOrders = Order::where('customer_id', $user->id)
            ->whereNotIn('status', ['delivered', 'cancelled', 'rejected', 'no_rider'])
            ->exists();

        if ($hasActiveOrders) {
            return back()->withErrors([
                'password' => 'Cannot delete account — may active order(s) ka pa. Please wait for them to complete or cancel them first.'
            ]);
        }

        // 4. Logout muna bago i-delete
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 5. Delete user (cascade delete sa orders via FK)
        DB::transaction(function () use ($user) {
            // Optional: anonymize orders instead of deleting them
            // Order::where('customer_id', $user->id)->update([
            //     'customer_id' => null,
            //     'delivery_address' => '[deleted user]',
            // ]);

            $user->delete();
        });

        return redirect()->route('login')
            ->with('success', 'Your account has been permanently deleted. We\'re sad to see you go! 😢');
    }
}