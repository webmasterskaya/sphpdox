<?php

namespace Sphpdox\Element;

use Symfony\Component\Console\Output\OutputInterface;
use TokenReflection\IReflectionProperty;

/**
 * Property element
 */
class PropertyElement extends Element
{
    public function __construct(IReflectionProperty $property)
    {
        $this->reflection = $property;
    }

    public function __toString()
    {
        if ($this->reflection->isPrivate()) {
            return '';
        }

        $parser = $this->getParser();

        if ($parser->hasAnnotation('private')) {
            return '';
        }

        $method = $this->reflection->getName();

        $string = sprintf(".. php:attr:: %s\n\n", $method);
        $string .= $this->getModifierLine();

        $string .= $this->indent($parser->getDescription(), 4, true);

        return $string;
    }

    protected function getModifierLine()
    {
        $line = [];
        if ($this->getTypeModifier()) {
            $line[] = sprintf("    :type: %s\n\n", $this->getTypeModifier());
        }
        if ($this->getVisibilityModifier()) {
            $line[] = sprintf("    :scope: %s\n\n", $this->getVisibilityModifier());
        }

        if ($line) {
            $line = implode("", $line);
        } else {
            $line = '';
        }

        return $line;
    }

    protected function getTypeModifier()
    {
        $vars = $this->getParser()->getAnnotationsByName('var');
        $var = count($vars) ? array_pop($vars) : false;
        $parts = preg_split('/\s+/', $var);

        if (count($parts) >= 2) {
            if ($parts[1]) {
                return $parts[1];
            }
        }
    }

    protected function getVisibilityModifier()
    {
        if ($this->reflection->isProtected()) {
            return 'protected';
        }
    }
}