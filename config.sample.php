<?PHP
/**
 * Config for the targetted musdb export downloader - sample.
 * Rename this sample file to config.php and fill in the values for configuring
 * the application.
 *
 * @author Joshua Ramon Enslin<jenslin@freies-deutsches-hochstift.de>
 */
declare(strict_types=1);

const MUSDB_API_USER = 'bot username';
const MUSDB_API_KEY = 'API token';
const MUSDB_INSTANCE = 'https://hessen.museum-digital.de';

// After the exporting is done, a mail is sent to the mail addresses
// entered here.
const MAIL_NOTIFICATION_TO = ['mail@example.com'];

const EXPORTS_TO_GENERATE = [
    [
        // LIDO or mdxml - export format
        'type' => 'lido',
        // A search query
        'query' => 'published collection:1',
        // Output file location
        'outfile' => __DIR__ . '/kunstsammlungen-YNKIAG7DFC4HZOATBPG75IFEY5BUDYUS.zip'
    ],
    [
        'type' => 'lido',
        'query' => 'published collection:5',
        'outfile' => __DIR__ . '/handschriftensammlung-GWSVRXX3ZBOKP2Q3HEZR46INRVVTHXAG.zip'
    ],
    /*
    [
        'type' => 'lido',
        'query' => 'published collection:870',
        'outfile' => __DIR__ . '/bibliothek-JZAY6KR66YJ3LZHUOJ4RDA3Z43ZRTMMN.zip'
    ],
     */
];
