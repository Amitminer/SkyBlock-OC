<?php
declare(strict_types=1);

namespace Vecnavium\SkyBlocksPM\config\database;

use Vecnavium\SkyBlocksPM\libs\libMarshal\attributes\Field;
use Vecnavium\SkyBlocksPM\libs\libMarshal\MarshalTrait;

class SQLiteConfig{
    use MarshalTrait;

    #[Field]
    public string $file = 'players.sqlite';
}