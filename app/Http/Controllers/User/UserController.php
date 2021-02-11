<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $error = null;

    private function error(int $error = null, string $message = null, int $status = 400): JsonResponse
    {
        if ($error) {
            $this->error = $error;
        }

        switch ($this->error) {
            case 1:
                $message = 'Bad id or user not found';
                break;
            case 2:
                $message = json_decode($message);
                break;
            case 3:
                $message = 'Bad company';
                break;
            case 4:
                $message = 'Error: #' . $this->error;
                Log::error('MyError', [
                    'Class' => 'UserController',
                    'Code' => $this->error,
                    'Comment' => 'Impossible to create an user'
                ]);
                $status = 500;
                break;
            case 5:
                $message = 'Error: #' . $this->error;
                Log::error('MyError', [
                    'Class' => 'UserController',
                    'Code' => $this->error,
                    'Comment' => 'Impossible to create an user data'
                ]);
                $status = 500;
                break;
            case 6:
                $message = 'Error: #' . $this->error;
                Log::error('MyError', [
                    'Class' => 'UserController',
                    'Code' => $this->error,
                    'Comment' => 'Impossible to create an stripe user'
                ]);
                $status = 500;
                break;
            case 7:
                $message = 'Error: #' . $this->error;
                Log::error('MyError', [
                    'Class' => 'UserController',
                    'Code' => $this->error,
                    'Comment' => 'Problem creating userIntegration'
                ]);
                $status = 500;
                break;
            case 8:
                $message = 'Error: #' . $this->error;
                Log::error('MyError', [
                    'Class' => 'UserController',
                    'Code' => $this->error,
                    'Comment' => 'Impossible to create an crisp user'
                ]);
                $status = 500;
                break;
            case 9:
                $message = 'Error: #' . $this->error;
                Log::error('MyError', [
                    'Class' => 'UserController',
                    'Code' => $this->error,
                    'Comment' => 'Problem updating userIntegration'
                ]);
                $status = 500;
                break;
            case 10:
                $message = 'Error: #' . $this->error;
                Log::error('MyError', [
                    'Class' => 'UserController',
                    'Code' => $this->error,
                    'Comment' => 'Problem updating user crisp'
                ]);
                $status = 500;
                break;
            case 11:
                $message = 'Error: #' . $this->error;
                Log::error('MyError', [
                    'Class' => 'UserController',
                    'Code' => $this->error,
                    'Comment' => 'Problem updating user stripe'
                ]);
                $status = 500;
                break;
            default:
                $message ? :$message = 'Undefined error';
        }

        return response()->json([
            'error' => $this->error,
            'message' => $message
        ], $status);
    }

    public function index(): JsonResponse
    {
        return response()
            ->json(User::with('data')
                ->get());
    }

    public function show(Request $request): JsonResponse
    {
        $user = User::where('id', $request->id)
            ->with('data')
            ->first();
        if (!$user) {
            return $this->error(1);
        }
        return response()
            ->json($user);
    }

    public function store(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'companyId' => ['required', 'numeric'],
            'email' => ['required', 'email', 'unique:User'],
            'indicMobile' => ['required', 'numeric'],
            'mobile' => ['required', 'numeric', 'unique:User'],
            'emailValidated' => ['nullable'],
            'emailValidatedExp' => ['date'],
            'resetPassword' => ['nullable'],
            'resetPasswordExp' => ['date'],
            'data.firstName' => ['required', 'string'],
            'data.lastName' => ['required', 'string'],
            'data.dateOfBirth' => ['required', 'date'],
            'data.gender' => ['required', 'numeric', 'min:0', 'max:2'],
        ]);
        if ($validation->fails()) {
            return $this->error(2, json_encode($validation->errors()));
        }

        if (!$user = User::create($request->all())) {
            return $this->error(4);
        } elseif (!$user->data()->create($request->data)) {
            $user->delete();
            return $this->error(5);
        }

        return response()
            ->json(array_merge($user->toArray(), [
                'data' => $user->data->toArray()
            ]));
    }

    public function update(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'companyId' => ['numeric'],
            'email' => ['email', 'unique:User'],
            'indicMobile' => ['numeric'],
            'mobile' => ['numeric', 'unique:User'],
            'emailValidated' => ['nullable'],
            'emailValidatedExp' => ['date'],
            'resetPassword' => ['nullable'],
            'resetPasswordExp' => ['date'],
            'data.firstName' => ['string'],
            'data.lastName' => ['string'],
            'data.dateOfBirth' => ['date'],
            'data.gender' => ['numeric', 'min:0', 'max:2'],
        ]);
        if (!$user = User::where('id', $request->id)->first()) {
            return $this->error(1);
        } elseif ($validation->fails()) {
            return $this->error(2, json_encode($validation->errors()));
        }

        if (!$user->update($request->all())) {
            return $this->error();
        } elseif ($request->data && !$user->data()->update($request->data)) {
            return $this->error();
        }

        return response()
            ->json(array_merge($user->toArray(), [
                'data' => $user->data->toArray()
            ]));
    }
}
