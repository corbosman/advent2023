<?php namespace day16_the_floor_will_be_lava;
use Ds\Deque;
use Ds\Map;
use Lib\solver;
use Illuminate\Support\Collection;

class day16_the_floor_will_be_lava extends solver
{
    public function solve() : void
    {
        [$floor, $width, $height] = $this->parse_input();
        $this->solution('16a', $this->part1($floor, $width, $height));
        $this->solution('16b', $this->part2($floor, $width, $height));
    }

    public function part1(array $floor, int $width, int $height) : int
    {
        return $this->fire_beam($floor, new Beam(0, 0, 1, 0), $width, $height);
    }

    public function part2(array $floor, int $width, int $height) : int
    {
        $max = 0;
        for($x=0; $x<$width;$x++) {
            $max = max($max, $this->fire_beam($floor, new Beam($x, 0, 0, 1), $width, $height));
            $max = max($max, $this->fire_beam($floor, new Beam($x, $height-1, 0, -1), $width, $height));
        }
        for($y=0; $y<$height; $y++) {
            $max = max($max, $this->fire_beam($floor, new Beam(0, $y, 1, 0), $width, $height));
            $max = max($max, $this->fire_beam($floor, new Beam($width-1, $y, -1, 0), $width, $height));
        }
        return $max;
    }

    public function fire_beam(array $floor, Beam $beam, int $width, int $height) : int
    {
        $seen = [];
        $beams = new Deque;
        $beams->push($beam);

        while($beams->count() > 0) {
            $beam = $beams->pop();

            /* hit the wall */
            if ($beam->x < 0 || $beam->y < 0 || $beam->x >= $width || $beam->y >= $height) continue;

            /* prevent loops */
            if (isset($seen[$beam->y][$beam->x]) && $seen[$beam->y][$beam->x] === "{$beam->dx}{$beam->dy}") continue;
            $seen[$beam->y][$beam->x] = "{$beam->dx}{$beam->dy}";

            $position = $floor[$beam->y][$beam->x];
            if ($position === '.' || $beam->pass_through($position)) {
                $beams->push($beam->next());
            }
            elseif ($position === '\\' || $position === '/') {
                $beams->push($beam->deflect($position));
            }
            elseif ($position === '-' || $position === '|') {
                [$beam1, $beam2] = $beam->split($position);
                $beams->push($beam1);
                $beams->push($beam2);
            }
        }
        return array_sum(array_map(fn($row) => count($row), $seen));
    }

    public function parse_input(): array
    {
        $floor = $this->input->map(fn($m) => str_split($m))->toArray();
        return [$floor, count($floor[0]), count($floor)];
    }
 }

