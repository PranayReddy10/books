<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\University;
use App\Department;
use App\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $university_list = University::where('status', 1)->orderBy('university_name')->get();
        $department_list = Department::where('status', 1)->orderBy('department_name')->get();
        $college_list = College::where('status', 1)->orderBy('college_name')->get();

        return view('auth.register', compact('university_list', 'department_list', 'college_list'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'phone'         => ['nullable', 'string', 'max:255'],
            'rollnumber'    => ['required', 'string', 'max:255'],
            'university'    => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'college'       => ['required', 'string', 'max:255'],
            'gender'        => ['required', 'string', 'max:20'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'usertype'      => 'User',
            'name'          => $data['name'],
            'username'      => generate_username($data['name'], $data['email']),
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'phone'         => $data['phone'] ?? '',
            'rollnumber'    => $data['rollnumber'],
            'university'    => $data['university'],
            'department_id' => $data['department_id'],
            'college'       => $data['college'],
            'gender'        => $data['gender'],
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        Auth::login($user);

        return redirect($this->redirectTo);
    }
}
