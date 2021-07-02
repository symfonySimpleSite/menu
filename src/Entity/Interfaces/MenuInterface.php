<?php

namespace SymfonySimpleSite\Menu\Entity\Interfaces;

use SymfonySimpleSite\NestedSets\Entity\NodeInterface;

interface MenuInterface extends NodeInterface
{
    public function getName(): ?string;

    public function setName(?string $name): self;

    public function getUrl(): ?string;

    public function setUrl(?string $url): self;

    public function getPath(): ?string;

    public function setPath(?string $url): self;

    public function getShortUrl(): ?string;

    public function setShortUrl(?string $shortUrl): void;

    public function getRoute(): ?string;

    public function setRoute(?string $url): self;
}