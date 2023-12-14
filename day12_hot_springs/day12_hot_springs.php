<?php namespace day12_hot_springs;
use Lib\solver;
use Illuminate\Support\Collection;

class day12_hot_springs extends solver
{
    public function solve() : void
    {
        $fields = $this->parse_input();
        $this->solution('12a', $this->part1($fields));
        $this->solution('12b', $this->part2($fields));
    }

    public function part1(Collection $fields) : int
    {
        return $fields->map(fn($field) => $this->find_arrangements($field[0], $field[1], 0, 0))->sum();
    }

    public function part2(Collection $fields) : int
    {
        return $fields->map(function($field) {
            $field[0] = "{$field[0]}?{$field[0]}?{$field[0]}?{$field[0]}?{$field[0]}";
            $field[1] = array_merge($field[1], $field[1], $field[1], $field[1], $field[1]);
            return $this->find_arrangements($field[0], $field[1], 0, 0);
        })->sum();
    }

    /* springs=#.?###  groups=[1,2,3]  current_group=0 (index in groups) damaged_length=3 (###) */
    public function find_arrangements(string $springs, array $groups, int $current_group, int $damaged_length, array &$cache = []) : int
    {
        /* end of the springs */
        if ($springs === '') {

            /* last char is a #, this finishes a set */
            if ($damaged_length > 0) {
                /* we have too many groups */
                if ($current_group >= count($groups)) return 0;

                /* length of last group matches! */
                if ($damaged_length === $groups[$current_group]) $current_group++;
            }

            /* check if number of groups is the amount we need */
            return count($groups) === $current_group ? 1 : 0;
        }

        $count = 0;
        $key = "{$springs}_{$current_group}_{$damaged_length}";
        if (isset($cache[$key])) return $cache[$key];

        /* we have a damaged spring */
        if ($springs[0] === '#' || $springs[0] === '?') {
            $count = $this->find_arrangements(substr($springs, 1), $groups, $current_group, $damaged_length+ 1, $cache);
        }

        /* operational spring */
        if ($springs[0] === '.' || $springs[0] === '?') {

            /* we were working on a damaged length before, check if we found a match */
            if ($damaged_length > 0) {

                /* the group length matches the wanted group! */
                if (isset($groups[$current_group]) && $damaged_length === $groups[$current_group]) {
                    $count += $this->find_arrangements(substr($springs, 1), $groups, $current_group + 1, 0, $cache);
                }
            } else {
                $count += $this->find_arrangements(substr($springs, 1), $groups, $current_group, 0, $cache);
            }
        }

        $cache[$key] = $count;

        return $count;
    }

    public function parse_input() : Collection
    {
        return $this
            ->input
            ->map(fn($line) => explode(' ', $line))
            ->map(fn($a) => [$a[0], array_map(fn($i) => (int)$i, explode(',', $a[1]))]);
    }
 }
