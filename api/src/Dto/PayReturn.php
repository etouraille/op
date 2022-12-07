<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class PayReturn
{

    #[Groups(['pay'])]
    private bool $success;
    #[Groups(['pay'])]
    private $isIntent;
    #[Groups(['pay'])]
    private $id;
    #[Groups(['pay'])]
    private $error;

    public function __construct($success, $isIntent, $id, $error)
    {
        $this->success = $success;
        $this->isIntent = $isIntent;
        $this->id = $id;
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @param mixed $success
     */
    public function setSuccess($success): void
    {
        $this->success = $success;
    }

    /**
     * @return mixed
     */
    public function getIsIntent()
    {
        return $this->isIntent;
    }

    /**
     * @param mixed $isIntent
     */
    public function setIsIntent($isIntent): void
    {
        $this->isIntent = $isIntent;
    }

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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setError($error): void
    {
        $this->error = $error;
    }




}