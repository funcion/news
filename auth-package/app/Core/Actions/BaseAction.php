<?php

namespace App\Core\Actions;

abstract class BaseAction
{
    /**
     * Estandariza la ejecución de la lógica de negocio.
     */
    abstract public function execute(mixed ...$args): mixed;
}
