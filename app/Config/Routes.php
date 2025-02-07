<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->get('/hello', 'Hello::index');
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('/hello', 'Hello::index');
$routes->get('/login', 'Login::index');
$routes->post('/login/check_login', 'Login::check_login');
$routes->get('/login/logout', 'Login::logout');
$routes->get('register', 'Register::index');
$routes->post('register', 'Register::index');
// $routes->get('register/success', 'Register::success');

$routes->get('register/verify', 'Register::verify');
$routes->post('register/verify', 'Register::verify');
$routes->get('register/success', 'Register::success');

$routes->get('forgot_password', 'ForgotPassword::index');
$routes->post('forgot_password', 'ForgotPassword::index');
$routes->get('forgot_password/reset_password', 'ForgotPassword::reset_password');
$routes->post('forgot_password/reset_password', 'ForgotPassword::reset_password');

$routes->get('user_profile', 'ProfileController::index');
$routes->post('user_profile/upload', 'ProfileController::upload');
$routes->post('user_profile/updateUsername', 'ProfileController::updateUsername');
$routes->post('user_profile/updateBio', 'ProfileController::updateBio');

$routes->get('discussion_forum', 'Forum::index');
$routes->post('discussion_forum/create', 'Forum::create');

$routes->get('discussion_forum/create', 'Forum::create');
$routes->post('discussion_forum/save', 'Forum::save');
$routes->post('discussion_forum/autocomplete', 'Forum::autocomplete');
$routes->get('discussion_forum/loadPost', 'Forum::loadPost');
$routes->get('discussion_forum/post_content/(:num)', 'Forum::displayPost/$1');
$routes->post('discussion_forum/search', 'Forum::search');

$routes->post('CommentController/postComment', 'CommentController::postComment');
$routes->post('/CommentController/updateLikeCount', 'CommentController::updateLikeCount');



$routes->get('course_review', 'CourseReview::index');
$routes->post('course_review/autocomplete', 'CourseReview::autocomplete');

$routes->get('session_expired', 'SessionController::index');


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
