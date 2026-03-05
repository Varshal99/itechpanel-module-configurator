<?php
/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ItechPanel\Configurator\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Uploader;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Image uploader for configurator
 */
class ImageUploader
{
    /**
     * @var Database
     */
    private $coreFileStorageDatabase;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $baseTmpPath;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string[]
     */
    private $allowedExtensions;

    /**
     * @param Database $coreFileStorageDatabase
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param string $baseTmpPath
     * @param string $basePath
     * @param string[] $allowedExtensions
     */
    public function __construct(
        Database $coreFileStorageDatabase,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        string $baseTmpPath = 'itechpanel/configurator/tmp',
        string $basePath = 'itechpanel/configurator',
        array $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png', 'webp']
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->baseTmpPath = $baseTmpPath;
        $this->basePath = $basePath;
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * Save file to temporary directory
     *
     * @param string $fileId
     * @return array
     * @throws \Exception
     */
    public function saveFileToTmpDir($fileId)
    {
        try {
            $baseTmpPath = $this->getBaseTmpPath();
            
            /** @var Uploader $uploader */
            $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
            $uploader->setAllowedExtensions($this->allowedExtensions);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            
            $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
            
            if (!$result) {
                throw new \Exception((string)__('File could not be saved to the destination folder.'));
            }

            $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
            $result['path'] = str_replace('\\', '/', $result['path']);
            $result['url'] = $this->storeManager
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . $this->getFilePath($baseTmpPath, $result['file']);
            $result['name'] = $result['file'];

            if (isset($result['file'])) {
                try {
                    $relativePath = rtrim($baseTmpPath, '/') . '/' . ltrim($result['file'], '/');
                    $this->coreFileStorageDatabase->saveFile($relativePath);
                } catch (\Exception $e) {
					$this->logger->critical($e);
					throw new \Exception($e->getMessage());
				}
            }

            return $result;
        } catch (\Exception $e) {
            throw new \Exception((string)$e->getMessage());
        }
    }

    /**
	 * Move file from temporary directory to destination
	 *
	 * @param string $imageName
	 * @return string
	 * @throws \Exception
	 */
	public function moveFileFromTmp($imageName)
	{
		$baseTmpPath = $this->getBaseTmpPath();
		$basePath = $this->getBasePath();

		$baseImagePath = $this->getFilePath($basePath, $imageName);
		$baseTmpImagePath = $this->getFilePath($baseTmpPath, $imageName);

		try {
			if (!$this->mediaDirectory->isFile($baseTmpImagePath)) {
				if ($this->mediaDirectory->isFile($baseImagePath)) {
					return $imageName;
				} else {
					throw new \Exception(
						(string)__('File not found in tmp or destination folders: %1', $imageName)
					);
				}
			}

			if (!$this->mediaDirectory->isDirectory($basePath)) {
				$this->mediaDirectory->create($basePath);
			}

			$this->coreFileStorageDatabase->copyFile(
				$baseTmpImagePath,
				$baseImagePath
			);
			$this->mediaDirectory->renameFile(
				$baseTmpImagePath,
				$baseImagePath
			);
		} catch (\Exception $e) {
			$this->logger->critical($e);
			throw new \Exception($e->getMessage());
		}

		return $imageName;
	}

    /**
     * Get base temporary path
     *
     * @return string
     */
    private function getBaseTmpPath()
    {
        return $this->baseTmpPath ?? 'itechpanel/configurator/tmp';
    }

    /**
     * Get base path
     *
     * @return string
     */
    private function getBasePath()
    {
        return $this->basePath ?? 'itechpanel/configurator';
    }

    /**
     * Get file path
     *
     * @param string $path
     * @param string $imageName
     * @return string
     */
    private function getFilePath($path, $imageName)
    {
        return rtrim($path ?? '', '/') . '/' . ltrim($imageName, '/');
    }
}
