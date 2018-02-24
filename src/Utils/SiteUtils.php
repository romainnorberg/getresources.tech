<?php

namespace App\Utils;

use App\Entity\Site;
use League\Uri;

class SiteUtils
{
    /* @var $site \App\Entity\Site */
    private $site;

    private $defaultQueries = [
        'utm_source=getresources.tech',
        'utm_medium=site',
    ];

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function getUrlWithExtra(array $extras = null)
    {
        $siteUrl = Uri\Http::createFromString($this->site->getUrl());

        // Default
        foreach ($this->defaultQueries as $query) {
            $siteUrl = Uri\merge_query($siteUrl, $query);
        }

        // Extras
        if (null !== $extras) {
            foreach ($extras as $query) {
                $siteUrl = Uri\merge_query($siteUrl, $query);
            }
        }

        return $siteUrl;
    }
}