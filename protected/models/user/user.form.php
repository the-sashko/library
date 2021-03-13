<?php
/**
 * Class For User Form Object
 */
class UserFormObject extends FormObject
{
    const EMAIL_PATTERN = '/^(.*?)@(.*?)\.(.*?)$/su';

    const PASSWORD_MIN_LENGTH = 8;

    const ERROR_EMAIL_NOT_SET            = 'Email Is Not Set';
    const ERROR_EMAIL_HAS_INVALID_FORMAT = 'Email Has Invalid Format';

    const ERROR_PASSWORD_NOT_SET        = 'Password Is Not Set';
    const ERROR_PASSWORD_TOO_SHORT      = 'Password Too Short';
    const ERROR_PASSWORDS_ARE_NOT_MATCH = 'Passwords Not Match';
    
    const ERROR_INVALID_EMAIL_OR_PASSWORD = 'Invalid Email Or Password';

    const ERROR_CAN_NOT_SIGNIN = 'Can Not Sign In';

    /**
     * Check Input Data
     */
    public function checkInputData(): void
    {
        $this->_checkEmail();
        $this->_checkPassword();
    }

    /**
     * Is User Email Exist In Form
     *
     * @return bool Is User Email Exist In Form
     */
    public function hasEmail(): bool
    {
        return $this->has('email');
    }

    /**
     * Get User Email From Form
     *
     * @return string|null Email
     */
    public function getEmail(): ?string
    {
        return $this->get('email');
    }

    /**
     * Get User Password From Form
     *
     * @return string|null Password
     */
    public function getPassword(): ?string
    {
        return $this->get('password');
    }

    /**
     * Is Password For Check Exist In Form
     *
     * @return bool Is Password For Check Exist In Form
     */
    public function hasPasswordForCheck(): bool
    {
        return $this->has('password_check');
    }

    /**
     * Get User Password (Retyped) From Form
     *
     * @return string|null Retyped Password For Check
     */
    public function getPasswordForCheck(): ?string
    {
        return $this->get('password_check');
    }

    /**
     * Check User Email
     *
     * @return bool Is Email Has Correct Format
     */
    private function _checkEmail(): bool
    {
        if (!$this->hasEmail()) {
            return true;
        }

        $email = $this->getEmail();

        if (empty($email)) {
            $this->setError(static::ERROR_EMAIL_NOT_SET);

            return false;
        }

        if (!preg_match(static::EMAIL_PATTERN, $email)) {
            $this->setError(static::ERROR_EMAIL_HAS_INVALID_FORMAT);

            return false;
        }

        return true;
    }

    /**
     * Check User Password
     *
     * @return bool Is Password Has Correct Format
     */
    private function _checkPassword(): bool
    {
        $password = $this->getPassword();

        if (empty($password)) {
            $this->setError(static::ERROR_PASSWORD_NOT_SET);

            return false;
        }

        if (strlen($password) < static::PASSWORD_MIN_LENGTH) {
            $this->setError(static::ERROR_PASSWORD_TOO_SHORT);

            return false;
        }

        if (!$this->hasPasswordForCheck()) {
            return true;
        }

        if ($password != $this->getPasswordForCheck()) {
            $this->setError(static::ERROR_PASSWORDS_ARE_NOT_MATCH);

            return false;
        }

        return true;
    }
}
