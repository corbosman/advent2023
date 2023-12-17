<?php namespace day17_clumsy_crucible;
use Lib\Heap;
use Lib\solver;
use Illuminate\Support\Collection;

class day17_clumsy_crucible extends solver
{
    public function solve() : void
    {
        $city = $this->parse_input();
        $this->solution('17a', $this->route($city, 0, 0, 3, 0));
        $this->solution('17b', $this->route($city, 0, 0, 10, 4));
    }

    /* this is sort of a dijkstra but the direction and steps are part of the decision if we've seen a previous direction */
    public function route(Collection $city, int $sx, int $sy, $max_blocks_without_turning = 3, $min_blocks_after_turning = 0) : int
    {
        $w = count($city[0]);
        $h = count($city);
        $q = new Heap;
        $q->insert([0, $sx, $sy, -1, -1, 0, 1, 0], INFINITE);
        $seen = [];

        while($q->count() > 0) {
            [$heat_loss, $x, $y, $px, $py, $dx, $dy, $steps] = $q->extract();

            if ($x === $w-1 && $y === $h -1) {
                if ($steps >= $min_blocks_after_turning) return $heat_loss;
            }

            /* we have already done this position in this direction with this amount of steps */
            $key = "{$x}_{$y}_{$dx}_{$dy}_{$steps}";
            if (isset($seen[$key])) continue;

            foreach([[0,-1],[1,0],[0,1],[-1,0]] as [$ndx, $ndy]) {
                $nx = $x+$ndx;
                $ny = $y+$ndy;

                if (out_of_bounds($nx, $ny, $w, $h)) continue;

                /* dont go back to where we came from */
                if ($x+$ndx === $px && $y+$ndy === $py) continue;

                $next_heat_loss = $heat_loss + $city[$ny][$nx];

                /* heading in the same direction */
                if ($dx === $ndx && $dy ===  $ndy) {
                    /* check if we have enough steps left in that direction */
                    if  ($steps < $max_blocks_without_turning) {
                        $q->insert([$next_heat_loss, $nx, $ny, $x, $y, $dx, $dy, $steps+1], $next_heat_loss);
                    }
                } else {
                    /* if we're close enough to a turn (or we're at [0,0) */
                    if ($steps >= $min_blocks_after_turning || [$x,$y] === [0,0]) {
                        $q->insert([$next_heat_loss, $nx, $ny, $x, $y, $ndx, $ndy, 1], $next_heat_loss);
                    }
                }
            }
            $seen[$key] = 1;
        }
    }

    public function parse_input() : Collection
    {
        return $this->input->map(fn($m) => collect(str_split($m)));
    }
}
