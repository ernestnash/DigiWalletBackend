<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
class WebController extends Controller
{
    // public function index()
    // {
    //     return view('auth.login');
    // }
    public function Login(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|numeric|min:10',
            'pin' => 'required|min:4',
        ]);
        $credentials = $request->only('phone_number', 'pin');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')
                ->withSuccess('Signed in');
        }
        return redirect()->back()->with('Error','Login details are not valid');
    }
    // public function registration()
    // {
    //     return view('auth.register');
    // }
    public function Registration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $data = $request->all();
        $check = $this->create($data);
        return redirect("dashboard")->withSuccess('You have signed-in');
    }
    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }
    // public function dashboard()
    // {
    //     if (Auth::check()) {
    //         return view('auth.dashboard');
    //     }
    //     return redirect("login")->withSuccess('You are not allowed to access');
    // }
    public function signOut()
    {
        Session::flush();
        Auth::logout();
        return Redirect('login');
    }
}