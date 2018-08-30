<?php

namespace Olveneer\TwigComponentsBundle\Service;


class SlotsResolver
{

    private $requiredElements;

    /**
     * @param $html
     * @throws ElementMismatchException
     */
    public function configure($html)
    {
        $pattern = '/<(?<tag>\w+)(?<attribute>(?:\s+\w+(?:\s*=\s*(?:(?:"[^"]*")|(?:\'[^\']*\')|[^>\s]+))?)*)\s*(\/?)>/';

        $matches = [];
        preg_match_all($pattern, $html, $matches);

        $tags = $matches['tag'];
        $attributes = $matches['attribute'];

        foreach ($this->requiredElements as $tag => $options) {
            $wantedAttributes = $options['attributes'];
            $amount = $options['amount'];

            $found = array_keys($tags, $tag);

            if ($wantedAttributes) {
                foreach ($found as $key => $val) {
                    $attribute = $attributes[$key];

                    if (!$attribute) {
                        unset($found[$key]);
                    } else {
                        $attributesParts = explode(' ', $attribute);

                        foreach($attributesParts as $part) {
                            if (!$part) {
                                continue;
                            }

                            $parts = explode('=', $part);

                            $foundAttribute = false;
                            for ($i = 0; $i < count($parts); $i++) {
                                $attrKey = $parts[0];
                                $attrValue =  substr($parts[1], 1, -1);

                                if (isset($wantedAttributes[$attrKey])) {
                                    $wantedValue = $wantedAttributes[$attrKey];

                                    if (is_callable($wantedValue)) {
                                        $check = $wantedValue($attrValue);
                                    } else {
                                        $check = ($wantedAttributes[$attrKey] === $attrValue || $wantedAttributes[$attrKey] === 'any');
                                    }

                                    if ($check) {
                                        $foundAttribute = true;
                                        break;
                                    }
                                }
                                // check if the attribute values match or if the wanted attribute is ''.

                                $i++;
                            }

                            if (!$foundAttribute) {
                                unset($found[$key]);
                            } else {
                                break;
                            }
                        }
                    }
                }
            }

            if (count($found) < $amount) {
                $attributesJson = json_encode($wantedAttributes);

                $pluralTags = 'tag';
                if ($amount > 1) {
                    $pluralTags = 'tag';
                }

                throw new ElementMismatchException("Slot content does not match the element requirement of $amount $tag $pluralTags having the $attributesJson attributes");
            }
        }
    }

    public function requireElement($tag, $amount = 1, $attributes = [])
    {
        $this->requiredElements[$tag] = ['attributes' => $attributes, 'amount' => $amount];
    }
}