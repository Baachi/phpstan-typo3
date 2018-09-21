<?php
declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;

class ObjectManagerDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return \TYPO3\CMS\Extbase\Object\ObjectManagerInterface::class;
    }

    public function isMethodSupported(
        MethodReflection $methodReflection
    ): bool {
        return $methodReflection->getName() === 'get';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        $arg = $methodCall->args[0]->value;
        if (!($arg instanceof \PhpParser\Node\Expr\ClassConstFetch)) {
            return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
        }
        $class = $arg->class;

        return new ObjectType((string)$class);
    }

}
