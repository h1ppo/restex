<?php

namespace H1ppo\Controller\Exception;

use Symfony\Component\HttpFoundation\Response;

class NotImplemented extends \Exception
{
    public function __construct($message, $code = Response::HTTP_NOT_IMPLEMENTED, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
