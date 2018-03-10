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
        $mainTabs = $crawler->filter('#content > .mainTabs');

        $sectionsContainerArray = $sectionsContainerList->each(function ($sectionsContainer) use ($mainTabs) {
            $sectionList = $sectionsContainer->filter('.l1-li');

            return $sectionList->each(function ($section) use ($mainTabs) {
                $sectionLink= $section->filter('a');
                $sectionArray = [
                    'name' => $sectionLink->text(),
                    'subSections' => []
                ];
                $sectionHref = $sectionLink->attr('href');
                $tabSection = $mainTabs->filter(".selector a[href='$sectionHref']");
                if ($tabSection->count() > 0) {
                    $tabPath = $tabSection->attr('class');
                    $subSectionList = $mainTabs->filter(".tab[data-name='$tabPath'] .option");
                } else {
                    $subSectionList = $section->filter('ul > li > a');
                }

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
