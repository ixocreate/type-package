<?php
/**
 * kiwi-suite/common-types (https://github.com/kiwi-suite/common-types)
 *
 * @package kiwi-suite/common-types
 * @link https://github.com/kiwi-suite/common-types
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace Ixocreate\CommonTypes\Entity;

use Doctrine\DBAL\Types\JsonType;
use Ixocreate\Contract\Type\DatabaseTypeInterface;
use Ixocreate\Entity\Type\AbstractType;

final class HtmlType extends AbstractType implements DatabaseTypeInterface
{
    /**
     * @param $value
     * @return array
     */
    protected function transform($value)
    {
        if (\is_string($value)) {
            return [
                'html' => $value,
                'quill' => null,
            ];
        }

        if (\is_array($value) && \array_key_exists("html", $value) && \array_key_exists("quill", $value)) {
            return [
                'html' => $value['html'],
                'quill' => $value['quill'],
            ];
        }

        return $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (empty($this->value())) {
            return "";
        }

        /*if ($this->value()['quill'] !== null) {
            try {
                return (new \DBlackborough\Quill\Render(json_encode($this->value()['quill']), 'HTML'))->render();
            } catch (\Exception $e) {

            }
        }*/

        return (string) $this->value()['html'];
    }

    /**
     * @return string
     */
    public function convertToDatabaseValue()
    {
        return $this->value();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
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
        return 'html';
    }
}
