<?php

namespace App\Sanitizer\Extension\Basic\NodeVisitor;

use HtmlSanitizer\Model\Cursor;
use HtmlSanitizer\Node\NodeInterface;
use HtmlSanitizer\Extension\Basic\Node\ANode;
use HtmlSanitizer\Visitor\AbstractNodeVisitor;
use HtmlSanitizer\Visitor\NamedNodeVisitorInterface;
use HtmlSanitizer\Visitor\HasChildrenNodeVisitorTrait;
use HtmlSanitizer\Extension\Basic\Sanitizer\AHrefSanitizer;

class ANodeVisitor extends AbstractNodeVisitor implements NamedNodeVisitorInterface
{
    use HasChildrenNodeVisitorTrait;

    /**
     * @var AHrefSanitizer
     */
    private $sanitizer;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->sanitizer = new AHrefSanitizer(
            $this->config['allowed_hosts'],
            $this->config['allow_mailto'],
            $this->config['force_https']
        );
    }

    protected function getDomNodeName(): string
    {
        return 'a';
    }

    public function getDefaultAllowedAttributes(): array
    {
        return ['href', 'title'];
    }

    public function getDefaultConfiguration(): array
    {
        return [
            'allowed_hosts' => null,
            'allow_mailto' => true,
            'force_https' => false,
        ];
    }

    protected function createNode(\DOMNode $domNode, Cursor $cursor): NodeInterface
    {
        $node = new ANode($cursor->node);
        $node->setAttribute('href', $this->sanitizer->sanitize($this->getAttribute($domNode, 'href')));
        $node->setAttribute('target', '_blank');
        return $node;
    }
}
