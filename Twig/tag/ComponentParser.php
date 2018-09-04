<?php

namespace Olveneer\TwigComponentsBundle\Twig\tag;

/**
 * Class SlotTokenParser
 * @package Olveneer\TwigComponentsBundle\Slot
 */
class ComponentParser extends \Twig_TokenParser
{
    /**
     * @var string
     */
    private $endTag = 'endget';

    /**
     * @param \Twig_Token $token
     * @return ComponentNode
     * @throws \Twig_Error_Syntax
     */
    public function parse(\Twig_Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        list($variables, $slotted) = $this->parseArguments();

        return new ComponentNode($expr, $variables, $token->getLine(), $slotted, $this->getTag());
    }

    /**
     * @return array
     * @throws \Twig_Error_Syntax
     */
    protected function parseArguments()
    {
        $stream = $this->parser->getStream();

        $variables = null;

        if ($stream->nextIf(/* Twig_Token::NAME_TYPE */ 5, 'with')) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(/* Twig_Token::BLOCK_END_TYPE */ 3);

        $body = $this->parser->subparse(array($this, 'decideComponentFork'));

        $slotted = [];
        $end = false;
        while (!$end) {
            switch ($stream->next()->getValue()) {
                case 'slot':
                    $name = $stream->getCurrent()->getValue();
                    $stream->expect(\Twig_Token::NAME_TYPE);

                    $stream->expect(/* Twig_Token::BLOCK_END_TYPE */ 3);
                    $slotNodes = $this->parser->subparse(array($this, 'decideComponentFork'));

                    $slotted[$name] = $slotNodes;
                    break;

                case 'endslot':
                    $stream->expect(/* Twig_Token::BLOCK_END_TYPE */ 3);
                    $body = $this->parser->subparse(array($this, 'decideComponentFork'));
                    break;

                case $this->endTag:
                    $end = true;
                    break;

                default:
                    throw new \Twig_Error_Syntax(sprintf('Unexpected end of template. Twig was looking for the following tag "else", "elseif", or "endif" to close the "if" block started at line %d).', $lineno), $stream->getCurrent()->getLine(), $stream->getSourceContext());
            }
        }

        $stream->expect(/* Twig_Token::BLOCK_END_TYPE */ 3);

        return [$variables, $slotted];
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return 'get';
    }

    /**
     * Callback called at each tag name when subparsing, must return
     * true when the expected end tag is reached.
     *
     * @param \Twig_Token $token
     * @return bool
     */
    public function decideComponentEnd(\Twig_Token $token)
    {
        return $token->test([$this->endTag]);
    }

    /**
     * Callback called at each tag name when subparsing, must return
     * true when the expected end tag is reached.
     *
     * @param \Twig_Token $token
     * @return bool
     */
    public function decideComponentFork(\Twig_Token $token)
    {
        return $token->test(['slot', 'endslot', $this->endTag]);
    }
}
