<?php
declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuItem extends AbstractDto
{

    protected string $url;
    protected string $title;
    protected bool   $active  = false;
    protected bool   $open    = false;
    protected array  $subMenu = [];

    public function __construct(
        string $url,
        string $title,
        bool $active = false,
        bool $open = false,
        array $subMenu = [],
        bool $ignoreExtra = false
    ) {
        parent::__construct(
            [
                'url'     => $url,
                'title'   => $title,
                'active'  => $active,
                'open'    => $open,
                'subMenu' => $subMenu
            ],
            $ignoreExtra
        );
    }

    public function hasSubMenu(): bool
    {
        return !empty($this->subMenu);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(['url', 'title', 'subMenu']);
        $resolver->setAllowedTypes('url', ['string']);
        $resolver->setAllowedTypes('title', ['string']);

        $resolver->setAllowedTypes('subMenu', ['array']);
        $resolver->setDefault('subMenu', []);
        $resolver->setAllowedValues(
            'subMenu',
            function (array $subMenu) {
                foreach ($subMenu as $menuItem) {
                    if (!$menuItem instanceof MenuItem) {
                        return false;
                    }
                }
                return true;
            }
        );
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isOpen(): bool
    {
        return $this->open;
    }

    public function getSubMenu(): array
    {
        return $this->subMenu;
    }

}
