<?php

namespace benf\neo\gql\interfaces\elements;

use benf\neo\elements\Block as NeoBlock;
use benf\neo\gql\types\generators\BlockType;

use craft\gql\interfaces\Element;
use craft\gql\TypeLoader;
use craft\gql\GqlEntityRegistry;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

/**
 * Class MatrixBlock
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.3.0
 */
class Block extends Element
{
	/**
	 * @inheritdoc
	 */
	public static function getTypeGenerator(): string
	{
		return BlockType::class;
	}
	
	/**
	 * @inheritdoc
	 */
	public static function getType($fields = null): Type
	{
		if ($type = GqlEntityRegistry::getEntity(self::class)) {
			return $type;
		}
		
		$type = GqlEntityRegistry::createEntity(self::class, new InterfaceType([
			'name' => static::getName(),
			'fields' => self::class . '::getFieldDefinitions',
			'description' => 'This is the interface implemented by all neo blocks.',
			'resolveType' => static function (NeoBlock $value) {
				return $value->getGqlTypeName();
			}
		]));
		
		foreach (BlockType::generateTypes() as $typeName => $generatedType) {
			TypeLoader::registerType($typeName, function () use ($generatedType) { return $generatedType ;});
		}
		
		return $type;
	}
	
	/**
	 * @inheritdoc
	 */
	public static function getName(): string
	{
		return 'NeoBlockInterface';
	}
	
	/**
	 * @inheritdoc
	 */
	public static function getFieldDefinitions(): array {
		return array_merge(parent::getFieldDefinitions(), [
			'fieldId' => [
				'name' => 'fieldId',
				'type' => Type::int(),
				'description' => 'The ID of the field that owns the neo block.'
			],
			'level' => [
				'name' => 'level',
				'type' => Type::int(),
				'description' => 'The Neo block\'s level.',
			],
			'ownerId' => [
				'name' => 'ownerId',
				'type' => Type::int(),
				'description' => 'The ID of the element that owns the neo block.'
			],
			'typeId' => [
				'name' => 'typeId',
				'type' => Type::int(),
				'description' => 'The ID of the neo block\'s type.'
			],
			'typeHandle' => [
				'name' => 'typeHandle',
				'type' => Type::string(),
				'description' => 'The handle of the neo block\'s type.'
			]
		]);
	}
}
