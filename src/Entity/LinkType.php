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
        if (!is_array($value)) {
            return [];
        }

        if (empty($value['type'])) {
            return [];
        }

        switch ($value['type']) {
            case 'media':
                if (empty($value['value']['id'])) {
                    return [];
                }

                $value['value'] = $this->mediaRepository->find($value['value']['id']);

                if (empty($value['value'])) {
                    return [];
                }
                break;
            case 'sitemap':
                if (empty($value['value']['id'])) {
                    return [];
                }

                $value['value'] = $this->pageRepository->find($value['value']['id']);

                if (empty($value['value'])) {
                    return [];
                }
                break;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "";
    }

    public function jsonSerialize()
    {
        $array = $this->value();

        if ($array['type'] === "media" || $array['type'] === "sitemap") {
            $array['value'] = $array['value']->toPublicArray();
        }

        return $array;
    }

    /**
     * @return string
     */
    public function convertToDatabaseValue()
    {
        $array = $this->value();

        if ($array['type'] === "media" || $array['type'] === "sitemap") {
            $array['value'] = (string) $array['value']->id();
        }

        return $array;
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
