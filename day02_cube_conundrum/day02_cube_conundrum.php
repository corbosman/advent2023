<?php namespace day02_cube_conundrum;
use Lib\solver;
use Illuminate\Support\Collection;

class day02_cube_conundrum extends solver
{
    public const MAX_RED = 12;
    public const MAX_GREEN = 13;
    public const MAX_BLUE = 14;

    public function solve() : void
    {
        $this->solution('2a', $this->part1($this->input));
        $this->solution('2b', $this->part2($this->input));
    }

    public function part1(Collection $input) : int
    {
        return $input->map(function ($game, $i) {
            preg_match_all('/(\d+ (red|green|blue))/', $game, $matches);
            foreach ($matches[1] as $g) {
                [$num, $color] = explode(' ', $g);
                if ($color === 'red'   && (int)$num > self::MAX_RED)   return 0;
                if ($color === 'green' && (int)$num > self::MAX_GREEN) return 0;
                if ($color === 'blue'  && (int)$num > self::MAX_BLUE)  return 0;
            }
            return $i + 1;
        })->sum();
    }

    public function part2(Collection $input) : int
    {
        return $input->map(function ($game) {
            $reds = collect(); $greens = collect(); $blues = collect();
            preg_match_all('/(\d+ (red|green|blue))/', $game, $matches);
            foreach ($matches[1] as $g) {
                [$num, $color] = explode(' ', $g);
                match($color) { 'red' => $reds->push($num), 'green' => $greens->push($num), 'blue' => $blues->push($num) };
            }
            return $reds->max() * $greens->max() * $blues->max();
        })->sum();
    }
}
