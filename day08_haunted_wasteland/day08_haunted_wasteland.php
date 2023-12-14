<?php namespace day08_haunted_wasteland;
use Lib\solver;
use Illuminate\Support\Collection;

class day08_haunted_wasteland extends solver
{
    public function solve() : void
    {
        [$instructions, $maps] = $this->parse_input();
        $this->solution('8a', $this->part1($instructions, $maps));
        $this->solution('8b', $this->part2($instructions, $maps));
    }

    public function part1(array $instructions, Collection $maps) : int
    {
        $size = count($instructions);
        $counter = 0;
        $index = 'AAA';

        while($index !== 'ZZZ') {
            $index = $maps[$index][$instructions[$counter++ % $size]];
        }
        return $counter;
    }

    public function part2(array $instructions, Collection $maps) : int
    {
        $size = count($instructions);
        $steps = collect();

        $a = $maps->keys()->filter(fn($m) => $m[2] === 'A')->values();
        foreach($a as $index) {
            $counter = 0;
            while($index[2] !== 'Z') {
                $index = $maps[$index][$instructions[$counter++ % $size]];
            }
            $steps->push($counter);
        }
        return (int)$steps->reduce(fn($c, $i) => gmp_lcm($c,$i),1);
    }

    public function parse_input() : array
    {
        return [
            array_map(fn($i) => $i==='L' ? 0 : 1, str_split($this->input[0])),
            $this->input->slice(2)->mapWithKeys(fn($m) => [substr($m,0,3) => [substr($m,7,3), substr($m,12,3)]])
        ];
    }
 }
