<?php
/**
 * AbstractToken.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\Authentication;


abstract class AbstractToken implements TokenInterface
{
    protected $id;
    protected $token;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id = null)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token = null)
    {
        $this->token = $token;
    }
}