<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

use Facebook\InstantArticles\Validators\Type;

/**
 * Base class for components accepting formatted text. It can contain bold, italic and links.
 *
 * Example:
 * This is a <b>formatted</b> <i>text</i> for <a href="https://foo.com">your article</a>.
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/body-text}
 */
abstract class TextContainer extends Element implements ChildrenContainer
{
    /**
     * @var Vector The content is a list of TextContainer
     */
    private Vector<mixed> $textChildren = Vector {};

    /**
     * Adds content to the formatted text.
     *
     * @param string|TextContainer The content can be a string or a FormattedText.
     *
     * @return $this
     */
    public function appendText(mixed $child): TextContainer
    {
        // TODO Make sure this is string|TextContainer
        $this->textChildren->add($child);
        return $this;
    }

    /**
     * Clears the text.
     */
    public function clearText()
    {
        $this->textChildren = Vector {};
    }

    /**
     * @return Vector<string|TextContainer> All text token for this text container.
     */
    public function getTextChildren(): Vector<mixed>
    {
        return $this->textChildren;
    }

    /**
     * Structure and create the full text in a DOMDocumentFragment.
     *
     * @param \DOMDocument $document - The document where this element will be appended (optional).
     *
     * @return \DOMNode
     */
    public function textToDOMDocumentFragment(\DOMDocument $document): \DOMNode
    {
        $fragment = $document->createDocumentFragment();

        // Generate markup
        foreach ($this->textChildren as $content) {
            if (is_string($content)) {
                $text = $document->createTextNode($content);
                $fragment->appendChild($text);
            } elseif ($content instanceof TextContainer) {
                $fragment->appendChild($content->toDOMElement($document));
            }
        }

        if (!$fragment->hasChildNodes()) {
            $fragment->appendChild($document->createTextNode(''));
        }

        return $fragment;
    }

    /**
     * Build up a string with the content from children text container
     *
     * @return string the unformated plain text content from children
     */
    public function getPlainText(): string
    {
        $text = '';

        // Generate markup
        foreach ($this->textChildren as $content) {
            if (is_string($content)) {
                $text .= $content;
            } elseif ($content instanceof TextContainer) {
                $text .= $content->getPlainText();
            }
        }

        return $text;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid tag, false otherwise.
     */
    public function isValid(): bool
    {
        $textContent = "";

        foreach ($this->textChildren as $content) {
            // Recursive check on TextContainer, if something inside is valid, this is valid.
            if ($content instanceof TextContainer) {
                return $content->isValid();
            // If is string content, concat to check if it is not only a bunch of empty chars.
            } elseif (is_string($content)) {
                $textContent = $textContent.$content;
            }
        }
        return !Type::isTextEmpty($textContent);
    }

    /**
     * Implements the ChildrenContainer::getContainerChildren().
     *
     * @see ChildrenContainer::getContainerChildren().
     * @return array of TextContainer
     */
    public function getContainerChildren(): Vector<Element>
    {
        $children = Vector {};

        foreach ($this->textChildren as $content) {
            if ($content instanceof TextContainer) {
                $children->add($content);
            }
        }

        return $children;
    }
}
