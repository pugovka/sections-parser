<?php

// Workaround https://bugs.php.net/64566
if (ini_get('auto_prepend_file') && !in_array(realpath(ini_get('auto_prepend_file')), get_included_files(), true)) {
    require ini_get('auto_prepend_file');
}
$filePath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $_SERVER['SCRIPT_NAME'];
if (is_file($filePath)) {
    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimes = array(
        'txt' => 'text/plain',
        'xsd' => 'text/plain',
        'avi' => 'video/avi',
        'bmp' => 'image/bmp',
        'css' => 'text/css',
        'gif' => 'image/gif',
        'htm' => 'text/html',
        'html' => 'text/html',
        'htmls' => 'text/html',
        'ico' => 'image/x-ico',
        'jpe' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'js' => 'application/javascript',
        'midi' => 'audio/midi',
        'mid' => 'audio/midi',
        'mod' => 'audio/mod',
        'mov' => 'movie/quicktime',
        'mp3' => 'audio/mp3',
        'mpg' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'pdf' => 'application/pdf',
        'png' => 'image/png',
        'swf' => 'application/shockwave-flash',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'wav' => 'audio/wav',
        'xbm' => 'image/xbm',
        'xml' => 'text/xml',
    );

    if (empty($mimes[$fileExtension])) {
        return false;
    }

    $lastModifiedTime = filemtime($filePath);
    header('Cache-Control: public, maxage=86400');
    header('Content-Type: ' . $mimes[$fileExtension]);
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModifiedTime) . ' GMT');
    header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+1 day')) . ' GMT');

    if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
        strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModifiedTime
    ) {
        header('HTTP/1.1 304 Not Modified');
    }

    require $filePath;
    return;
}

$script = isset($_ENV['APP_FRONT_CONTROLLER']) ? $_ENV['APP_FRONT_CONTROLLER'] : 'index.php';

$_SERVER = array_merge($_SERVER, $_ENV);
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $script;

// Since we are rewriting to app_dev.php, adjust SCRIPT_NAME and PHP_SELF accordingly
$_SERVER['SCRIPT_NAME'] = DIRECTORY_SEPARATOR . $script;
$_SERVER['PHP_SELF'] = DIRECTORY_SEPARATOR . $script;

require $script;

error_log(sprintf('%s:%d [%d]: %s', $_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_PORT'], http_response_code(),
    $_SERVER['REQUEST_URI']), 4);

// Register request uri
$requestUri = isset($_SERVER['REQUEST_URI'])
    ? $_SERVER['REQUEST_URI']
    : '/';
