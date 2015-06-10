<?php
/**
 * TokenInterface.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */
namespace Framework\Authentication;

interface TokenInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @param null|integer $id
     */
    public function setId($id = null);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param null|string $token
     */
    public function setToken($token = null);

    /**
     * @return bool
     */
    public function isValid();
}