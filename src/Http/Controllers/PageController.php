<?php

namespace App\Http\Controllers;

use App\Entities\Video;
use App\Http\VideoSignature;
use App\Repositories\VideoRepository;
use Doctrine\ORM\NoResultException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Response\SapiStreamEmitter;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\Stream;

class PageController extends Controller
{
    /**
     * GET: /
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return \Zend\Diactoros\Response\HtmlResponse
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        $q = $request->getQueryParams();
        $code = isset($q['code']) ? (string)$q['code'] : '';
        $error = isset($q['error']) ? base64_decode((string)$q['error']) : false;

        return $this->view('home', [
            'code' => $code,
            'error' => $error
        ], $error === false ? 422 : 200);
    }

    /**
     * POST: /watch
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return \Zend\Diactoros\Response\HtmlResponse|RedirectResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function watch(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        $q = $request->getParsedBody();
        $code = isset($q['code']) ? (string)$q['code'] : '';

        /** @var VideoRepository $repository */
        $repository = $this->entityManager->getRepository(Video::class);

        try {
            $video = $repository->getVideoByCode($code);
            $video->incrementWatches();
            $video->setModified(time());
            $this->entityManager->persist($video);
            $this->entityManager->flush();

            $signature = new VideoSignature(1500, 'URAimWPrXpMlKicv8yRkIzfa8TZvh6Kn'); // @todo set key from config.

            return $this->view('player', [
                'title' => 'Video Player',
                'signature' => $signature->getSignedPayloadUri(['id' => $video->getId(), 'video' => $video->getVideoFile()])
            ]);

        } catch (NoResultException $e) {
            return new RedirectResponse('/?error=' . base64_encode('The code entered is invalid'));
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface|TextResponse|Stream
     */
    public function stream(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        try{
            $signature = new VideoSignature(1500, 'URAimWPrXpMlKicv8yRkIzfa8TZvh6Kn'); // @todo set key from config.
            $payload = $signature->getPayloadFromSignedUri($args['signature']);
        } catch (\Exception $e) {
            return new TextResponse('This URL has expired.', 403);
        }

        $this->app->getContainer()->share('emitter', new SapiStreamEmitter());

        $filePathName = APP_ROOT . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR . $payload['video'];

        if (!file_exists($filePathName)) {
            return new TextResponse('File not found.', 403);
        }

        $response = $response
            ->withHeader('Content-Type', (new \finfo(FILEINFO_MIME))->file($filePathName))
            ->withHeader('Content-Length', (string) filesize($filePathName))
            ->withBody(new Stream($filePathName));

        return $response;
    }
}