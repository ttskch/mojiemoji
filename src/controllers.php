<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->match('/', function (Request $request) use ($app) {
    /** @var \Symfony\Component\Form\Form $form */
    $form = require __DIR__.'/forms/form.php';

    $form->handleRequest($request);
    if ($form->isValid()) {
        $moji = $request->request->get('form')['moji'];
        switch (mb_strlen($moji)) {
            case 1:
                $size = 128 * 72/96;
                $x = 2 * 72/96;
                $y = 150 * 72/96;
                break;
            case 2:
                $size = 60 * 72/96;
                $x = 6 * 72/96;
                $y = 114 * 72/96;
                break;
            default:
                $size = 60 * 72/96;
                $x = 4 * 72/96;
                $y = 74 * 72/96;
                $moji = mb_substr($moji, 0, 2)."\n".mb_substr($moji, 2, 4);
                break;
        }

        $image = imagecreatefrompng(__DIR__.'/../files/template.png');
        $fgcolor = imagecolorallocate($image, 0, 0, 0);
        imagefttext($image, $size, 0, $x, $y, $fgcolor, __DIR__.'/../files/SourceHanCodeJP-Regular.otf', $moji, ['linespacing' => 0.7]);

        ob_start();
        imagepng($image);
        $base64 = base64_encode(ob_get_contents());
        ob_end_clean();
        imagedestroy($image);

        $app['session']->set('base64', $base64);

        return $app->redirect($app['url_generator']->generate('homepage'));
    }

    $base64 = $app['session']->get('base64') ?: base64_encode(file_get_contents(__DIR__.'/../files/naruhodo.png'));
    $app['session']->set('base64', null);

    return $app['twig']->render('index.html.twig', [
        'form' => $form->createView(),
        'base64' => $base64,
    ]);
})
->bind('homepage')
;

$app->get('/download/{base64}', function (Request $request, $base64) use ($app) {
    $name = 'mojiemoji_'.date('YmdHis').'.png';
    $file = sys_get_temp_dir().'/mojiemoji_'.preg_replace('/[ .]/', '', microtime()).'.png';
    file_put_contents($file, base64_decode($base64));

    return $app
        ->sendFile($file)
        ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name)
    ;
})
->bind('download')
->assert('base64', '.*')
;

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
