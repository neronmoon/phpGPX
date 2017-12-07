<?php
/**
 * Created            15/02/2017 18:29
 * @author            Jakub Dubec <jakub.dubec@gmail.com>
 */

namespace phpGPX\Parsers;

use phpGPX\Models\Extensions;
use phpGPX\Models\Extensions\TrackPointExtension;
use phpGPX\Parsers\Extensions\TrackPointExtensionParser;

/**
 * Class ExtensionParser
 * @package phpGPX\Parsers
 */
abstract class ExtensionParser
{
	public static $tagName = 'extensions';

	public static $usedNamespaces = [];

	/**
	 * @param \SimpleXMLElement $nodes
	 * @return Extensions
	 */
	public static function parse($nodes)
	{
		$extensions = new Extensions();

		$nodeNamespaces = $nodes->getNamespaces(true);

		foreach ($nodeNamespaces as $key => $namespace) {
			switch ($namespace) {
				case TrackPointExtension::EXTENSION_NAMESPACE:
				case TrackPointExtension::EXTENSION_V1_NAMESPACE:
					$extension = $nodes->children($namespace)->{TrackPointExtension::EXTENSION_NAME};
					if (!empty($extension)) {
						$extensions->elements['trackpoint'] = TrackPointExtensionParser::parse($extension);
					}
					break;
				default:
					foreach ($nodes->children() as $key => $value) {
						$extensions->elements[$key] = (string) $value;
					}
			}
		}

		return $extensions;
	}


	/**
	 * @param Extensions $extensions
	 * @param \DOMDocument $document
	 * @return \DOMElement|null
	 */
	public static function toXML(Extensions $extensions, \DOMDocument &$document)
	{
		$node =  $document->createElement(self::$tagName);

		if (!empty($extensions->elements)) {
			foreach ($extensions->elements as $key => $value) {
				if ('trackpoint' === $key) {
					$child = TrackPointExtensionParser::toXML($extensions->trackPointExtension, $document);
				} else {
					$child = $document->createElement($key, $value);
				}

				$node->appendChild($child);
			}
		}

		return $node;
	}
}
