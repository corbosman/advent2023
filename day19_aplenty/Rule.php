<?php namespace day19_aplenty;

class Rule
{
    public function __construct(
        public string $category,
        public string $cmp,
        public int $num,
        public string $next
    ) {}
}
