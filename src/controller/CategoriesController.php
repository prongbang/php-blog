<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Monolog\Logger;
use Slim\Csrf\Guard;
use App\Controller\Controller;
use App\Model\Authorization;
use App\Utils\IPUtil;
use App\Model\Category;

class CategoriesController extends Controller {
    
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
        $this->logger->info("GET '/manage/categories' route");
        if (Authorization::checkSession()) {
            $dataArray = array(
                'username' => Authorization::getSession('username'),
                'hostname' => IPUtil::getHostName(),
                'categories' => Category::all()
            );
            
            return $this->view->render($response, 'categories/index.html', $dataArray);
        } else {
            return $response->withRedirect('login');
        }
    }

    public function viewById(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $id = $args['id'];
        $this->logger->info("GET '/manage/categories/$id' route");
        if (Authorization::checkSession()) {
            if(is_numeric($id)) {
                $dataArray = array(
                    'username' => Authorization::getSession('username'),
                    'hostname' => IPUtil::getHostName(),
                    'category' => Category::find($id),
                    'categories' => Category::all()
                );
                return $this->view->render($response, 'categories/view.html', $dataArray);                
            }
            return $response->withRedirect('../categories');
        } else {
            return $response->withRedirect('../login');
        }
    }

    public function editById(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $id = $args['id'];
        $this->logger->info("GET '/manage/categories/$id/edit' route");
        if (Authorization::checkSession()) {
            if(is_numeric($id)) {
                $category = Category::find($id);
                if(!empty($category)) {
                    $dataArray = array(
                        'username' => Authorization::getSession('username'),
                        'hostname' => IPUtil::getHostName(),
                        'category' => $category,
                        'categories' => Category::all()
                    );
                    return $this->view->render($response, 'categories/edit.html', $dataArray);
                }
            }
            return $response->withRedirect('../../categories');
        }
        return $response->withRedirect('../../login');
    }

    public function create(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $this->logger->info("GET '/manage/categories/create' route");
        if (Authorization::checkSession()) {
            $dataArray = array(
                'username' => Authorization::getSession('username'),
                'hostname' => IPUtil::getHostName(),
                'categories' => Category::all()
            );
            return $this->view->render($response, 'categories/create.html', $dataArray);
        } else {
            return $response->withRedirect('../login');
        }
    }
    
    public function _create(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $this->logger->info("POST '/manage/categories' route");
        if (Authorization::checkSession()) {
            if (false === $request->getAttribute('csrf_status')) {
                // display suitable error here 
                return $response->withRedirect('../login');
            } else {
                // successfully passed CSRF check 
                return $this->add($_POST, $response);
            }
        }
        return $response->withRedirect('../login');
    }

    public function _mothod(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $id = $args['id'];
        $method = $_POST['_method'];
        $this->logger->info("$method '/manage/categories/$id' route");
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

                    return $this->add($_POST, $response);   

                } else if ($method == "PUT") {

                    return $this->update($_POST, $response, $args, "../");      

                } else if ($method == "DELETE") {

                    return $this->delete($_POST, $response, $args);   

                }
                return $response->withRedirect('../categories');
            }
        }
        return $response->withRedirect('manage/login');
    }
    
    private function delete($POST, $response, $args) {
        $id = $args['id'];
        if(is_numeric($id)) {
            $category = Category::find($id);
            if(!empty($category)) {
                try {
                    $category->delete();
                } catch (Exception $e) {}
            }
        }
        return $response->withRedirect('../categories');
    }

    private function update($POST, $response, $args, $redirect) {
        $id = $args['id'];
        if(is_numeric($id)) {
            $category = Category::find($id);
            if(!empty($category)) {

                $category->parent_id    = $POST['parent_id'] == "" ? NULL : intval($POST['parent_id']);
                $category->order        = $POST['order'] == "" ? 1 : intval($POST['order']);
                $category->name         = $POST['name'];
                $category->slug         = $POST['slug']; 
                $category->updated_at   = time(); 

                try {
                    $category->save();
                } catch (Exception $e) {}

                return $response->withRedirect($redirect.'categories/'.$category->id.'/edit');
            }
        }
        return $response->withRedirect('../categories');
    }

    private function add($POST, $response) {
        $dataArray = array(
            'username' => Authorization::getSession('username'),
            'hostname' => IPUtil::getHostName(),
            'category' => $POST,
            'categories' => Category::all()
        );
        if(!empty($POST)) {
            $category = new Category();
            $category->parent_id    = $POST['parent_id'] == "" ? NULL : intval($POST['parent_id']);
            $category->order        = $POST['order'] == "" ? 1 : intval($POST['order']);
            $category->name         = $POST['name'];
            $category->slug         = $POST['slug'];
            $category->created_at   = time(); 

            try {
                if($category->save() >= 1) {
                    return $response->withRedirect('categories/'.$category->id.'/edit');
                }
            } catch(Exception $e) { }
        }
        return $this->view->render($response, 'categories/create.html', $dataArray);   
    }

}