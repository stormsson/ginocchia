<?php
namespace Hart\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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
        $sql = "SELECT count(*) as total FROM events ";
        $count = $app['db']->fetchAssoc($sql);

        $total = self::STARTING_COUNTER + $count['total'];
        return $total;
    }


    public function getAforisma()
    {
        $aforismi = array(
            "Guardare da vicino il proprio dolore è un modo di consolarsi. - Stendhal",
            "Se il vero dolore consistesse in uno schiaffo... - Carlos Ruiz Zafón",
            "E per tutti il dolore degli altri | è dolore a metà. - Fabrizio De André",
            "Là dove cresce il dolore è terra benedetta. - Oscar Wilde",
            "Il bello della musica è che quando ti colpisce non senti dolore. - Bob Marley",
            "Tutti gli uomini sanno dare consigli e conforto al dolore che non provano. - William Shakespeare"
        );

        $index = mt_rand(0, count($aforismi) -1);

        return $aforismi[$index];
    }

    public function index(Request $request, Application $app)
    {

        $count = $this->getCount($app);

        return $app['twig']->render('Default/index.html.twig', array(
            'name' => "pippo",
            'count' => $count
        ));
    }

    public function count(Request $request, Application $app)
    {
        $sql = "INSERT INTO events (id, user, created_at) VALUES (NULL, NULL, CURRENT_TIMESTAMP);";
        try {
            $result = $app['db']->executeUpdate($sql);
        } catch (\Exception $e) {
            $result = false;
        }

        $count = $this->getCount($app);

        if ($result) {
            return $app->json(array(
                'message'=> $this->getAforisma(),
                'count'=>$count
            ), 201);
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
