<?php
namespace NwSilex\Provider;

use Silex\Application;
use NwSilex\Mail\Mailer;
use Silex\Provider\SwiftmailerServiceProvider;

class MailerServiceProvider extends SwiftmailerServiceProvider
{
    public function register(Application $app)
    {
        parent::register($app);

        $configMail = isset($app['app.config']['mail']) ? $app['app.config']['mail'] : array();

        $app['swiftmailer.options'] = $configMail;
        
        $app['app.mailer'] = $app->share(function ($app) use($configMail) {
            return new Mailer($app['mailer'], $app['twig'], $configMail, $app['logger']);
        });
    }
}
