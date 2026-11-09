<?php

namespace Hubleto\App\External\WaiBlue\OhsCore\Db\Column;

/**
 * File column whose file is downloaded through a checked route, because the upload folder is not public.
 * The model makes sure a saved path is the record's own (see KeepsStoredFiles).
 */
class StoredFile extends \Hubleto\Framework\Db\Column\File
{
  // The form links the file name to this checked download route ({%ID%} = record id) instead of the upload folder.
  public function setDownloadUrl(string $downloadUrl): StoredFile
  {
    $this->setReactComponent('OhsCoreProtectedFileInput');
    $this->setProperty('downloadUrl', $downloadUrl);
    return $this;
  }
}
