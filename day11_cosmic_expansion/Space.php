<?php namespace day11_cosmic_expansion;
use drupol\phpermutations\Iterators\Combinations;
use Tightenco\Collect\Support\Collection;

class Space
{
    public Collection $galaxies;
    public array $map = [];
    public array $map_r = [];

    public function __construct()
    {
        $this->galaxies = collect();
    }

    public function distances(int $expansion_factor) : int
    {
        $sum = 0;
        $galaxies = collect();
        [$empty_rows, $empty_cols] = $this->find_empty_space();

        foreach($this->galaxies as $g) {
            $g = clone($g);
            $g->x += ($expansion_factor * $empty_cols->filter(fn($r) => $r < $g->x)->count());
            $g->y += ($expansion_factor * $empty_rows->filter(fn($r) => $r < $g->y)->count());
            $galaxies->push($g);
        }
        while($galaxies->count() > 0) {
            $g1 = $galaxies->shift();
            foreach($galaxies as $g2) {
                $sum += $g1->manhattan($g2);
            }
        }
        return $sum;
    }

    public function find_empty_space() : array
    {
        return [
            collect($this->map)->map(fn($m) => collect($m)->filter(fn($x) => $x === '#')->count())->filter(fn($x)=>$x === 0)->keys(),
            collect($this->map_r)->map(fn($m) => collect($m)->filter(fn($x) => $x === '#')->count())->filter(fn($x)=>$x === 0)->keys()
        ];
    }
}
