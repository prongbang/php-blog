<?php
// Routes

// Front ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$app->get('/', 'DefaultController:index');


// Back ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$app->group('/manage', function () use ($app) {

    // Home -----------------------------------------------------------------------
    $app->get (''                               , 'ManageController:index'       );

    // Authen ---------------------------------------------------------------------
    $app->get ('/login'                         , 'AuthController:getLogin'      );
    $app->post('/login'                         , 'AuthController:postLogin'     );
    $app->post('/logout'                        , 'AuthController:postLogout'    );

    // Posts ----------------------------------------------------------------------
    $app->get ('/posts'                         , 'PostController:index'         );
    $app->get ('/posts/create'                  , 'PostController:create'        );
    $app->get ('/posts/{id:[0-9]+}'             , 'PostController:viewById'      );
    $app->get ('/posts/{id:[0-9]+}/edit'        , 'PostController:editById'      );
    $app->post('/posts/{id:[0-9]+}'             , 'PostController:_mothod'       );
    $app->post('/posts'                         , 'PostController:postCreate'    );
    $app->post('/posts/upload'                  , 'PostController:upload'        );

    // Categories -----------------------------------------------------------------
    $app->get ('/categories'                    , 'CategoriesController:index'   );
    $app->get ('/categories/{id:[0-9]+}'        , 'CategoriesController:viewById');
    $app->get ('/categories/{id:[0-9]+}/edit'   , 'CategoriesController:editById');
    $app->get ('/categories/create'             , 'CategoriesController:create'  );
    $app->post('/categories/{id:[0-9]+}'        , 'CategoriesController:_mothod' );
    $app->post('/categories'                    , 'CategoriesController:_create' );
    
    // Pages -----------------------------------------------------------------------
    $app->get ('/pages'                         , 'PagesController:index'        );
    $app->get ('/pages/create'                  , 'PagesController:create'       );
    $app->get ('/pages/{id:[0-9]+}'             , 'PagesController:viewById'     );
    $app->get ('/pages/{id:[0-9]+}/edit'        , 'PagesController:editById'     );
    $app->post('/pages/{id:[0-9]+}'             , 'PagesController:edit'         );
    $app->post('/pages'                         , 'PagesController:_mothod'      );
    $app->post('/pages/upload'                  , 'PagesController:upload'       );

    // Profile ---------------------------------------------------------------------
    $app->get ('/profile'                       , 'ProfileController:index'      );

});
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~