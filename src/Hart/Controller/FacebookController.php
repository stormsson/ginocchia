<?php
namespace Hart\Controller;

use MyProject\Proxies\__CG__\OtherProject\Proxies\__CG__\stdClass;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Hart\Utils\Utils;
use Hart\Manager\UserManager;

class FacebookController
{
    /**
     * Verify if the user is logged also in the server app
     * @param Request $request
     * @param Application $app
     */
    protected function processFbLogin(Request $request, Application $app)
    {
        if($request->getMethod() == 'POST') {
            $result_json = Utils::curlGet('https://graph.facebook.com/me?access_token='.$request->get('facebook_access_token'));
            $fb_user = json_decode($result_json);

            if(!isset($fb_user->error)) {
                try {
                    $sql = "SELECT * FROM users WHERE facebook_id = ?";
                    $app_user = $app['db']->fetchAssoc($sql, array($fb_user->id));

                    //creo l'utente in db se non lo trovo
                    if(!$app_user) {
                        $sql = "INSERT INTO users (first_name, facebook_id, created_at) VALUES ('$fb_user->first_name', '$fb_user->id', CURRENT_TIMESTAMP);";
                        if($app['db']->executeUpdate($sql)) {
                            $app_user = array();
                            $app_user['id'] = $fb_user->id;
                            $app_user['first_name'] = $fb_user->first_name;
                            $app_user['facebook_id'] = $fb_user->facebook_id;
                        }
                    }
                } catch (\Exception $e) {
                    $app_user = false;
                }
            } else {
                $app_user = false;
            }

            return $app_user;
        }
    }

    public function fbLogin(Request $request, Application $app)
    {
        $user = $this->processFbLogin($request, $app);

        if ($user) {

            $result = UserManager::setSessionToken($app, $user);

            return $app->json(array(
                'message'=> 'ok',
            ), 201);
        } else {

            return $app->json(array(
                'message'=>'bad request'
            ), 400);
        }
    }

    public function fbLogout(Request $request, Application $app)
    {
        $result = UserManager::deleteSessionToken($app);

        return $app->redirect('/');
    }
}