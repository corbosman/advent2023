<?php namespace day18_lavaduct_lagoon;
class Plan
{
    public function __construct(
        public string $direction,
        public int $distance,
        public string $color
    ) {}
}
