<?php namespace day11_cosmic_expansion;
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
        $galaxies = clone $this->galaxies;
        [$empty_rows, $empty_cols] = $this->find_empty_space();

        while($galaxies->count() > 0) {
            $g1 = $galaxies->shift();

            foreach($galaxies as $g2) {
                $distance = $g1->distance($g2);
                $expand = 0;

                $from_x = min($g1->x, $g2->x);
                $to_x   = max($g1->x, $g2->x);

                /* expand X */
                foreach($empty_cols as $col) {
                    if ($col > $from_x && $col < $to_x) $expand++;
                }

                $from_y = min($g1->y, $g2->y);
                $to_y   = max($g1->y, $g2->y);

                /* expand Y */
                foreach($empty_rows as $row) {
                    if ($row > $from_y && $row < $to_y) $expand++;
                }

                // $expanded_distance = $distance + $empty;
                $sum += $distance - ($expand) + ($expansion_factor * $expand);
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
