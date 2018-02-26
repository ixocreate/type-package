<?php
namespace KiwiSuite\CommonTypes\Entity;

use KiwiSuite\Entity\Type\Convert\Convert;
use KiwiSuite\Entity\Type\TypeInterface;

final class ColorType implements TypeInterface
{
    private $color;

    public function __construct(string $value)
    {
        if (substr($value, 0, 1) !== '#') {
            //TODO Exception
            throw new \Exception("invalid hex color");
        }
        $value = substr($value, 1);

        if (strlen($value) === 3) {
            $value = $value . $value;
        }

        if (strlen($value) !== 6) {
            //TODO Exception
            throw new \Exception("invalid hex color");
        }

        if (!preg_match('/^[a-f0-9]{6}$/i', $value)) {
            //TODO Exception
            throw new \Exception("invalid hex color");
        }

        $this->color = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return '#' . $this->color;
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function convertToInternalType($value)
    {
        return Convert::convertString($value);
    }

    public function __toString()
    {
        return (string)$this->getValue();
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->getValue();
    }

    public function toRgb()
    {
        return [
            'r' => hexdec($this->color[0] . $this->color[1]),
            'g' => hexdec($this->color[2] . $this->color[3]),
            'b' => hexdec($this->color[4] . $this->color[5]),
        ];
    }
}
