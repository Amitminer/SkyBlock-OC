<?php

declare(strict_types=1);

namespace Vecnavium\SkyBlocksPM\libs\libMarshal\parser;

/**
 * @template U of mixed
 * @extends Parseable<float, U>
 */
interface FloatParseable extends Parseable {

	/**
	 * @param float $value
	 * @return U
	 */
	public function parse(mixed $value): mixed;

	/**
	 * @param U $value
	 */
	public function serialize(mixed $value): float;
}