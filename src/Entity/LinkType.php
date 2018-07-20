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
use KiwiSuite\Cms\Router\PageRoute;
use KiwiSuite\Contract\Schema\ElementInterface;
use KiwiSuite\Contract\Type\DatabaseTypeInterface;
use KiwiSuite\Contract\Type\SchemaElementInterface;
use KiwiSuite\Entity\Type\AbstractType;
use KiwiSuite\Media\Entity\Media;
use KiwiSuite\Media\Repository\MediaRepository;
use KiwiSuite\Media\Uri\Uri;
use KiwiSuite\Schema\Elements\LinkElement;
use KiwiSuite\Schema\ElementSubManager;

final class LinkType extends AbstractType implements DatabaseTypeInterface, SchemaElementInterface
{
    /**
     * @var PageRepository
     */
    private $pageRepository;
    /**
     * @var MediaRepository
     */
    private $mediaRepository;
    /**
     * @var PageRoute
     */
    private $pageRoute;
    /**
     * @var Uri
     */
    private $uri;

    /**
     * LinkType constructor.
     * @param PageRepository $pageRepository
     * @param MediaRepository $mediaRepository
     * @param PageRoute $pageRoute
     * @param Uri $uri
     */
    public function __construct(
        PageRepository $pageRepository,
        MediaRepository $mediaRepository,
        PageRoute $pageRoute,
        Uri $uri
    ) {
        $this->pageRepository = $pageRepository;
        $this->mediaRepository = $mediaRepository;
        $this->pageRoute = $pageRoute;
        $this->uri = $uri;
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
                if (is_array($value['value'])) {
                    if (empty($value['value']['id'])) {
                        return [];
                    }

                    $value['value'] = $value['value']['id'];
                }

                $value['value'] = $this->mediaRepository->find($value['value']);

                if (empty($value['value'])) {
                    return [];
                }
                break;
            case 'sitemap':
                if (is_array($value['value'])) {
                    if (empty($value['value']['id'])) {
                        return [];
                    }

                    $value['value'] = $value['value']['id'];
                }


                $value['value'] = $this->pageRepository->find($value['value']);

                if (empty($value['value'])) {
                    return [];
                }
                break;
        }

        return $value;
    }

    public function getType(): ?string
    {
        $array = $this->value();

        if (empty($array)) {
            return null;
        }

        return $array['type'];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $array = $this->value();

        if (empty($array)) {
            return "";
        }

        switch ($array['type']) {
            case 'media':
                return $this->assembleMediaUrl();
            case 'sitemap':
                return $this->assemblePageUrl();
            case 'external':
                return $this->assembleExternalUrl();
        }

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

    private function assemblePageUrl(): string
    {
        return $this->pageRoute->fromPage($this->value()['value']);
    }

    private function assembleMediaUrl(): string
    {
        if (!($this->value()['value'] instanceof Media)) {
            return "";
        }

        return $this->uri->url($this->value()['value']);
    }

    private function assembleExternalUrl(): string
    {
        return $this->value()['value'];
    }

    public function schemaElement(ElementSubManager $elementSubManager): ElementInterface
    {
        return $elementSubManager->get(LinkElement::class);
    }
}
