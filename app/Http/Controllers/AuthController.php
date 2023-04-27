<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateInfoRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public UserService $userService;
    public function show_user(Request $request)
    {
        $response = $this->userService->get('show_user');
        return $response;
    }
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function register(RegisterRequest $request)
    {
        $data =   $request->only('first_name', 'last_name', 'email', 'password')
            + [
                'is_admin' => $request->path() === 'api/admin/register' ? 1 : 0
            ];
        $this->userService->post('register', $data);
        // $user = User::create(
        //     $request->only('first_name', 'last_name', 'email')
        //         + [
        //             'password' => \Hash::make($request->input('password')),
        //             'is_admin' => $request->path() === 'api/admin/register' ? 1 : 0
        //         ]
        // );
        // return response($user, Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $scope = $request->path() === 'api/admin/login' ? 'admin' : "ambassador";
        $data = $request->only('email', 'password') + compact('scope');
        $response = $this->userService->post('login', $data);
        $cookie =cookie('token',$response['token'],60*24);
        return response([
            'message' => 'success',
        ])->withCookie($cookie);
    }

    public function user(Request $request)
    {
        $user = $request->user();

        dd($user);
        return new UserResource($user);
    }

    public function logout()
    {
        $response = $this->userService->post('logout',[]);

        return $response;
    }

    public function updateInfo(UpdateInfoRequest $request)
    {
        $user = $request->user();

        $user->update($request->only('first_name', 'last_name', 'email'));

        return response($user, Response::HTTP_ACCEPTED);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user();

        $user->update([
            'password' => \Hash::make($request->input('password'))
        ]);

        return response($user, Response::HTTP_ACCEPTED);
    }
}
