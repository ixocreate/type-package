<?php
/**
 * kiwi-suite/entity (https://github.com/kiwi-suite/common-types)
 *
 * @package kiwi-suite/common-types
 * @see https://github.com/kiwi-suite/common-types
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace KiwiSuite\CommonTypes\Entity;

use Doctrine\DBAL\Types\JsonType;
use KiwiSuite\Cms\Repository\PageRepository;
use KiwiSuite\Contract\Type\DatabaseTypeInterface;
use KiwiSuite\Entity\Type\AbstractType;
use KiwiSuite\Media\Repository\MediaRepository;

final class LinkType extends AbstractType implements DatabaseTypeInterface
{
    /**
     * @var PageRepository
     */
    private $pageRepository;
    /**
     * @var MediaRepository
     */
    private $mediaRepository;

    public function __construct(PageRepository $pageRepository, MediaRepository $mediaRepository)
    {
        $this->pageRepository = $pageRepository;
        $this->mediaRepository = $mediaRepository;
    }


    /**
     * @param $value
     * @return mixed
     */
    protected function transform($value)
    {
        return $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "";
    }

    /**
     * @return string
     */
    public function convertToDatabaseValue()
    {
        return $this->value();
    }

    /**
     * @return string
     */
    public static function baseDatabaseType(): string
    {
        return JsonType::class;
    }

    public static function serviceName(): string
    {
        return 'link';
    }
}
