<?php

namespace Olveneer\TwigComponentsBundle\Twig\tags\component;

/**
 * Class SlotTokenParser
 * @package Olveneer\TwigComponentsBundle\Slot
 */
class ComponentParser extends \Twig_TokenParser
{
    /**
     * @var string
     */
    private $endTag = 'endcomponent';

    public function parse(\Twig_Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        list($variables, $only, $ignoreMissing) = $this->parseArguments();

        return new ComponentNode($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }

    /**
     * @return array
     * @throws \Twig_Error_Syntax
     */
    protected function parseArguments()
    {
        $stream = $this->parser->getStream();

        $ignoreMissing = false;
        if ($stream->nextIf(/* Twig_Token::NAME_TYPE */ 5, 'ignore')) {
            $stream->expect(/* Twig_Token::NAME_TYPE */ 5, 'missing');

            $ignoreMissing = true;
        }

        $variables = null;
        if ($stream->nextIf(/* Twig_Token::NAME_TYPE */ 5, 'with')) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
        }

        $only = false;
        if ($stream->nextIf(/* Twig_Token::NAME_TYPE */ 5, 'only')) {
            $only = true;
        }

        $stream->expect(/* Twig_Token::BLOCK_END_TYPE */ 3);

        $continue = true;
        while ($continue)
        {
            // create subtree until the decideSlotEnd() callback returns true
            $body = $this->parser->subparse([$this, 'decideComponentEnd']);

            $tag = $stream->next()->getValue();

            switch ($tag)
            {
                case $this->endTag:
                    $continue = false;
                    break;
                default:
                    throw new \Twig_Error_Syntax(sprintf("Unexpected end of template. Twig was looking for the following tags '$this->endTag' to close the '$this->endTag' block started at line %d)", $lineno), -1);
            }

            // if the endtag can also contains params, you can uncomment this line:
            // $params = array_merge($params, $this->getInlineParams($token));
            // and comment this one:
            $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        }

        return array($variables, $only, $ignoreMissing);
    }

    public function getTag()
    {
        return 'component';
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
}