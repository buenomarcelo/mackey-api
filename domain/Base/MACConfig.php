<?php

namespace MAC\Base;

use Illuminate\Contracts\Config\Repository as ConfigContract;

class MACConfig
{
    public function __construct(
        private readonly ConfigContract $config
    ) {
    }

    public function getFrontendUrl(): string
    {
        return $this->config->get('mac.frontend_url');
    }
}
