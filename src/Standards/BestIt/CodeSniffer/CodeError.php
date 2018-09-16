<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer;

/**
 * The warning of a code does not conform to the style guide.
 *
 * We use the exception code as a string like the PDOException does, not as an integer.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer
 */
class CodeError extends CodeWarning
{

}
