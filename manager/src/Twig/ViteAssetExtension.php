<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ViteAssetExtension extends AbstractExtension
{
    private array $manifest;

    public function __construct(KernelInterface $kernel)
    {
        $manifestPath = $kernel->getProjectDir() . '/public/build/manifest.json';
        if (file_exists($manifestPath)) {
            $this->manifest = json_decode(file_get_contents($manifestPath), true);
        } else {
            $this->manifest = [];
        }
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite_asset', [$this, 'getAssetPath']),
        ];
    }

    public function getAssetPath(string $entry): ?string
    {
        return $this->manifest[$entry]['file'] ?? null;
    }
}
