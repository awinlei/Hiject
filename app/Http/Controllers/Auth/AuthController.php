<?php

/*
 * This file is part of Hiject.
 *
 * Copyright (C) 2016 Hiject Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hiject\Controller;

/**
 * Authentication Controller.
 */
class AuthController extends BaseController
{
    /**
     * Display the form login.
     *
     * @param array $values
     * @param array $errors
     */
    public function login(array $values = [], array $errors = [])
    {
        if ($this->userSession->isLogged()) {
            $this->response->redirect($this->helper->url->to('DashboardController', 'index'));
        } else {
            $this->response->html($this->helper->layout->app('auth/index', [
                'captcha'   => !empty($values['username']) && $this->userLockingModel->hasCaptcha($values['username']),
                'errors'    => $errors,
                'values'    => $values,
                'no_layout' => true,
                'title'     => t('Login'),
            ]));
        }
    }

    /**
     * Check credentials.
     */
    public function check()
    {
        $values = $this->request->getValues();
        $this->sessionStorage->hasRememberMe = !empty($values['remember_me']);
        list($valid, $errors) = $this->authValidator->validateForm($values);

        if ($valid) {
            $this->redirectAfterLogin();
        } else {
            $this->login($values, $errors);
        }
    }

    /**
     * Logout and destroy session.
     */
    public function logout()
    {
        if (!DISABLE_LOGOUT) {
            $this->sessionManager->close();
            $this->response->redirect($this->helper->url->to('AuthController', 'login'));
        } else {
            $this->response->redirect($this->helper->url->to('DashboardController', 'index'));
        }
    }

    /**
     * Redirect the user after the authentication.
     */
    private function redirectAfterLogin()
    {
        if (isset($this->sessionStorage->redirectAfterLogin) && !empty($this->sessionStorage->redirectAfterLogin) && !filter_var($this->sessionStorage->redirectAfterLogin, FILTER_VALIDATE_URL)) {
            $redirect = $this->sessionStorage->redirectAfterLogin;
            unset($this->sessionStorage->redirectAfterLogin);
            $this->response->redirect($redirect);
        } else {
            $this->response->redirect($this->helper->url->to('DashboardController', 'index'));
        }
    }
}
