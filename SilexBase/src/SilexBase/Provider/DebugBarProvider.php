<?php
/**
 * User: dongww
 * Date: 14-4-4
 * Time: 下午1:53
 */

namespace SilexBase\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use DebugBar\StandardDebugBar;

/**
 * DebugBar Provider
 *
 * Class DebugBarProvider
 * @package SilexBase\Provider
 */
class DebugBarProvider implements ServiceProviderInterface
{
    protected $app;

    public function register(Application $app)
    {
        $this->app = $app;

        if (!isset($app['debug_bar'])) {
            $app['debug_bar'] = $app->share(function () {
                return new StandardDebugBar();
            });

            if ($app['config.main']['providers']['doctrine']) {
                $debugStack = new \Doctrine\DBAL\Logging\DebugStack();
                $app['db']->getConfiguration()->setSQLLogger($debugStack);
                $app['debug_bar']->addCollector(new \DebugBar\Bridge\DoctrineCollector($debugStack));
            }

            $app->get('/debugbar/{path}', function ($path) use ($app) {
                return $app->sendFile($app['debug_bar']->getJavascriptRenderer()->getBasePath() . '/' . $path);
            })->assert('path', '.+');
        }
    }

    /**
     * 输出debugBar，只有当页面有</body>标签时有效。
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        if (!$event->isMasterRequest()) {
            return;
        }
        if ($request->isXmlHttpRequest()) {
            return;
        }

        if ($response->isRedirection()
            || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $request->getRequestFormat()
        ) {
            return;
        }

        $basePath = $event->getRequest()->getBasePath();
        $render = $this->app['debug_bar']->getJavascriptRenderer($basePath . '/index_dev.php/debugbar');
        ob_start();
        echo $render->renderHead();
        echo $render->render();
        $debugContent = ob_get_contents();
        ob_end_clean();

        $content = $response->getContent();
        $content = str_replace("</body>", $debugContent . '</body>', $content);
        $event->getResponse()->setContent($content);
    }

    public function boot(Application $app)
    {
        $app['dispatcher']->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'), -1000);
    }
}
 