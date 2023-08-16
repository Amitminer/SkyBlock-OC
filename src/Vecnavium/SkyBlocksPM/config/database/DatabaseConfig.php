<?php
declare(strict_types=1);

namespace Vecnavium\SkyBlocksPM\config\database;

use Vecnavium\SkyBlocksPM\libs\libMarshal\attributes\Field;
use Vecnavium\SkyBlocksPM\libs\libMarshal\MarshalTrait;

class DatabaseConfig{
    use MarshalTrait;

    #[Field]
    public string $type = 'sqlite';
    #[Field]
    public SQLiteConfig $sqlite;
    #[Field]
    public MySQLConfig $mysql;
    #[Field(name: 'worker-limit')]
    public int $workerLimit = 1;
}