<?php
declare(strict_types=1);

namespace Vecnavium\SkyBlocksPM\config\database;

use Vecnavium\SkyBlocksPM\libs\libMarshal\attributes\Field;
use Vecnavium\SkyBlocksPM\libs\libMarshal\MarshalTrait;

class MySQLConfig{
    use MarshalTrait;
    
    #[Field]
    public string $host = '127.0.0.1';
    #[Field]
    public string $username = 'root';
    #[Field]
    public string $password = '';
    #[Field]
    public string $schema = 'skyblockspm';
    #[Field]
    public int $port = 3306;
}