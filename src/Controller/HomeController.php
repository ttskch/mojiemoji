<?php

namespace App\Controller;

use App\Entity\GenerateCriteria;
use App\Form\GenerateType;
use App\Service\EmojiGenerator;
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
    public function index(EmojiGenerator $generator, Request $request, SessionInterface $session)
    {
        $form = $this->createForm(GenerateType::class, $criteria = new GenerateCriteria());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $base64 = $generator->setCriteria($criteria)->generate();
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
