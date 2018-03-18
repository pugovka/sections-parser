<?php

namespace AppBundle\Controller;

use AppBundle\Sections\SectionsInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    private $sections;

    public function __construct(SectionsInterface $sections)
    {
        $this->sections = $sections;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig', [
            'sections' => json_encode($this->sections->getSections())
        ]);
    }
}
