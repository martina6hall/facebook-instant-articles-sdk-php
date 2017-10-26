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
 * An audio within for the article.
 * Also consider to use one of the other media types for an article:
 * <ul>
 *     <li>Image</li>
 *     <li>Video</li>
 *     <li>SlideShow</li>
 *     <li>Map</li>
 * </ul>.
 *
 * Example:
 *    <audio title="audio title">
 *        <source src="http://foo.com/mp3">
 *    </audio>
 *
 * @see Image
 * @see Video
 * @see SlideShow
 * @see Map
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/audio}
 */
class Audio extends Element
{
    /**
     * @var string The audio title
     */
    private string $title = "";

    /**
     * @var string The string url for the audio file
     */
    private string $url = "";

    /**
     * @var string Can be set with: empty ("") (Default), "muted" or "autoplay"
     */
    private string $playback = "";

    /**
     * @var boolean stores the usage or not of autoplay for audio
     */
    private bool $autoplay = false;

    /**
     * @var boolean stores status of muted for this audio
     */
    private bool $muted = false;

    private function __construct()
    {
    }

    /**
     * @return Audio
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Sets the URL for the audio. It is REQUIRED.
     *
     * @param string $url The url of image. Ie: http://domain.com/audiofile.mp3
     *
     * @return $this
     */
    public function withURL(string $url): Audio
    {
        $this->url = $url;

        return $this;
    }

    /**
     * The audio title.
     *
     * @param string $title the audio title that will be shown
     *
     * @return $this
     */
    public function withTitle(string $title): Audio
    {
        $this->title = $title;

        return $this;
    }

    /**
     * It will make audio start automatically.
     *
     * @return $this
     */
    public function enableAutoplay(): Audio
    {
        $this->autoplay = true;

        return $this;
    }

    /**
     * It will make audio *NOT* start automatically.
     *
     * @return $this
     */
    public function disableAutoplay(): Audio
    {
        $this->autoplay = false;

        return $this;
    }

    /**
     * It will make audio be muted initially.
     *
     * @return $this
     */
    public function enableMuted(): Audio
    {
        $this->muted = true;

        return $this;
    }

    /**
     * It will make audio laud.
     *
     * @return $this
     */
    public function disableMuted(): Audio
    {
        $this->muted = false;

        return $this;
    }

    /**
     * Gets the audio title
     *
     * @return string Audio title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Gets the url for the audio
     *
     * @return string Audio url
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Gets the playback definition
     *
     * @return string playback definition
     */
    public function getPlayback(): string
    {
        return $this->playback;
    }

    /**
     * Structure and create the full ArticleImage in a XML format DOMNode.
     *
     * @param \DOMDocument $document where this element will be appended. Optional
     *
     * @return \DOMNode
     */
    public function toDOMElement(\DOMDocument $document): \DOMNode
    {
        if (!$this->isValid()) {
            return $this->emptyElement($document);
        }

        $element = $document->createElement('audio');

        // title markup optional
        if ($this->title) {
            $element->setAttribute('title', $this->title);
        }

        // Autoplay mode markup optional
        if ($this->autoplay) {
            $element->setAttribute('autoplay', 'autoplay');
        }

        // Autoplay mode markup optional
        if ($this->muted) {
            $element->setAttribute('muted', 'muted');
        }

        // Audio URL markup. REQUIRED
        if ($this->url) {
            $source_element = $document->createElement('source');
            $source_element->setAttribute('src', $this->url);
            $element->appendChild($source_element);
        }

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid Audio that contains not empty url, false otherwise.
     */
    public function isValid(): bool
    {
        return !Type::isTextEmpty($this->url);
    }
}
