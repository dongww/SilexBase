<?php
/**
 * User: dongww
 * Date: 14-3-20
 * Time: 上午10:11
 */

namespace Controller;

use SilexBase\Core\Controller;
use App\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function indexAction(Application $app)
    {
        return $app['twig']->render('Admin/index.twig');
    }
}
 