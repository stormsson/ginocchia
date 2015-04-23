<?php
namespace Hart\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Hart\Utils\Utils;
use Hart\Manager\UserManager;
use Hart\Query\EventsQuery;
/**
 * per info su come fare query:
 * http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/data-retrieval-and-manipulation.html
 *
 * per info su come usare twig
 * http://silex.sensiolabs.org/doc/providers/twig.html#usage
 */

class DefaultController
{
    const STARTING_COUNTER = 10;

    protected function getCount($app)
    {
        $count = EventsQuery::countEvents($app);

        $total = self::STARTING_COUNTER + $count['total'];

        return $total;
    }

    protected function getUserCount($user_id, $app)
    {
        $count = EventsQuery::countEventsByUser($user_id, $app);
        return $count['total'];
    }

    public function getAforisma()
    {
        $aforismi = array(
            "Guardare da vicino il proprio dolore è un modo di consolarsi. - Stendhal",
            "Se il vero dolore consistesse in uno schiaffo... - Carlos Ruiz Zafón",
            "E per tutti il dolore degli altri | è dolore a metà. - Fabrizio De André",
            "Là dove cresce il dolore è terra benedetta. - Oscar Wilde",
            "Il bello della musica è che quando ti colpisce non senti dolore. - Bob Marley",
            "Tutti gli uomini sanno dare consigli e conforto al dolore che non provano. - William Shakespeare",
            "Un'ora breve di dolore c'impressiona lungamente; un giorno sereno passa e non lascia traccia. - Luigi Pirandello",
            "Mai fidarsi delle citazioni prese da internet - Abraham Lincoln",
        );

        $index = mt_rand(0, count($aforismi) -1);

        return $aforismi[$index];
    }

    public function index(Request $request, Application $app)
    {
        //count generico
        $args = array(
            'count' => $this->getCount($app)
        );

        //count utente
        $current_user = UserManager::getCurrentUser($app);
        if($current_user) {
            $args['user_count'] = $this->getUserCount($current_user['id'], $app);
            $args['first_name'] = $current_user['first_name'];
        }

        return $app['twig']->render('Default/index.html.twig', $args);
    }

    public function count(Request $request, Application $app)
    {
        if($user_logged = UserManager::getSessionToken($app)) {
            $user_id = $user_logged['user_id'];
        } else {
            $user_id = 'NULL';
        }

        $sql = "INSERT INTO events (id, user, created_at) VALUES (NULL, $user_id, CURRENT_TIMESTAMP);";

        try {
            $result = $app['db']->executeUpdate($sql);
        } catch (\Exception $e) {
            $result = false;
        }

        $count = $this->getCount($app);

        //count generico
        $args = array(
            'count' => $this->getCount($app),
            'message' => $this->getAforisma(),
        );

        //count utente
        $current_user = UserManager::getCurrentUser($app);
        if($current_user) {
            $args['user_count'] = $this->getUserCount($current_user['id'], $app);
            $args['first_name'] = $current_user['first_name'];
        }

        if ($result) {
            return $app->json($args, 201);
        } else {
            return $app->json(array(
                'message'=>'bad request'
            ), 400);
        }
    }

    public function history(Request $request, Application $app)
    {
        return $app['twig']->render('Default/history.html.twig', array(

        ));
    }
}
