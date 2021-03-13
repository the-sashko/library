<?php
/**
 * ValuesObject Class For User Model
 */
class UserValuesObject extends ValuesObject
{
    /**
     * Get User ID
     *
     * @return int ID
     */
    public function getId(): int
    {
        return (int) $this->get('id');
    }

    /**
     * Get User Login
     *
     * @return string Login
     */
    public function getLogin(): string
    {
        return (string) $this->get('login');
    }

    /**
     * Get User Email
     *
     * @return string Email
     */
    public function getEmail(): string
    {
        return (string) $this->get('email');
    }

    /**
     * Get User Password Hash
     *
     * @return string Password Hash
     */
    public function getPasswordHash(): string
    {
        return (string) $this->get('password_hash');
    }

    /**
     * Get User Token
     *
     * @return string|null Token
     */
    public function getToken(): ?string
    {
        $token = $this->get('token');

        if (empty($token)) {
            return null;
        }

        return (string) $token;
    }

    /**
     * Set User Login
     *
     * @param string|null $login Login
     */
    public function setLogin(?string $login = null): void
    {
        $this->set('login', $login);
    }

    /**
     * Set User Email
     *
     * @param string|null $email Email
     */
    public function setEmail(?string $email = null): void
    {
        $this->set('email', $email);
    }

    /**
     * Set User Password Hash
     *
     * @param string|null $passwordHash Password Hash
     */
    public function setPasswordHash(?string $passwordHash = null): void
    {
        $this->set('password_hash', $passwordHash);
    }
    /**
     * Set User Token
     *
     * @param string|null $token Token
     */
    public function setToken(?string $token = null): void
    {
        $this->set('token', $token);
    }
}
