<?php
/**
 * @see https://github.com/artesaos/seotools
 */

$title = 'corona virus statistic';
$description = 'make same day statistic for some countries';

return [
    'meta' => [
        /*
         * The default configurations to be used by the meta generator.
         */
        'defaults'       => [
            'title'        => $title, // set false to total remove
            'titleBefore'  => false, // Put defaults.title before page title, like 'It's Over 9000! - Dashboard'
            'description'  => $description, // set false to total remove
            'separator'    => ' - ',
            'keywords'     => [
                'corona','virus','israel','spain','italy','argentina',
                'china','united states','usa','united kingdom','uk','germany','statistic',
            ],
            'canonical'    => false, // Set null for using Url::current(), set false to total remove
            'robots'       => 'nofollow', // Set to 'all', 'none' or any combination of index/noindex and follow/nofollow
        ],
        /*
         * Webmaster tags are always added.
         */
        'webmaster_tags' => [
            'google'    => null,
            'bing'      => null,
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => null,
            'norton'    => null,
        ],

        'add_notranslate_class' => false,
    ],
    'opengraph' => [
        /*
         * The default configurations to be used by the opengraph generator.
         */
        'defaults' => [
            'title'       => $title, // set false to total remove
            'description'  => $description, // set false to total remove
            'url'         => false, // Set null for using Url::current(), set false to total remove
            'type'        => false,
            'site_name'   => false,
            'images'      => [],
        ],
    ],
    'twitter' => [
        /*
         * The default values to be used by the twitter cards generator.
         */
        'defaults' => [
            //'card'        => 'summary',
            //'site'        => '@LuizVinicius73',
        ],
    ],
    'json-ld' => [
        /*
         * The default configurations to be used by the json-ld generator.
         */
        'defaults' => [
            'title'       => $title, // set false to total remove
            'description' => $description, // set false to total remove
            'url'         => false, // Set null for using Url::current(), set false to total remove
            'type'        => 'WebPage',
            'images'      => [],
        ],
    ],
];
