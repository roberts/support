<?php

declare(strict_types=1);

namespace Roberts\Support\Services;

class FeatureDetector
{
    public function detect(): array
    {
        return [
            'hasTwitter' => $this->hasTwitter(),
            'hasMail' => $this->hasMail(),
            'hasFlux' => $this->hasFlux(),
            'hasFilament' => $this->hasFilament(),
            'hasQueue' => $this->hasQueue(),
            'hasCache' => $this->hasCache(),
            'hasSession' => $this->hasSession(),
        ];
    }

    protected function hasTwitter(): bool
    {
        return $this->envContains('TWITTER_') ||
               $this->composerRequires('atymic/twitter');
    }

    protected function hasMail(): bool
    {
        return $this->envContains('MAIL_');
    }

    protected function hasFlux(): bool
    {
        return $this->envContains('FLUX_') ||
               $this->composerRequires('livewire/flux');
    }

    protected function hasFilament(): bool
    {
        return $this->composerRequires('filament/filament');
    }

    protected function hasQueue(): bool
    {
        return $this->envContains('QUEUE_CONNECTION');
    }

    protected function hasCache(): bool
    {
        return $this->envContains('CACHE_');
    }

    protected function hasSession(): bool
    {
        return $this->envContains('SESSION_');
    }

    protected function envContains(string $search): bool
    {
        $envExample = base_path('.env.example');

        if (! file_exists($envExample)) {
            return false;
        }

        return str_contains(file_get_contents($envExample), $search);
    }

    protected function composerRequires(string $package): bool
    {
        $composerPath = base_path('composer.json');

        if (! file_exists($composerPath)) {
            return false;
        }

        $composer = json_decode(file_get_contents($composerPath), true);

        return isset($composer['require'][$package]) ||
               isset($composer['require-dev'][$package]);
    }
}
