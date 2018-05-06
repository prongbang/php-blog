<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Monolog\Logger;
use Slim\Csrf\Guard;
use App\Controller\Controller;
use App\Model\Authorization;
use App\Utils\DateUtil;
use App\Utils\FileUtil;
use App\Utils\IPUtil;
use App\Model\Pages;
use App\Model\Category;


class PagesController extends Controller {
    
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

    
    public function index(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $this->logger->info("GET '/manage/pages' route");
        if (Authorization::checkSession()) {
            $dataArray = array(
                'username' => Authorization::getSession('username'),
                'hostname' => IPUtil::getHostName(),
                'pages' => Pages::all()
            );
            return $this->view->render($response, 'pages/index.html', $dataArray);
        } else {
            return $response->withRedirect('login');
        }
    }

    
    public function edit(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $id = $args['id'];
        $this->logger->info("PUT '/manage/pages/$id' route");
        if (Authorization::checkSession()) {
            $dataArray = array(
                'username' => Authorization::getSession('username'),
                'hostname' => IPUtil::getHostName()
            );
            if(is_numeric($id)) {
                $record = Pages::find($id);
                $dataArray['page'] = $record;
                return $this->view->render($response, 'pages/edit.html', $dataArray);
            }
            return $response->withStatus(404);
        } else {
            return $response->withRedirect('../login');
        }
    }
    
    public function viewById(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $id = $args['id'];
        $this->logger->info("GET '/manage/pages/$id' route");
        if (Authorization::checkSession()) {
            $dataArray = array(
                'username' => Authorization::getSession('username'),
                'hostname' => IPUtil::getHostName()
            );

            if(is_numeric($id)) {
                $record = Pages::find($id);
                $dataArray['page'] = $record;
                return $this->view->render($response, 'pages/view.html', $dataArray);   
            }
            return $response->withStatus(404);
        } else {
            return $response->withRedirect('../login');
        }
    }

    public function editById(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $id = $args['id'];
        $this->logger->info("GET '/manage/pages/$id/edit' route");
        if (Authorization::checkSession()) {
            $dataArray = array(
                'username' => Authorization::getSession('username'),
                'hostname' => IPUtil::getHostName()
            );

            var_dump($id);
            $dataArray['categories'] = Category::all();
            return $this->view->render($response, 'pages/edit.html', $dataArray);
        } else {
            return $response->withRedirect('../../login');
        }
    }

    public function create(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $this->logger->info("GET '/manage/pages/create' route");
        if (Authorization::checkSession()) {
            $dataArray = array(
                'username' => Authorization::getSession('username'),
                'hostname' => IPUtil::getHostName()
            );
            $dataArray['categories'] = Category::all();
            return $this->view->render($response, 'pages/create.html', $dataArray);
        } else {
            return $response->withRedirect('../login');
        }
    }

    public function _mothod(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $method = $args['_method'];
        $this->logger->info("$method '/manage/pages' route");
        if (Authorization::checkSession()) {
            if (false === $request->getAttribute('csrf_status')) {
                // display suitable error here 
                return $response->withRedirect('../login');
            } else {
                $dataArray = array(
                    'username' => Authorization::getSession('username'),
                    'hostname' => IPUtil::getHostName()
                );
                if ($method == "POST") {
                    // TODO

                    return $this->view->render($response, 'pages/edit.html', $dataArray);
                } else if ($method == "PUT") {

                    var_dump($id);

                    return $this->view->render($response, 'pages/edit.html', $dataArray);   
                        
                } else if ($method == "DELETE") {
                    // TODO 

                    return $this->view->render($response, 'pages/edit.html', $dataArray);
                }
            }
        } else {
            return $response->withRedirect('manage/login');
        }
    }

    public function upload(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $this->logger->info("POST '/manage/pages/upload' route");
        if (Authorization::checkSession()) {
            $slug = $_POST['type_slug'];
            $file = $_FILES['image'];

            // echo out script that TinyMCE can handle and update the image in the editor
            return FileUtil::upload_image($file);
        } else {
            return $response->withRedirect('../login');
        }
    }

}