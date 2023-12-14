<?php namespace day05_if_you_give_a_seed_a_fertilizer;

class Mapper
{
    public function __construct(
        public int $source_start,
        public int $source_end,
        public int $destination,
        public int $length,
        public int $diff
    ) {}
}
