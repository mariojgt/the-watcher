<?php

namespace Mariojgt\TheWatcher\Controllers\Auth\FrontAuth;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Mariojgt\TheWatcher\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Mariojgt\TheWatcher\Events\UserVerifyEvent;

class RegisterController extends Controller
{
    /**
     * @return [blade view]
     */
    public function register()
    {
        return Inertia::render('Auth/FrontLogin/Register', [
            'title' => 'Login',
            'isAdmin' => true,  // Dynamic update the logo
        ]);
    }


    /**
     * Register a new user to the aplication.
     *
     * @param Request $request
     *
     * @return [redirect]
     */
    public function userRegister(Request $request)
    {
        if (config('skeleton.register_enable') == false) {
            return Redirect::back()->with('error', 'Sorry but registration has been disable.');
        }
        // Validate the user Note the small update in the password verification
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->uncompromised()],
        ]);

        DB::beginTransaction();
        // Register the user in the database
        $user           = new User();
        $user->name     = Request('name');
        $user->email    = Request('email');
        $user->password = Hash::make(Request('password'));
        $user->save();

        // Send the verification to the user
        UserVerifyEvent::dispatch($user);

        DB::commit();
        return Redirect::back()
            ->with('success', 'Account Created with success, Please check you email for a verification link.');
    }
}
