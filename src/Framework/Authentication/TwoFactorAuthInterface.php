<?php
/**
 * TwoFactorAuthInterface.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */
namespace Framework\Authentication;

interface TwoFactorAuthInterface
{
    /**
     * @param TokenInterface $token
     * @return mixed
     * @throws \Exception
     */
    public function validate(TokenInterface $token);

    /**
     * @param ...$params
     * @return mixed
     * @throws \Exception
     */
    public function registerUser(...$params);

    /**
     * @param ...$params
     * @return TokenInterface
     * @throws \Exception
     */
    public function buildToken(...$params);
}