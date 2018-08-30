<?php

namespace Olveneer\TwigComponentsBundle\Twig\tag;

/**
 * Class SlotTokenParser
 * @package Olveneer\TwigComponentsBundle\Slot
 */
class CollectParser extends \Twig_TokenParser
{
    /**
     * @var string
     */
    private $endTag = 'endcollect';


    /**
     * @param \Twig_Token $token
     * @return SlotNode|\Twig_Node
     * @throws \Twig_Error_Syntax
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();

        $stream = $this->parser->getStream();

        // recovers all inline parameters close to your tag name
        $params = array_merge([], $this->getInlineParams($token));

        $continue = true;
        while ($continue)
        {
            // create subtree until the decideSlotEnd() callback returns true
            $body = $this->parser->subparse([$this, 'decideCollectEnd']);

            $tag = $stream->next()->getValue();

            switch ($tag)
            {
                case $this->endTag:
                    $continue = false;
                    break;
                default:
                    throw new \Twig_Error_Syntax(sprintf("Unexpected end of template. Twig was looking for the following tag '$this->endTag' to close the '$this->endTag' block started at line %d)", $lineno), -1);
            }

            // you want $body at the beginning of your arguments
            array_unshift($params, $body);

            // if the endtag can also contains params, you can uncomment this line:
            // $params = array_merge($params, $this->getInlineParams($token));
            // and comment this one:
            $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        }

        return new CollectNode(new \Twig_Node($params), $lineno, $this->getTag());
    }

    /**
     * Recovers all tag parameters until we find a BLOCK_END_TYPE ( %} )
     *
     * @param \Twig_Token $token
     * @return array
     * @throws \Twig_Error_Syntax
     */
    protected function getInlineParams(\Twig_Token $token)
    {
        $stream = $this->parser->getStream();
        $params = [];
        while (!$stream->test(\Twig_Token::BLOCK_END_TYPE))
        {
            $params[] = $this->parser->getExpressionParser()->parseExpression();
        }
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        return $params;
    }

    /**
     * Callback called at each tag name when subparsing, must return
     * true when the expected end tag is reached.
     *
     * @param \Twig_Token $token
     * @return bool
     */
    public function decideCollectEnd(\Twig_Token $token)
    {
        return $token->test([$this->endTag]);
    }

    /**
     * slot: if the parsed tag match the one you put here, your parse()
     * method will be called.
     *
     * @return string
     */
    public function getTag()
    {
        return 'collect';
    }
}