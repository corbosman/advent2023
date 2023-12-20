<?php namespace day19_aplenty;

class Workflow
{
    public function __construct(
        public array $rules,
        public string $last
    ) {}
}
