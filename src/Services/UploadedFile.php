<?php

namespace Hubleto\App\External\WaiBlue\OhsCore\Services;

/**
 * Sends a file of the upload folder as a download, and deletes the files of deleted records.
 */
class UploadedFile extends \Hubleto\Erp\Core
{
  public string $translationContext = 'hubleto-app-external-waiblue-ohscore-loader';
  public string $translationContextInner = 'Services\\UploadedFile';

  /**
   * Sets download headers and returns the file content, or a message when the file is missing.
   * $relativePath is the value of a File column (relative to the upload folder).
   */
  public function send(string $relativePath): string
  {
    $filePath = $this->getPathInUploadFolder($relativePath);
    if ($filePath == '') {
      return $this->refuse(404, $this->translate('The file was not found.'));
    }

    $contentType = mime_content_type($filePath);
    if ($contentType === false) $contentType = 'application/octet-stream';

    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: private, no-store');

    return (string) file_get_contents($filePath);
  }

  // Deletes an uploaded file. A path that leads outside the upload folder is ignored.
  public function delete(string $relativePath): void
  {
    $filePath = $this->getPathInUploadFolder($relativePath);
    if ($filePath != '') unlink($filePath);
  }

  // Full path of an existing file in the upload folder, or '' when there is none.
  private function getPathInUploadFolder(string $relativePath): string
  {
    if ($relativePath == '') return '';

    $uploadFolder = realpath($this->config()->getAsString('uploadFolder'));
    $filePath = realpath($uploadFolder . '/' . $relativePath);

    // realpath() resolves "..", so this also stops paths that point outside the upload folder.
    $isFileInUploadFolder = $uploadFolder !== false
      && $filePath !== false
      && str_starts_with($filePath, $uploadFolder . DIRECTORY_SEPARATOR)
      && is_file($filePath);

    return $isFileInUploadFolder ? $filePath : '';
  }

  // Plain-text answer when the file does not exist.
  public function refuse(int $httpStatus, string $message): string
  {
    http_response_code($httpStatus);
    header('Content-Type: text/plain; charset=utf-8');
    return $message;
  }
}
