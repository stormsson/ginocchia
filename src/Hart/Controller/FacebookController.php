<?php
namespace Hart\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Hart\Utils\Utils;

/**
 * per info su come fare query:
 * http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/data-retrieval-and-manipulation.html
 *
 * per info su come usare twig
 * http://silex.sensiolabs.org/doc/providers/twig.html#usage
 */

class FacebookController
{
    public function fbLoginTemplate(Request $request, Application $app)
    {
        return $app['twig']->render('Default/fb-login-template.html.twig', array());
    }

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
                        $result = $app['db']->executeUpdate($sql);
                    } else {
                        $result = true;
                    }
                } catch (\Exception $e) {
                    $result = false;
                }
            } else {
                $result = false;
            }
            return $result;
        }
    }

    public function fbLogin(Request $request, Application $app)
    {
        $result = $this->processFbLogin($request, $app);

        if($result) {
            $this->setCookie($result->first_name.'*'.$result->id);
        }
    }

    protected function setCookie($cookie_value)
    {
        setcookie('ginocchia_cookie', $cookie_value, time()+3600, '/');  /* expire in 1 hour */
    }
}