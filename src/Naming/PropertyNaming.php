<?php

declare(strict_types=1);

namespace Rector\Core\Naming;

use Nette\Utils\Strings;
use PHPStan\Type\ObjectType;

final class PropertyNaming
{
    /**
     * @param ObjectType|string $objectType
     */
    public function fqnToVariableName($objectType): string
    {
        if ($objectType instanceof ObjectType) {
            $className = $objectType->getClassName();
        } else {
            $className = $objectType;
        }

        $shortName = $this->fqnToShortName($className);
        $shortName = $this->removeInterfaceSuffixPrefix($className, $shortName);

        return lcfirst($shortName);
    }

    /**
     * @source https://stackoverflow.com/a/2792045/1348344
     */
    public function underscoreToName(string $underscoreName): string
    {
        $camelCaseName = str_replace('_', '', ucwords($underscoreName, '_'));

        return lcfirst($camelCaseName);
    }

    private function fqnToShortName(string $fqn): string
    {
        if (! Strings::contains($fqn, '\\')) {
            return $fqn;
        }

        /** @var string $lastNamePart */
        $lastNamePart = Strings::after($fqn, '\\', - 1);
        if (Strings::endsWith($lastNamePart, 'Interface')) {
            return Strings::substring($lastNamePart, 0, - strlen('Interface'));
        }

        return $lastNamePart;
    }

    private function removeInterfaceSuffixPrefix(string $className, string $shortName): string
    {
        // remove interface prefix/suffix
        if (! interface_exists($className)) {
            return $shortName;
        }

        // starts with "I\W+"?
        if (Strings::match($shortName, '#^I[A-Z]#')) {
            return Strings::substring($shortName, 1);
        }

        if (Strings::match($shortName, '#Interface$#')) {
            return Strings::substring($shortName, -strlen('Interface'));
        }

        return $shortName;
    }
}
