<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Classes\Fixtures\TraitUseDeclarationSniff\Correct;

use BestIt\Sniffs\ClassRegistrationTrait;
use BestIt\Sniffs\SuppressingTrait;

class CorrectClass
{
    use SuppressingTrait { isSniffSuppressed as protected isSniffSuppressed; }
    use ClassRegistrationTrait;
}