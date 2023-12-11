<?php namespace day11_cosmic_expansion;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day11_cosmic_expansion extends solver
{
    public function solve() : void
    {
        $space = $this->parse_input();
        $this->solution('11a', $space->distances(2));
        $this->solution('11b', $space->distances(1000000));
    }

    public function parse_input() : Space
    {
        $space = new Space;

        foreach($this->input as $y => $row) {
            foreach(str_split($row) as $x => $c) {
                $space->map[$y][$x] = $c;
                $space->map_r[$x][$y] = $c;
                if ($c === '#') $space->galaxies->push(new Galaxy($x, $y));
            }
        }

        return $space;
    }
 }
