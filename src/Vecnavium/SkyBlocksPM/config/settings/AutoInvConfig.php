<?php
declare(strict_types=1);

namespace Vecnavium\SkyBlocksPM\config\settings;

use Vecnavium\SkyBlocksPM\libs\libMarshal\attributes\Field;
use Vecnavium\SkyBlocksPM\libs\libMarshal\MarshalTrait;

class AutoInvConfig{
    use MarshalTrait;

    #[Field]
    public bool $enabled = true;
    #[Field(name: 'drop-when-full')]
    public bool $dropWhenFull = true;
}