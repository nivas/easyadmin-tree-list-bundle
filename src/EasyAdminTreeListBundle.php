<?php


namespace Nivas\Bundle\EasyAdminTreeListBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EasyAdminTreeListBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }	
}