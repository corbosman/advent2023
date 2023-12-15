<?php namespace day15_lens_library;
use Ds\Map;
use Lib\solver;
use Illuminate\Support\Collection;

class day15_lens_library extends solver
{
    public function solve() : void
    {
        $sequence = $this->parse_input();
        $this->solution('15a', $this->part1($sequence));
        $this->solution('15b', $this->part2($sequence));
    }

    public function part1(Collection $sequence) : int
    {
        return $sequence->map(fn($hash) => $this->hash($hash))->sum();
    }

    public function part2(Collection $sequence) : int
    {
        $boxes = $this->boxes();

        foreach($sequence as $s) {
            preg_match('/^([a-z]+)([=\-])(.*)$/', $s, $matches);

            $label        = $matches[1];
            $box          = $this->hash($label);
            $operation    = $matches[2];
            $focal_length = $matches[3] ?? null;

            switch($operation) {
                case '-':
                    if ($boxes[$box]->hasKey($label)) $boxes[$box]->remove($label);
                    break;
                case '=':
                    $boxes[$box][$label] = $focal_length;
            }
        }
        return $this->focussing_power($boxes);
    }

    public function focussing_power(Collection $boxes) : int
    {
        return $boxes->reduce(function($carry, $box, $box_number) {
            [$_, $box_sum] = $box->values()->reduce(fn($carry, $focal_length) => [$carry[0]+1, $carry[1] + ($carry[0] * $focal_length)],[1,0]);
            return $carry + (($box_number+1) * $box_sum);
        },0);
    }

    public function hash(string $label) : string
    {
        return collect(str_split($label))->reduce(fn($result, $c) => (($result + ord($c))*17) % 256 ,0);
    }

    public function boxes() : Collection
    {
        $boxes = collect();
        for ($i = 0; $i < 256; $i++) $boxes[] = new Map;
        return $boxes;
    }

    public function parse_input() : Collection
    {
        return collect(explode(',',$this->input[0]));
    }
 }
