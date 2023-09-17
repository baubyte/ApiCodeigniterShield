<?php

namespace App\Controllers\Api;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    /**
     * Register endpoint
     *
     * @return mixed
     */
    public function register()
    {
        $rules = [
            "username"  => "required|is_unique[users.username]",
            "email"     => "required|is_unique[auth_identities.secret]",
            "password"  => "required",
        ];
        if (!$this->validate($rules)) {
            $response = $this->genericResponse(
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                $this->validator->getErrors(),
                true,
                []
            );
            return $this->respond($response, ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
        $userModel = new UserModel();
        $user = new User([
            "username"  => $this->request->getJsonVar("username"),
            "email"     => $this->request->getJsonVar("email"),
            "password"  => $this->request->getJsonVar("password"),
        ]);
        $userModel->save($user);
        $response = $this->genericResponse(
            ResponseInterface::HTTP_CREATED,
            "Usuario Creado Correctamente.",
            false,
            []
        );
        return $this->respond($response, ResponseInterface::HTTP_CREATED);
    }

    /**
     * Login endpoint
     *
     * @return mixed
     */
    public function login()
    {

        if (auth()->loggedIn()) {
            auth()->logout();
        }
        $rules = [
            "email"     => "required|is_not_unique[auth_identities.secret]",
            "password"  => "required",
        ];
        if (!$this->validate($rules)) {
            $response = $this->genericResponse(
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                $this->validator->getErrors(),
                true,
                []
            );
            return $this->respond($response, ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
        $credentials = [
            "email"     => $this->request->getJsonVar("email"),
            "password"  => $this->request->getJsonVar("password"),
        ];
        $loginAttempt = auth()->attempt($credentials);
        if (!$loginAttempt->isOK()) {
            $response = $this->genericResponse(
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                "Credenciales Incorrectas",
                true,
                []
            );
            return $this->respond($response, ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
        $userModel = new UserModel();
        $user = $userModel->findById(auth()->id());
        $token = $user->generateAccessToken("Api Shield");
        $authToken = $token->raw_token;
        $response = $this->genericResponse(
            ResponseInterface::HTTP_OK,
            "Inicio de Sesión Correcto",
            false,
            [
                "token" => $authToken
            ]
        );
        return $this->respond($response, ResponseInterface::HTTP_OK);
    }

    /**
     * Profile endpoint
     *
     * @return mixed
     */
    public function profile()
    {
        if (auth("tokens")->loggedIn()) {
            $userId = auth()->id();
            $userModel = new UserModel();
            $user = $userModel->findById($userId);
            $response = $this->genericResponse(
                ResponseInterface::HTTP_OK,
                "Inicio de Sesión Correcto",
                false,
                [
                    "user" => $user
                ]
            );
            return $this->respond($response, ResponseInterface::HTTP_OK);
        }
    }

    /**
     * Logout endpoint
     *
     * @return mixed
     */
    public function logout()
    {
        if (auth("tokens")->loggedIn()) {
            auth()->logout();
            auth()->user()->revokeAllAccessTokens();
            $response = $this->genericResponse(
                ResponseInterface::HTTP_OK,
                "Cierre de sesión correcto.",
                false,
                []
            );
            return $this->respond($response, ResponseInterface::HTTP_OK);
        }
    }
    /**
     * Invalid Request
     *
     * @return ResponseInterface
     */
    public function invalidRequest()
    {
        $response = $this->genericResponse(
            ResponseInterface::HTTP_FORBIDDEN,
            "Por favor inicie sesión",
            true,
            []
        );
        return $this->respond($response, ResponseInterface::HTTP_FORBIDDEN);
    }
    /**
     * Generic Response
     *
     * @param integer $status
     * @param string $message
     * @param boolean $error
     * @param array $data
     * @return array
     */
    private function genericResponse(int $status, string|array $message, bool $error, array $data): array
    {
        return [
            "status"    => $status,
            "message"   => $message,
            "error"     => $error,
            "data"      => $data
        ];
    }
}
