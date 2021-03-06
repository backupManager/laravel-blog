<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Repositories\UserRepository;
use App\User;
use Illuminate\Http\Request;
use Validator;

class RegisterController extends Controller
{
    protected $userRepository;


    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->middleware('guest');
    }


    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|alpha_dash|max:16|min:3|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        if (mb_substr_count($request->get('name'), '_') > 1 || mb_substr_count($request->get('name'), '-') > 1) {
            return back()->withInput()->withErrors("name's '-' and '_' max count is 1.");
        }

        $this->validator($request->all())->validate();

        auth()->login($this->create($request->all()));

        $this->userRepository->clearCache();

        return redirect()->route('post.index');
    }


    protected function validator(array $data)
    {

        return Validator::make($data, [
            'name' => 'required|alpha_dash|max:16|min:3|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'avatar' => config('app.avatar')
        ]);
    }
}
