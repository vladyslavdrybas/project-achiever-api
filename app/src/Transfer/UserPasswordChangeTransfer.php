<?php

declare(strict_types=1);

namespace App\Transfer;

class UserPasswordChangeTransfer extends AbstractTransfer
{
    protected string $oldpassword;
    protected string $newpassword;
    protected string $confirmpassword;

    /**
     * @return string
     */
    public function getOldpassword(): string
    {
        return $this->oldpassword;
    }

    /**
     * @param string $oldpassword
     */
    public function setOldpassword(string $oldpassword): void
    {
        $this->oldpassword = $oldpassword;
    }

    /**
     * @return string
     */
    public function getNewpassword(): string
    {
        return $this->newpassword;
    }

    /**
     * @param string $newpassword
     */
    public function setNewpassword(string $newpassword): void
    {
        $this->newpassword = $newpassword;
    }

    /**
     * @return string
     */
    public function getConfirmpassword(): string
    {
        return $this->confirmpassword;
    }

    /**
     * @param string $confirmpassword
     */
    public function setConfirmpassword(string $confirmpassword): void
    {
        $this->confirmpassword = $confirmpassword;
    }
}
