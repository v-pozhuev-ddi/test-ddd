<?php

namespace App\Ddd\Application\Controller;

use App\Ddd\Application\FractalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


abstract class AbstractApiController extends AbstractController {

    /**
     * @var FractalService
     */
    protected $fractalService;

    public function __construct( FractalService $fractalService )
    {
        $this->fractalService = $fractalService;
    }
}