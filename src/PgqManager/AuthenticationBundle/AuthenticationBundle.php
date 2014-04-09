<?php

namespace PgqManager\AuthenticationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class  AuthenticationBundle extends Bundle
{
    public function getParent()
    {
        return 'DapsLdapBundle';
    }
}
