<?php declare(strict_types=1);

namespace System;

class Route
{
    public function __construct(
        public readonly string $method,
        public readonly string $uri,
        public readonly string $controller,
        public readonly string $action,
        public readonly string $pattern
    ) {}
}