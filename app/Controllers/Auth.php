<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        return view('upload'); // Redirect to upload_form for modal
    }

    public function register()
    {
        return view('upload'); // Redirect to upload_form for modal
    }

    public function create()
    {
        $model = new UserModel();
        $data = [
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];
        $model->save($data);
        return redirect()->to('/auth/login')->with('success', 'Registration successful! You can now log in.');
    }

    public function check()
    {
        $model = new UserModel();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $model->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            session()->set('user_id', $user['id']);
            return redirect()->to('/')->with('success', 'Logged in successfully!');
        } else {
            return redirect()->back()->with('error', 'Invalid username or password.');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('success', 'Logged out successfully!');
    }
}
