<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Service;

use WPZinc\Airwallex\AirwallexObject;
use WPZinc\Airwallex\Exception\InvalidArgumentException;

/**
 * File Service: upload files (to the files.* host) and get download links.
 *
 * @see https://www.airwallex.com/docs/api/supporting_services/file_service
 */
class FileService extends AbstractService
{
    /**
     * Upload a file, e.g. for dispute evidence or KYC documents.
     *
     * POST /api/v1/files/upload (multipart/form-data, files.airwallex.com)
     *
     * @param string|\SplFileInfo|\CURLFile $file   Path, SplFileInfo or CURLFile.
     * @param array<string, mixed>          $params Extra form fields, e.g. ['notes' => '...'].
     * @param array<string, mixed>          $opts   Request options.
     */
    public function upload(string|\SplFileInfo|\CURLFile $file, array $params = [], array $opts = []): AirwallexObject
    {
        if (! $file instanceof \CURLFile) {
            $path = $file instanceof \SplFileInfo ? $file->getPathname() : $file;
            if (! is_file($path) || ! is_readable($path)) {
                throw new InvalidArgumentException(sprintf('File not found or not readable: %s', $path));
            }
            $file = new \CURLFile($path, mime_content_type($path) ?: 'application/octet-stream', basename($path));
        }

        [$json, $response] = $this->requestor->requestMultipart('/api/v1/files/upload', ['file' => $file] + $params, $opts);

        return AirwallexObject::constructFrom(is_array($json) ? $json : [], $response);
    }

    /**
     * Get download links for uploaded files.
     *
     * POST /api/v1/files/download_links
     *
     * @param array<string, mixed> $params e.g. ['file_ids' => ['...']].
     * @param array<string, mixed> $opts   Request options.
     */
    public function downloadLinks(array $params = [], array $opts = []): AirwallexObject
    {
        return $this->request('post', '/api/v1/files/download_links', $params, $opts);
    }
}
