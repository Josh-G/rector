<?php

declare(strict_types=1);

namespace Rector\TypeDeclaration\Rector\Property;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PHPStan\Type\MixedType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\TypeDeclaration\TypeInferer\PropertyTypeInferer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @sponsor Thanks https://spaceflow.io/ for sponsoring this rule - visit them on https://github.com/SpaceFlow-app
 *
 * @see \Rector\TypeDeclaration\Tests\Rector\Property\PropertyTypeDeclarationRector\PropertyTypeDeclarationRectorTest
 */
final class PropertyTypeDeclarationRector extends AbstractRector
{
    /**
     * @var PropertyTypeInferer
     */
    private $propertyTypeInferer;

    public function __construct(PropertyTypeInferer $propertyTypeInferer)
    {
        $this->propertyTypeInferer = $propertyTypeInferer;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add @var to properties that are missing it', [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $value;

    public function run()
    {
        $this->value = 123;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @var int
     */
    private $value;

    public function run()
    {
        $this->value = 123;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class];
    }

    /**
     * @param Property $node
     */
    public function refactor(Node $node): ?Node
    {
        if (count((array) $node->props) !== 1) {
            return null;
        }

        if ($node->type !== null) {
            return null;
        }

        // is already set
        /** @var PhpDocInfo|null $phpDocInfo */
        $phpDocInfo = $node->getAttribute(AttributeKey::PHP_DOC_INFO);
        if ($phpDocInfo !== null && ! $phpDocInfo->getVarType() instanceof MixedType) {
            return null;
        }

        $type = $this->propertyTypeInferer->inferProperty($node);
        if ($type instanceof MixedType) {
            return null;
        }

        if ($phpDocInfo === null) {
            $phpDocInfo = $this->phpDocInfoFactory->createEmpty($node);
        }

        $phpDocInfo->changeVarType($type);

        return $node;
    }
}
