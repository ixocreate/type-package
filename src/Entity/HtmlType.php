<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Type\Entity;

use Doctrine\DBAL\Types\JsonType;
use Ixocreate\Entity\Type\AbstractType;
use Ixocreate\Entity\Type\Type;
use Ixocreate\Type\DatabaseTypeInterface;
use nadar\quill\BlockListener;
use nadar\quill\InlineListener;
use nadar\quill\Lexer;
use nadar\quill\Line;

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

        if (empty($this->value()['quill'])) {
            return "";
        }

        $lexer = new Lexer($this->value()['quill']);
        $lexer->registerListener(new class() extends InlineListener {
            public function process(Line $line)
            {
                try {
                    $link = $line->getAttribute('ixolink');
                    if ($link) {
                        /** @var LinkType $link */
                        $link = Type::create($link, LinkType::serviceName());
                        $this->updateInput(
                            $line,
                            '<a href="' . (string)$link . '" target="' . $link->getTarget() . '">' . $line->input . '</a>'
                        );
                    }
                } catch (\Exception $exception) {
                }
            }
        });

        $lexer->registerListener(new class() extends BlockListener {
            /**
             * @param Line $line
             * @return void
             */
            public function process(Line $line)
            {
                $linebreak = $line->getAttribute('linebreak');
                if ($linebreak === 'true') {
                    $this->pick($line);
                    $line->setDone();
                }
            }

            public function render(Lexer $lexer)
            {
                foreach ($this->picks() as $pick) {
                    // get all
                    $prev = $pick->line->previous(function (Line $line) {
                        if (!$line->isInline()) {
                            return true;
                        }
                    });

                    // if there is no previous element, we take the same line element.
                    if (!$prev) {
                        $prev = $pick->line;
                    }

                    $pick->line->output = $prev->input . $pick->line->renderPrepend() . '<br>';
                    $prev->setDone();
                }
            }
        });
        return $lexer->render();
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
