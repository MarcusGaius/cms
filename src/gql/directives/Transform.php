<?php
namespace craft\gql\directives;

use Craft;
use craft\gql\GqlEntityRegistry;
use GraphQL\Language\DirectiveLocation;
use GraphQL\Type\Definition\Directive;
use GraphQL\Type\Definition\FieldArgument;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

/**
 * Class Transform
 */
class Transform extends BaseDirective
{
    /**
     * @inheritdoc
     */
    public static function getDirective(): Directive
    {
        if ($type = GqlEntityRegistry::getEntity(self::class)) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(static::getName(), new self([
            'name' => static::getName(),
            'locations' => [
                DirectiveLocation::FIELD,
            ],
            'args' => [
                new FieldArgument([
                    'name' => 'handle',
                    'type' => Type::string(),
                    'description' => 'The handle of the named transform to use.'
                ]),
                new FieldArgument([
                    'name' => 'width',
                    'type' => Type::int(),
                    'description' => 'Width for the generated transform'
                ]),
                new FieldArgument([
                    'name' => 'height',
                    'type' => Type::int(),
                    'description' => 'Height for the generated transform'
                ]),
                new FieldArgument([
                    'name' => 'mode',
                    'type' => Type::string(),
                    'description' => 'The mode to use for the generated transform.'
                ]),
                new FieldArgument([
                    'name' => 'position',
                    'type' => Type::string(),
                    'description' => 'The position to use when cropping, if no focal point specified.'
                ]),
                new FieldArgument([
                    'name' => 'interlace',
                    'type' => Type::string(),
                    'description' => 'The interlace mode to use for the transform'
                ]),
                new FieldArgument([
                    'name' => 'quality',
                    'type' => Type::int(),
                    'description' => 'The quality of the transform'
                ]),
                new FieldArgument([
                    'name' => 'format',
                    'type' => Type::string(),
                    'description' => 'The format to use for the transform'
                ]),
                new FieldArgument([
                    'name' => 'immediately',
                    'type' => Type::boolean(),
                    'description' => 'Whether the transform should be generated immediately or only when the image is requested used the generated URL'
                ]),
            ],
        ]));

        return $type;
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'transform';
    }

    /**
     * @inheritdoc
     */
    public static function applyDirective($source, $value, array $arguments, ResolveInfo $resolveInfo)
    {
        if ($resolveInfo->fieldName !== 'url') {
            return $value;
        }

        $generateNow = $arguments['immediately'] ?? Craft::$app->getConfig()->general->generateTransformsBeforePageLoad;
        unset($arguments['immediately']);

        if (!empty($arguments['handle'])) {
            $transform = $arguments['handle'];
        } else {
            $transform = $arguments;
        }

        return Craft::$app->getAssets()->getAssetUrl($source, $transform, $generateNow);
    }
}
