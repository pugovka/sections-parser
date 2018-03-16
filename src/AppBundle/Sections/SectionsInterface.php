<?php

namespace AppBundle\Sections;

interface SectionsInterface
{
    /**
     * Get parsed sections array from XML content
     *
     * @return array
     */
    public function getSections();
}
