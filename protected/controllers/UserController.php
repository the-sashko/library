<?php
/**
 * User Controller Class
 */
class UserController extends ControllerCore
{
    /**
     * Display Login Page
     */
    public function actionLogin(): void
    {
        if (
            array_key_exists('isSignedIn', $this->commonData) &&
            (bool) $this->commonData['isSignedIn']
        ) {
            $this->redirect('/');
        }

        $formErrors = null;
        $userModel  = $this->getModel('user');

        $pagePath = [
            '/login/' => __t('Sign In')
        ];

        $formData = [
            'errors'   => null,
            'email'    => null,
            'password' => null
        ];

        if (empty($this->post)) {
            $this->render(
                'login',
                [
                    'formData' => $formData,
                    'pagePath' => $pagePath,
                    'seoTitle' => __t('Sign In')
                ]
            );
        }

        $formObject = $userModel->signIn($this->post);

        if ($formObject->isStatusSuccess()) {
            $this->redirect('/');
        }

        $formData           = array_merge($formData, $formObject->exportRow());
        $formData['errors'] = $formObject->getErrors();

        $this->render(
            'login',
            [
                'formData' => $formData,
                'pagePath' => $pagePath
            ]
        );
    }

    /**
     * Display Logout Page
     */
    public function actionLogout(): void
    {
        $this->getModel('user')->signOut();
        $this->redirect('/');
    }

    /**
     * Display Profile Page
     */
    public function actionProfile(): void
    {
        if (
            !array_key_exists('isSignedIn', $this->commonData) ||
            !(bool) $this->commonData['isSignedIn']
        ) {
            $this->redirect('/login/');
        }

        $userModel = $this->getModel('user');

        $formErrors = null;

        $pagePath = [
            '/profile/' => __t('Profile')
        ];

        $formData = [
            'errors'         => null,
            'password'       => null,
            'password_check' => null
        ];

        if (empty($this->post)) {
            $this->render(
                'profile',
                [
                    'formData' => $formData,
                    'pagePath' => $pagePath
                ]
            );
        }

        $formObject = $userModel->changePassword($this->post);

        if ($formObject->isStatusSuccess()) {
            $this->redirect('/');
        }

        $formData           = array_merge($formData, $formObject->exportRow());
        $formData['errors'] = $formObject->getErrors();

        $this->render(
            'profile',
            [
                'formData' => $formData,
                'pagePath' => $pagePath
            ]
        );
    }
}
