<?php
namespace App\Http\Controllers;

use App\Entities\Video;
use App\Repositories\VideoRepository;
use Doctrine\ORM\NoResultException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PageController extends Controller
{
    /**
     * GET: /
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return \Zend\Diactoros\Response\HtmlResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        $q = $request->getQueryParams();
        $code = isset($q['code']) ? (string) $q['code'] : '';
        $error = false; // "The code entered is invalid";

        /** @var VideoRepository $repository */
        $repository = $this->entityManager->getRepository(Video::class);

        if (strlen($code) > 0) {
            try{
                $video = $repository->getVideoByCode($code);
                var_dump($video); die();
            } catch (NoResultException $e) {
                $error = 'The code entered is invalid';
            }

        }

        return $this->view('home', [
            'title' => 'Video Player',
            'code' => $code,
            'error' => $error
        ]);
    }
}