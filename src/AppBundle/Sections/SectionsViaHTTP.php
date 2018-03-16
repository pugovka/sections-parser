<?php

namespace AppBundle\Sections;

use DOMDocument;
use \GuzzleHttp\Client;

class SectionsViaHTTP implements SectionsInterface
{
    const URI = 'https://www.farpost.ru/vladivostok/';

    public function getSections()
    {
        $xmlElement = $this->getContent();
        if (($xmlElement instanceof \SimpleXMLElement) == false) {
            return [];
        }

        $sectionsContainerXMLArray = $xmlElement
            ->xpath('//table[@id="index-dirs"]//ul[contains(@class, "first-level")]');
        $mainTabs = $this->getFirstOrNull($xmlElement->xpath('//div[@id="content"]/div[contains(@class, "mainTabs")]'));
        $sectionsContainerArray = [];

        if (empty($sectionsContainerXMLArray)) {
            return [];
        }

        foreach ($sectionsContainerXMLArray as $containerIndex => $sectionsContainer) {
            $sectionXMLArray = $sectionsContainer->xpath('li[@class="l1-li"]');
            if (empty($sectionXMLArray)) {
                return [];
            }

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
                        $sectionsContainerArray[$containerIndex][$sectionIndex]['subSections'][] = (string)$subSection;
                    }
                }
            }
        }

        return call_user_func_array('array_merge', $sectionsContainerArray);
    }

    private function getFirstOrNull($object)
    {
        return (is_array($object) && !empty($object[0])) ? $object[0] : null;
    }

    private function getContent()
    {
        $client = new Client();
        $request = $client->get(self::URI);
        $body = $request->getBody();

        $document = new DOMDocument('1.0', 'UTF-8');
        $internalErrors = libxml_use_internal_errors(true);
        $document->loadHTML($body);
        libxml_use_internal_errors($internalErrors);
        $xmlString = $document->saveXML();

        return simplexml_load_string($xmlString);
    }
}
