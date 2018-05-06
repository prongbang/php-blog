<?php

namespace App\Controller;

// use Slim\Http\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Monolog\Logger;
use Slim\Csrf\Guard;
use App\Controller\Controller;
use App\Model\Authorization;
use App\Model\User;

class AuthController extends Controller {
    
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

    public function getLogin(RequestInterface $request, ResponseInterface $response, $args){
        // Log message
        $this->logger->info("GET '/manage/login' route");
        if (Authorization::checkSession()) {
            return $response->withRedirect('../manage');
        } else {
            $dataArray = array(
                
            );
            return $this->view->render($response, 'login.html', $dataArray);
        }
    }

    
    public function postLogout(RequestInterface $request, ResponseInterface $response, $args){
        // Log message
        $this->logger->info("POST '/manage/logout' route");
        if (Authorization::checkSession()) {
            Authorization::destroySession();
            return $response->withRedirect('login');
        } else {
            return $response->withRedirect('login');
        }
    }

    public function postLogin(RequestInterface $request, ResponseInterface $response, $args){
        // Log message
        $this->logger->info("POST '/manage/login' route");
        if (false === $request->getAttribute('csrf_status')) {
            // display suitable error here 
            return $response->withRedirect('login');
        } else {
            // successfully passed CSRF check 
            $result = FALSE;
            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                $email = $_POST['email'];
                $password = $_POST['password'];

                // $query->where([['column_1', '=', 'value_1'],])
                $record = User::where([['email', '=', $email]])->get()->all();

                if(count($record) > 0) {
                    //$hash = password_hash($password, PASSWORD_BCRYPT);
                    $hash = $record[0]['password'];
                    if(password_verify($password, $hash) && $email == $record[0]['email']) {
                        //$records = $this->db->table->where('name', 'like', '%foo%')->get();
                        Authorization::pushSession("name", $record[0]['name']);
                        Authorization::pushSession("user_id", $record[0]['id']);
                        Authorization::pushSession("username", $email);
                        Authorization::pushSession("email", $email);
                        $result = TRUE;
                    }
                }
            }
            return $response->withRedirect($result ? '/manage':'login');
        }
    }
}