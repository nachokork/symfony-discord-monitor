<?php

namespace App\ErrorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppErrorBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}