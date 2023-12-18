<?php namespace day18_lavaduct_lagoon;
use JetBrains\PhpStorm\ArrayShape;
use Lib\solver;
use Illuminate\Support\Collection;

class day18_lavaduct_lagoon extends solver
{
    public array $dir = ['U' => [0,-1], 'R' => [1,0], 'D' => [0,1], 'L' => [-1,0]];

    public function solve() : void
    {
        $plan = $this->parse_input();
        $this->solution('18a', $this->part1($plan));
        $this->solution('18b', $this->part2($plan));
    }

    public function part1(Collection $plan) : int
    {
        $distance_around_edge = 0;
        $points = collect([[0,0]]);
        foreach($plan as $i => $p) {
             $points[] = [
                $points->last()[0] + ($this->dir[$p->direction][0] * $p->distance),
                $points->last()[1] + ($this->dir[$p->direction][1] * $p->distance)
            ];
            $distance_around_edge += $p->distance;
        }
        return $this->shoelace_pick($points, $distance_around_edge);
    }

    public function part2(Collection $plan) : int
    {
        $dirs = ['R', 'D', 'L', 'U'];
        $distance_around_edge = 0;
        $points = collect([[0,0]]);

        foreach($plan as $i => $p) {
            $distance = hexdec(substr(ltrim($p->color, '#'), 0, 5));
            $direction = $dirs[substr($p->color, -1, 1)];
            $points[] = [
                $points->last()[0] + ($this->dir[$direction][0] * $distance),
                $points->last()[1] + ($this->dir[$direction][1] * $distance)
            ];
            $distance_around_edge += $distance;
        }
        return $this->shoelace_pick($points, $distance_around_edge);
    }

    public function shoelace_pick(Collection $points, int $distance_around_edge) : int
    {
        $n = count($points);
        $area = 0;

        /* calculate area */
        for ($i = 0; $i < $n; $i++) {
            [$x1, $y1] = [$points[$i][0],  $points[$i][1]];
            [$x2, $y2] = [$points[($i + 1) % $n][0], $points[($i + 1) % $n][1]];
            $area += ($x1 * $y2) - ($x2 * $y1);
        }
        $area = abs($area) / 2;

        /* pick's formula */
        $interior_points = $area - ($distance_around_edge / 2) + 1;

        return $interior_points + $distance_around_edge;
    }

    public function parse_input() : Collection
    {
       return $this->input
           ->map(fn($m) => explode(' ', $m))
           ->map(fn($m) => new Plan($m[0], (int)$m[1], trim($m[2], '()')));
    }
 }

