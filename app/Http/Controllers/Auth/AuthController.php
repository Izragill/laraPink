<?php


namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    protected $loginView;
    protected $redirectTo = '/admin';

    public function username()
    {
        return 'name';
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');

        return $this->loginView = (config('settings.theme') .'.login');
    }

    public function showLoginForm()
    {
        $view = property_exists($this, 'loginView') ? $this->loginView : '';

        if (view()->exists($view)) {
            return view($view)->with('title', 'Вход на сайт');
        }

        abort(404);
    }

    public function login(Request $request)
    {
        $array = $request->all();
        $remember = $request->has('remember');

//        Auth::logout();

        if(Auth::attempt(['login'=>$array['login'],'password'=>$array['password']], $remember)){
            return redirect($this->redirectTo);
        }

        return redirect()->back()
            ->withInput($request->only('login', 'remember'))
            ->withErrors(['login' => 'Данные аутентификации не верны']);
    }

    public function logout()
    {

    }
}
