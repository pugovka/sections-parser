<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DomCrawler\Crawler;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $page = file_get_contents('https://www.farpost.ru/vladivostok/');
        $crawler = new Crawler($page);
        $sectionsContainerList = $crawler->filter('.first-level');

        $sectionsContainerArray = $sectionsContainerList->each(function ($sectionsContainer) {
            $sectionList = $sectionsContainer->filter('.l1-li');

            return $sectionList->each(function ($section) {
                $sectionName = $section->filter('a')->text();
                $sectionArray = [
                    'name' => $sectionName,
                    'subSections' => []
                ];
                $subSectionList = $section->filter('ul > li > a');

                if ($subSectionList->count() > 0) {
                    $sectionArray['subSections'] = $subSectionList->each(function ($subSection) {
                        return $subSection->text();
                    });
                }

                return $sectionArray;
            });
        });

        return $this->render('default/index.html.twig', [
            'sectionsContainerArray' => $sectionsContainerArray,
        ]);
    }
}
