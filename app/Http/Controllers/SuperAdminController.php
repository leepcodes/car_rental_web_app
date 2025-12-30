<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function clients()
    {
         $client = User::where('user_type', 'client')->get();

        return Inertia::render('superadmin/client/client', [
            'users' => $client,
        ]);
    }

    public function operators()
    {
        $operators = User::where('user_type', 'operator')->get(); 

        return Inertia::render('superadmin/operator/operator', [
            'users' => $operators,
        ]);
    }

    public function showUser($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'user_type' => $user->user_type,
        ]);
    }

    // public function deleteOperator($id)
    // {
    //     $user = User::findOrFail($id);
    //     $user->delete();
    //     return redirect()->route('superadmin.operators');
    // }

    // public function deleteClient($id)
    // {
    // $user = User::findOrFail($id);
    // $user->delete();

    // return redirect()->route('superadmin.clients');
    
    // }
    public function deleteOperator($id)
    {
        return $this->deleteUserAndRedirect($id, 'superadmin.operators');
    }

    public function deleteClient($id)
    {
        return $this->deleteUserAndRedirect($id, 'superadmin.clients');
    }

    private function deleteUserAndRedirect($id, $redirectRoute)
    {
        $user = User::findOrFail($id);
        $user->delete();
    
    return redirect()->route($redirectRoute);
    }

    
}