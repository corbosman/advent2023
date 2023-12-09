<?php namespace day09_mirage_maintenance;
use JetBrains\PhpStorm\ArrayShape;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day09_mirage_maintenance extends solver
{
    public function solve() : void
    {
        $input = $this->parse_input();
        $this->solution('9a', $this->part1($input));
        $this->solution('9b', $this->part2($input));
    }

    public function part1(Collection $input) : int
    {
        return $input->map(fn($i) => $this->predict($i))->sum();
    }

    public function part2(Collection $input) : int
    {
        return $input->map(fn($i) => $this->predict($i->reverse()))->sum();
    }

    public function predict(Collection $values) : int
    {
        $differences = $values->sliding(2)->map(fn($v) => $v->last() - $v->first());
        return $differences->sum() === 0 ? $values->last() : $values->last() + $this->predict($differences);
    }

    public function parse_input() : Collection
    {
       return $this->input->map(fn($m) => collect(explode(' ', $m))->map(fn($m) => (int)$m));
    }
 }

