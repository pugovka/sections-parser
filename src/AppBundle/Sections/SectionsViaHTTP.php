<?php

namespace AppBundle\Sections;

use DOMDocument;
use \GuzzleHttp\Client;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class SectionsViaHTTP implements SectionsInterface
{
    const URI = 'https://www.farpost.ru/vladivostok/';
    const CACHE_EXPIRE_TIME = 30 * 60;

    public function getSections()
    {
        $xmlElement = $this->getContent();
        if (($xmlElement instanceof \SimpleXMLElement) == false) {
            return [];
        }

        $sectionsContainerXMLArray = $xmlElement
            ->xpath('//table[@id="index-dirs"]//ul[contains(@class, "first-level")]');
        $mainTabs = $this->getFirstOrNull($xmlElement->xpath('//div[@id="content"]/div[contains(@class, "mainTabs")]'));
        $sectionsArray = [
            'name' => 'root',
            'subSections' => [],
        ];

        if (empty($sectionsContainerXMLArray)) {
            return $sectionsArray;
        }
        $sectionsContainerArray = [];

        foreach ($sectionsContainerXMLArray as $containerIndex => $sectionsContainer) {
            $sectionXMLArray = $sectionsContainer->xpath('li[@class="l1-li"]');

            foreach ($sectionXMLArray as $sectionIndex => $section) {
                $sectionLink = $this->getFirstOrNull($section->xpath('a[@class="l1"]'));

                if (empty($sectionLink)) {
                    continue;
                }
                $sectionsContainerArray[$containerIndex][$sectionIndex] = [
                    'name' => (string)$this->getFirstOrNull($sectionLink->xpath('text()')),
                    'subSections' => []
                ];
                $sectionHref = (string)$this->getFirstOrNull($sectionLink->xpath('@href'));
                $tabSection = $this->getFirstOrNull(
                    $mainTabs->xpath("//div[contains(@class, 'selector')]//a[contains(@href, '$sectionHref')]")
                );

                $tabPath = empty($tabSection) ? null : $this->getFirstOrNull($tabSection->xpath('@class'));
                if (!empty($tabPath)) {
                    $subSectionList =
                        $mainTabs->xpath("//div[contains(@data-name, '$tabPath')]//a[contains(@class, 'option')]");
                } else {
                    $subSectionList = $section->xpath('ul/li/a');
                }

                if (count($subSectionList) > 0) {
                    foreach ($subSectionList as $subSection) {
                        $sectionsContainerArray[$containerIndex][$sectionIndex]['subSections'][] = [
                            'name' => (string)$subSection
                        ];
                    }
                }
            }
        }

        $sectionsArray['subSections'] = call_user_func_array('array_merge', $sectionsContainerArray);

        return $sectionsArray;
    }

    private function getFirstOrNull($object)
    {
        return (is_array($object) && !empty($object[0])) ? $object[0] : null;
    }

    private function getContent()
    {
        $cache = new FilesystemAdapter();

        $latestXMLString = $cache->getItem('latest_XML_string');
        if ($latestXMLString->isHit()) {
            return simplexml_load_string($latestXMLString->get());
        }
        $client = new Client();
        $request = $client->get(self::URI);
        $body = $request->getBody();

        $document = new DOMDocument('1.0', 'UTF-8');
        $internalErrors = libxml_use_internal_errors(true);
        $document->loadHTML($body);
        libxml_use_internal_errors($internalErrors);
        $document->encoding = 'UTF-8';
        $xmlString = $document->saveXML();

        $latestXMLString->set($xmlString);
        $latestXMLString->expiresAfter(self::CACHE_EXPIRE_TIME);
        $cache->save($latestXMLString);

        return simplexml_load_string($xmlString);
    }
}
