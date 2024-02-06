<?php

namespace AcMarche\Sepulture\DoctrineExtensions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
/*
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 9/01/17
 * Time: 11:11
 */
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

class AnyValue extends FunctionNode
{
    public ?Node $value = null; // la valeur  passée en paramètre de la fction ANY_VALUE()

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER); //identifie la fonction ANY_VALUE() de mysql
        $parser->match(TokenType::T_OPEN_PARENTHESIS); //parenthèse ouvrante
        $this->value = $parser->StringPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS); ////parenthèse fermante
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'ANY_VALUE('.$this->value->dispatch($sqlWalker).')';
    }
}
