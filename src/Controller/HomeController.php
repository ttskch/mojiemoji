<?php

namespace App\Controller;

use App\Form\GenerateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/", name="home_")
 */
class HomeController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Template()
     */
    public function index(Request $request, SessionInterface $session)
    {
        $form = $this->createForm(GenerateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->get('generate');

            $font = [
                'file' => __DIR__ . '/../Resources/files/' . ($data['font'] === '明朝' ? 'ipaexm.ttf' : 'ipaexg.ttf'),
                'linespacing' => 0.9,
                'size' => [120, 62, 62],
                'x' => [6, 4, 4],
                'y' => [146, 116, 78],
            ];

            switch (mb_strlen($moji = $data['moji'])) {
                case 1:
                    $size = $font['size'][0] * 72 / 96;
                    $x = $font['x'][0] * 72 / 96;
                    $y = $font['y'][0] * 72 / 96;
                    break;
                case 2:
                    $size = $font['size'][1] * 72 / 96;
                    $x = $font['x'][1] * 72 / 96;
                    $y = $font['y'][1] * 72 / 96;
                    break;
                default:
                    $size = $font['size'][2] * 72 / 96;
                    $x = $font['x'][2] * 72 / 96;
                    $y = $font['y'][2] * 72 / 96;
                    $moji = mb_substr($moji, 0, 2) . "\n" . mb_substr($moji, 2, 4);
                    break;
            }

            $image = imagecreatefrompng(__DIR__ . '/../Resources/files/template.png');
            $fgcolor = imagecolorallocate($image, 0, 0, 0);
            imagefttext($image, $size, 0, $x, $y, $fgcolor, $font['file'], $moji, ['linespacing' => $font['linespacing']]);

            ob_start();
            imagepng($image);
            $base64 = base64_encode(ob_get_contents());
            ob_end_clean();
            imagedestroy($image);

            $session->set('base64', $base64);

            return $this->redirectToRoute('home_index');
        }

        $base64 = $session->get('base64') ?: base64_encode(file_get_contents(__DIR__.'/../../public/statics/naruhodo.png'));
        $session->remove('base64');

        return [
            'form' => $form->createView(),
            'base64' => $base64,
        ];
    }

    /**
     * @Route("/download/{base64}", name="download", requirements={"base64" = ".+"})
     */
    public function form(string $base64, Request $request, TranslatorInterface $translator)
    {
        $name = 'mojiemoji_' . date('YmdHis') . '.png';
        $file = sys_get_temp_dir() . '/mojiemoji_' . preg_replace('/[ .]/', '', microtime()) . '.png';
        file_put_contents($file, base64_decode($base64));

        return (new BinaryFileResponse($file))
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name)
        ;
    }
}
