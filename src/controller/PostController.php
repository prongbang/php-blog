<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface;
// use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Monolog\Logger;
use Slim\Csrf\Guard;
use App\Controller\Controller;
use App\Model\Authorization;
use App\Utils\DateUtil;
use App\Utils\FileUtil;
use App\Utils\IPUtil;
use App\Model\Post;
use App\Model\Category;


class PostController extends Controller {
    
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
        $this->logger->info("GET '/manage/posts' route");
        if (Authorization::checkSession()) {
            $dataArray = array(
                'posts' => Post::all(),
                'username' => Authorization::getSession('username'),
                'hostname' => IPUtil::getHostName(),
            );

            // $posts = $this->db->table('posts');
            // $dataArray['posts'] = $posts->get(); 

            return $this->view->render($response, 'posts/index.html', $dataArray);
        } else {
            return $response->withRedirect('login');
        }
    }

    
    public function _mothod(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $id = $args['id'];
        $method = $_POST['_method'];
        $this->logger->info("$method '/manage/posts/$id' route");
        if (Authorization::checkSession()) {
            if (false === $request->getAttribute('csrf_status')) {
                // display suitable error here 
                return $response->withRedirect('../login');
            } else {
                // successfully passed CSRF check 
                $dataArray = array(
                    'username' => Authorization::getSession('username'),
                    'hostname' => IPUtil::getHostName()
                );

                if ($method == "POST") {
                    
                    return $this->add($_POST, $_FILES, $response);

                } else if ($method == "PUT") {

                    return $this->update($_POST, $_FILES, $response, $args, "../");
                        
                } else if ($method == "DELETE") {

                    return $this->delete($_POST, $_FILES, $response, $args);

                }
            } 
        } else {
            return $response->withRedirect('../login');
        }
    }
    
    public function viewById(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $id = $args['id'];
        $this->logger->info("GET '/manage/posts/$id' route");
        if (Authorization::checkSession()) {
            $dataArray = array(
                'username' => Authorization::getSession('username'),
                'hostname' => IPUtil::getHostName()
            );

            if(is_numeric($id)) {
                $record = Post::find($id);
                $dataArray['post'] = $record;
                return $this->view->render($response, 'posts/view.html', $dataArray);   
            }
            return $response->withRedirect('../posts');
        } else {
            return $response->withRedirect('../login');
        }
    }

    public function editById(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $id = $args['id'];
        $this->logger->info("GET '/manage/posts/$id/edit' route");
        if (Authorization::checkSession()) {
            if(is_numeric($id)) {
                $dataArray = array(
                    'username' => Authorization::getSession('username'),
                    'hostname' => IPUtil::getHostName(),
                    'categories' => Category::all(),
                    'post' => Post::find($id)
                );
                return $this->view->render($response, 'posts/edit.html', $dataArray);
            }
            return $response->withRedirect('../../posts');
        } else {
            return $response->withRedirect('../../login');
        }
    }

    public function create(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $this->logger->info("GET '/manage/posts/create' route");
        if (Authorization::checkSession()) {
            $dataArray = array(
                'username' => Authorization::getSession('username'),
                'hostname' => IPUtil::getHostName()
            );
            $dataArray['categories'] = Category::all();
            return $this->view->render($response, 'posts/create.html', $dataArray);
        } else {
            return $response->withRedirect('../login');
        }
    }

    public function postCreate(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $this->logger->info("POST '/manage/posts' route");
        if (Authorization::checkSession()) {
            if (false === $request->getAttribute('csrf_status')) {
                // display suitable error here 
                return $response->withRedirect('manage/login');
            } else {
                // successfully passed CSRF check 
                return $this->add($_POST, $_FILES, $response);
            }
        } else {
            return $response->withRedirect('manage/login');
        }
    }

    public function upload(RequestInterface $request, ResponseInterface $response, $args) {
        // Log message
        $this->logger->info("POST '/manage/posts/upload' route");
        if (Authorization::checkSession()) {
            $slug = $_POST['type_slug'];
            $file = $_FILES['image']; 

            // echo out script that TinyMCE can handle and update the image in the editor
            return FileUtil::upload_image($file, $slug);
        } else {
            return $response->withRedirect('manage/login');
        }
    }

    private function delete($POST, $FILES, $response, $args) {
        $id = $args['id'];
        if(is_numeric($id)) {
            $post = Post::find($id);
            if(!empty($post)) {
                if($post->image != "" && $post->image != NULL) {
                    if(FileUtil::delete(__DIR__. "/../../public/", $post->image));
                    // TODO 
                    // - delete image in body
                }
                try {
                    $post->delete();
                } catch(Exception $e){}
            }
        }
        return $response->withRedirect('../posts');
    }

    private function update($POST, $FILES, $response, $args, $redirect) {
        $id = $args['id'];
        if(is_numeric($id)) {
            $post = Post::find($id);
            if(!empty($post)) {
                $file = $FILES['image'];
                if (!empty($file['name'])) {
                    if($post->image != "" && $post->image != NULL) {
                        if(FileUtil::delete(__DIR__. "/../../public/", $post->image));
                    }
                    $target_dir = 'storage/posts/'.DateUtil::dateYyyyMmDd().'/';
                    // move uploaded file from temp to uploads directory
                    $result = FileUtil::upload($file, __DIR__, "/../../public/", $target_dir);
                    if($result['status'] == "Success") {
                        $post->image = $result['path'];
                    }
                }

                $post->slug             = $POST['slug'];
                $post->author_id        = intval(Authorization::getSession("user_id"));
                $post->category_id      = intval($POST['category_id']);
                $post->title            = $POST['title'];
                $post->seo_title        = $POST['seo_title'];
                $post->excerpt          = $POST['excerpt'];
                $post->body             = $POST['body'];
                $post->meta_description = $POST['meta_description'];
                $post->meta_keywords    = $POST['meta_keywords'];
                $post->status           = $POST['status'];
                $post->featured         = !empty($POST['featured']) ? intval($POST['featured']) : 0; 
                $post->updated_at       = time();

                try {
                    $post->save();
                } catch(Exception $e){}
                return $response->withRedirect($redirect.'posts/'.$post->id.'/edit');
            }
        }
        return $response->withRedirect('../posts');
    }

    private function add($POST, $FILES, $response) {
        $file = $FILES['image'];
        $data = [
            'author_id'         => intval(Authorization::getSession("user_id")),
            'category_id'       => intval($POST['category_id']),
            'title'             => $POST['title'],
            'seo_title'         => $POST['seo_title'],
            'excerpt'           => $POST['excerpt'],
            'body'              => $POST['body'],
            'image'             => "",
            'slug'              => $POST['slug'],
            'meta_description'  => $POST['meta_description'],
            'meta_keywords'     => $POST['meta_keywords'],
            'status'            => $POST['status'],
            'featured'          => !empty($POST['featured']) ? intval($POST['featured']) : 0,
        ];

        if(!empty($file)) {
            $target_dir = 'storage/posts/'.DateUtil::dateYyyyMmDd().'/';
            // move uploaded file from temp to uploads directory
            $result = FileUtil::upload($file, __DIR__, "/../../public/", $target_dir);
            if($result['status'] == "Success") {
                $data['image'] = $result['path'];
            }
        }

        if(!empty($data['title'])) {
            // Or create a new post
            $post = new Post;
            $post->slug             = $data['slug'];
            $post->author_id        = $data['author_id'];
            $post->category_id      = $data['category_id'];
            $post->title            = $data['title'];
            $post->seo_title        = $data['seo_title'];
            $post->excerpt          = $data['excerpt'];
            $post->body             = $data['body'];
            $post->image            = $data['image'];
            $post->meta_description = $data['meta_description'];
            $post->meta_keywords    = $data['meta_keywords'];
            $post->status           = $data['status'];
            $post->featured         = $data['featured']; 
            $post->created_at       = time();

            try {
                if($post->save() >= 1) {
                    return $response->withRedirect('posts/'.$post->id.'/edit');
                }
            } catch(Exception $e){}
        }
        return $this->view->render($response, 'posts/create.html', $dataArray);
    }

}