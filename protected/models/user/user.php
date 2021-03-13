<?php
/**
 * ModelCore Class For User Model
 */
class User extends ModelCore implements IModelAuth
{
    const ERROR_INVALID_CRYPT_CONFIG = 'Crypt Config Has Bad Format';

    const USER_NOT_SIGNED_IN = 'User Not Log In';

    /**
     * Sign In By Auth Token
     *
     * @param string|null $authToken Authentication Token
     *
     * @return bool Is User Successfully Signed In
     */
    public function signInByToken(?string $authToken = null): bool
    {
        throw new Exception('Method Is Not Implemented');
    }

    /**
     * Sign In
     *
     * @param array $inputData Input Data
     *
     * @return UserFormObject User Form Object
     */
    public function signIn(array $inputData): UserFormObject
    {
        $formObject = new UserFormObject($inputData);

        if ($formObject->hasErrors()) {
            return $formObject;
        }

        $email    = $formObject->getEmail();
        $password = $formObject->getPassword();

        try {
            if ($this->signinByLoginAndPassword($email, $password)) {
                $formObject->setSuccess();

                return $formObject;
            }
        } catch (Exception $exp) {
            $formObject->setError($exp->getMessage());
        }

        if (!$formObject->hasErrors()) {
            $formObject->setError(UserFormObject::ERROR_CAN_NOT_SIGNIN);
        }

        return $formObject;
    }

    /**
     * Change User Password
     *
     * @param array $inputData Input Data
     *
     * @return UserFormObject User Form Object
     */
    public function changePassword(array $inputData): UserFormObject
    {
        $formObject = new UserFormObject($inputData);

        if (!$this->isSignedIn()) {
            $formObject->setError(static::USER_NOT_SIGNED_IN);

            return $formObject;
        }

        $id = $this->session->get('id');

        $formObject = new UserFormObject($inputData);

        if ($formObject->hasErrors()) {
            return $formObject;
        }

        $password = $formObject->getPassword();

        $cryptConfig = $this->getConfig('crypt');

        if (
            !array_key_exists('salt', $cryptConfig) ||
            empty($cryptConfig['salt'])
        ) {
            throw new Exception(static::ERROR_INVALID_CRYPT_CONFIG);
        }

        $cryptPlugin = $this->getPlugin('crypt');

        $hashSalt     = $cryptConfig['salt'];
        $passwordHash = $cryptPlugin->getHash($password, $hashSalt);

        if ($this->store->updateRowPasswordHash($id, $passwordHash)) {
            $formObject->setSuccess();
            
            return $formObject;
        }

        return $formObject;
    }

    /**
     * Sign In By Login (Email) And Password
     *
     * @param string|null $email    User Email
     * @param string|null $password User Password
     *
     * @return bool Is User Successfully Signed In
     */
    public function signinByLoginAndPassword(
        ?string $email    = null,
        ?string $password = null
    ): bool
    {
        if (empty($email)) {
            throw new Exception(UserFormObject::ERROR_EMAIL_NOT_SET);
        }

        if (empty($password)) {
            throw new Exception(UserFormObject::ERROR_PASSWORD_NOT_SET);
        }

        $cryptConfig = $this->getConfig('crypt');

        if (
            !array_key_exists('salt', $cryptConfig) ||
            empty($cryptConfig['salt'])
        ) {
            throw new Exception(static::ERROR_INVALID_CRYPT_CONFIG);
        }

        $cryptPlugin = $this->getPlugin('crypt');

        $hashSalt     = $cryptConfig['salt'];
        $passwordHash = $cryptPlugin->getHash($password, $hashSalt);

        $row = $this->store->getRowByEmail($email);

        if (empty($row)) {
            throw new Exception(
                UserFormObject::ERROR_INVALID_EMAIL_OR_PASSWORD
            );
        }

        $user = $this->getVO($row);

        if ($user->getPasswordHash() != $passwordHash) {
            throw new Exception(
                UserFormObject::ERROR_INVALID_EMAIL_OR_PASSWORD
            );
        }

        $token = sprintf('%s%s', (string) time(), $email);
        $token = $cryptPlugin->getHash($token, $hashSalt);

        $this->session->set('id', $user->getId());
        $this->session->set('token', $token);

        return $this->store->updateRowToken($user->getId(), $token);
    }

    /**
     * Check Is User Signed In
     *
     * @return bool Is User Signed In
     */
    public function isSignedIn(): bool
    {
        $id    = $this->session->get('id');
        $token = $this->session->get('token');

        if (empty($id) || empty($token)) {
            return false;
        }

        $row = $this->store->getRowById((int) $id);

        if (empty($row)) {
            return false;
        }

        $user = $this->getVO($row);

        return $user->getToken() == $token;
    }

    /**
     * Signed Out User
     *
     * @return bool Is User Successfully Signed Out
     */
    public function signOut(): bool
    {
        if (!$this->isSignedIn()) {
            return false;
        }

        $id = $this->session->get('id');

        $this->session->remove('id');
        $this->session->remove('token');

        return $this->store->updateRowToken($id);
    }
}
