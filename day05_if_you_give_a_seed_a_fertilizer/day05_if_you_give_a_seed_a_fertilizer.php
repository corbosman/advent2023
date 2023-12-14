<?php namespace day05_if_you_give_a_seed_a_fertilizer;
use Lib\solver;
use Illuminate\Support\Collection;

class day05_if_you_give_a_seed_a_fertilizer extends solver
{
    public function solve() : void
    {
        [$seeds, $seedmaps] = $this->parse_input();
        $this->solution('5a', $this->part1($seeds, $seedmaps));
        $this->solution('5b', $this->part2($seeds, $seedmaps));
    }

    public function part1(Collection $seeds, Collection $seedmaps) : int
    {
        foreach($seedmaps as $seedmap) {
            $seeds = $seeds->map(fn($seed) => $seedmap->convert($seed));
        }
        return $seeds->min();
    }

    public function part2(Collection $seeds, Collection $seedmaps) : int
    {
        /* convert seeds to ranges */
        $seeds = $this->seed_ranges($seeds);

        foreach($seedmaps as $seedmap) {
            $seeds = $seeds->map(fn($seed) => $seedmap->convert_range($seed));
        }

        /* find the lowest seed range */
        return $seeds->collapse()->map(fn($seed)=>$seed[0])->min();
    }

    /* returns a collection of seed ranges */
    public function seed_ranges(Collection $seeds) : Collection
    {
        return $seeds->chunk(2)
                     ->map(fn($s) => [(int)$s->first(), (int)$s->first()+$s->last()-1])
                     ->map(fn($s) => collect([$s]));
    }

    public function parse_input() : array
    {
        $input = $this->input->chunkWhile(fn($i) => $i!=="")->map(fn($i) => $i->reject(fn($j) => $j===""));
        $seeds = collect(explode(' ', substr($input->shift()->first(), 7)));
        $seedmaps = $input->map(function($c) {
            $seedmap = new Seedmap(explode(' ', $c->shift())[0]);
            $c->map(fn($m) => explode(' ', $m))
              ->each(fn($m) => $seedmap->add_mapper(new Mapper($m[1], $m[1]+$m[2]-1, $m[0], $m[2], $m[0]-$m[1])));
            return $seedmap;
        });
        return [$seeds, $seedmaps];
    }
}
