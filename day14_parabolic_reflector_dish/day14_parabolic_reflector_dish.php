<?php namespace day14_parabolic_reflector_dish;
use Lib\solver;

class day14_parabolic_reflector_dish extends solver
{
    public array $cache;
    public array $platform_cache;
    public const CYCLES = 1_000_000_000;

    public function solve() : void
    {
        $platform = $this->parse_input();
        $this->solution('14a', $this->part1($platform, count($platform[0]), count($platform)));
        $this->solution('14b', $this->part2($platform, count($platform[0]), count($platform)));
    }

    public function part1(array $platform, int $width, int $height) : int
    {
        return $this->load($this->tilt($platform,$width, $height));
    }

    public function part2(array $platform, int $width, int $height) : int
    {
        $count = 0;
        /* loop until we see a repetition */
        while(true) {
            $count++;
            $hash = $this->hash($platform);
            if (isset($this->cache[$hash])) break;
            $platform = $this->cycle($platform, $width, $height);
            $this->cache[$hash] = $count;
            $this->platform_cache[$count] = $platform;
        }
        /* now we can jump forwards */
        $index = $this->cache[$hash];
        $mod_index = $index + (self::CYCLES - $index) % ($count - $index);

        return $this->load($this->platform_cache[$mod_index]);
    }

    public function cycle(array $platform, int $width, int $height) : array
    {
        for($i=0;$i<4;$i++) {
            $platform = $this->tilt($platform,$width, $height);
            $platform = $this->rotate($platform);
        }
        return $platform;
    }

    public function rotate(array $platform) : array
    {
        return array_map(null, ...array_reverse($platform));
    }

    public function hash(array $platform) : string
    {
        return implode('',array_map('implode', $platform));
    }

    public function tilt(array $platform, int $width, int $height) : array
    {
        for($y=1; $y<$height; $y++) {
            foreach($platform[$y] as $x => $c) {
                if ($platform[$y][$x] === Rock::ROUND->value) {
                    $rock_y = $y;
                    while($rock_y > 0 && $platform[$rock_y-1][$x] === Rock::NONE->value) {
                        $rock_y--;
                    }
                    if ($rock_y !== $y) {
                        $platform[$y][$x] = Rock::NONE->value;
                        $platform[$rock_y][$x] = Rock::ROUND->value;
                    }
                }
            }
        }
        return $platform;
    }

    public function load(array $platform) : int
    {
        return collect($platform)
            ->reverse()
            ->values()
            ->reduce(fn($carry, $item, $i) => $carry + (($i+1) * collect($item)->filter(fn($i) => $i === Rock::ROUND->value)->count()),0);
    }

    public function parse_input()
    {
        return $this->input->map(fn($m) => str_split($m))->toArray();
    }
}

enum Rock : string
{
    case ROUND = 'O';
    case SQUARE = '#';
    case NONE = '.';
}
