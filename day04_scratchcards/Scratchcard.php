<?php namespace day04_scratchcards;

use Illuminate\Support\Collection;

class Scratchcard
{
    public function __construct(
        public Collection $winning,
        public Collection $numbers,
        public int $count=1
    ) {}

    public function score() : int
    {
       $winners = $this->winners()->count();
       return $winners === 0 ? 0 : 1 << $winners-1;
    }

    public function winners() : Collection
    {
        return $this->winning->intersect($this->numbers);
    }

    public function add($count = 1) : void
    {
        $this->count+=$count;
    }
}
