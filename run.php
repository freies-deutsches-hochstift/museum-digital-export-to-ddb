<?PHP
/**
 * Runs the actual downloads to file.
 *
 * @author Joshua Ramon Enslin<jenslin@freies-deutsches-hochstift.de>
 */
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function setupCurlConnection(string $url) {

    $ch =  curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-MUSDB-API-USER: ' . MUSDB_API_USER,
        'X-MUSDB-API-KEY: ' . MUSDB_API_KEY,
    ]);

    curl_setopt($ch, CURLOPT_USERAGENT, 'FDH export script');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,  15);
    curl_setopt($ch, CURLOPT_COOKIEJAR,  __DIR__ . '/cookiefile');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookiefile');

    return $ch;

}

function fetch(string $outfile, string $type, string $query) {

    if (!is_writable(dirname($outfile))) {
        throw new Exception("Cannot write to output file: " . $outfile);
    }

    // Back up current file
    $backupFile = "/tmp/" . uniqid(basename($outfile)) . ".bak";
    if (is_file($outfile)) {
        copy($outfile, $backupFile);
    }

    $exportUrl = MUSDB_INSTANCE . '/musdb/api/export/objects_quick/' . urlencode($type) . '?s=' . urlencode($query);
    $exportGenTime = MUSDB_INSTANCE . '/musdb/api/meta/get_quick_export_update_time/' . urlencode($type) . '?s=' . urlencode($query);

    // Check if the export has already been generated recently.

    if (file_exists($outfile)) {

        $ch = setupCurlConnection($exportGenTime);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception("Failed to download " . $exportGenTime . PHP_EOL . PHP_EOL . "Error: " . curl_error($ch));
        }
        curl_close($ch);

        if (!($updateTimeData = json_decode($result, true))) {
            throw new Exception("Failed to get last update time");
        }
        /*
        if ($updateTimeData['update_time'] < filemtime($outfile)) {
            echo "Export has already been generated more recently than the export dumps" . PHP_EOL;
            return;
        }
         */

        if (unlink($outfile)) echo "Unlinked outfile: " . $outfile . PHP_EOL;

    }

    // Run export

    $ch = setupCurlConnection($exportUrl);
    $fh = fopen($outfile, "w") or exit("Error opening output file");
    curl_setopt($ch, CURLOPT_FILE, $fh);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception("Failed to download " . $exportUrl . PHP_EOL . PHP_EOL . "Error: " . curl_error($ch));
    }

    // Get info to check status of request
    if (!($info = curl_getinfo($ch))) {
        throw new Exception("Failed to get status of request");
    }

    curl_close($ch);
    fclose($fh);

    $msg = 'A quick export has been run using the search query \'' . $query . '\'. Exported to file: ' . basename($outfile);
    echo $msg . PHP_EOL;

    // Request failed: Recreate backup
    // A filesize below 2048 signals a failure due to error messages being around 1KB large
    if (((int)$info['http_code'] !== 200 || filesize($outfile) < 2048) && file_exists($backupFile)) {
        echo PHP_EOL . "Request failed. Will recreate from backup file." . PHP_EOL;
        copy($backupFile, $outfile);
    }

    else {
        /*
        mail(implode(', ', MAIL_NOTIFICATION_TO),
            "Targetted musdb export succeeded",
            $msg);
        */
    }

}

foreach (EXPORTS_TO_GENERATE as $toExport) {

    if (empty($toExport['type']) || !isset($toExport['query']) || empty($toExport['outfile'])) {
        throw new Exception("Invalid config. Requires 1) type, 2) query, and 3) outfile as array keys");
    }

    fetch(
        $toExport['outfile'],
        $toExport['type'],
        $toExport['query'],
    );

}
