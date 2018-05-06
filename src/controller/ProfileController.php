<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Monolog\Logger;
use Slim\Csrf\Guard;
use App\Controller\Controller;
use App\Model\Authorization;
use App\Utils\IPUtil;

class ProfileController extends Controller {
    
    private $csrf;
    private $logger;
    private $view;
    private $db;

    public function __construct(Guard $csrf, Logger $logger, $view, $db) {
        $this->csrf     = $csrf;
        $this->logger   = $logger;
        $this->view     = $view;
        $this->db       = $db;
    }

    public function index(RequestInterface $request, ResponseInterface $response, $args){
        // Log message
        $this->logger->info("GET '/manage/profile' route");
        if (Authorization::checkSession()) {
            $dataArray = array(
                'username' => Authorization::getSession('username'),
                'hostname' => IPUtil::getHostName()
            );
            return $this->view->render($response, 'profile/index.html', $dataArray);
        } else {
            return $response->withRedirect('manage/login');
        }
    }
}