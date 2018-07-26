<?php

namespace App\Http\Controllers;

use Doctrine\ORM\EntityManagerInterface;
use Photogabble\Tuppence\App;
use League\Plates\Engine;
use Zend\Diactoros\Response\HtmlResponse;

class Controller
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var App
     */
    protected $app;

    /**
     * @var Engine
     */
    protected $plates;

    /**
     * ContentType constructor.
     *
     * @param Engine $plates
     * @param EntityManagerInterface $entityManager
     * @param App $app
     */
    public function __construct(Engine $plates, EntityManagerInterface $entityManager, App $app)
    {
        $this->entityManager = $entityManager;
        $this->app = $app;
        $this->plates = $plates;
    }

    /**
     * @param string $template
     * @param array $values
     * @param int $status
     * @param array $headers
     *
     * @return HtmlResponse
     */
    protected function view($template, $values = [], $status = 200, $headers = [])
    {
        return new HtmlResponse($this->plates->render($template, $values), $status, $headers);
    }
}