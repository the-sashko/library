<?php
class UserHook extends HookCore
{
    public function checkUser(): void
    {
        if (!$this->hasEntityParam('isSignedIn')) {
            $isSignedIn = $this->getModel('user')->isSignedIn();

            $this->setEntityParam('isSignedIn', $isSignedIn);
        }
    }
}
