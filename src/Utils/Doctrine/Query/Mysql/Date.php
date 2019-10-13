<?php declare(strict_types = 1);

namespace App\Utils\Doctrine\Query\Mysql;

use DateTime;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

class Date extends FunctionNode
{
    /**
     * @var DateTime
     */
    public $date;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'DATE(' . $sqlWalker->walkArithmeticPrimary($this->date) . ')';
    }

    /**
     * @param Parser $parser
     *
     * @throws QueryException
     */
    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->date = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
