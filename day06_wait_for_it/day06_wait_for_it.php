<?php namespace day06_wait_for_it;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day06_wait_for_it extends solver
{
    public function solve() : void
    {
        [$races1, $races2] = $this->parse_input();
        $this->solution('6a', $this->part1($races1));
        $this->solution('6b', $this->part2($races2));
    }

    public function part1(Collection $races) : int
    {
        $all = collect();
        foreach($races as [$time, $distance]) {
            $wins = collect();
            for($hold=1; $hold<$time-1; $hold++) {
                $race_distance = $this->distance($time, $hold);
                if ($race_distance > $distance) $wins->push($hold);
            }
            $all->push($wins);
        }
        return $all->reduce(fn($c, $i) => $c*$i->count(), 1);
    }

    public function part2(Collection $races) : int
    {
        [$time, $distance] = $races->first();
        $winning_race = $this->find_winning_race($time, floor($time/2), $distance);

        $left_edge  = $this->find_edge($time, (int)floor($winning_race/2), 0, $winning_race, $distance, 'left', false);
        $right_edge = $this->find_edge($time, (int)floor($winning_race + ($winning_race/2)), $winning_race, $time, $distance, 'right', false);
        return $right_edge - $left_edge + 1;
    }

    public function find_winning_race(int $time, int $hold, int $distance) : int
    {
        if ($hold === 0 || $hold === $time) return 0;
        if ($this->distance($time, $hold) > $distance) return $hold;

        return max($this->find_winning_race($time, floor($hold/2), $distance), $this->find_winning_race($time, floor($hold + ($hold/2)), $distance));
    }

    public function find_edge(int $time, int $hold, int $min, int $max, int $distance, string $dir, bool $state) : int
    {
        // check if we're at an edge by seeing if any of the values next to us are different
        $winning      = $this->distance($time, $hold)   > $distance;
        $winning_prev = $this->distance($time, $hold-1) > $distance;
        $winning_next = $this->distance($time, $hold+1) > $distance;

        /* found an edge! */
        if ($winning !== $winning_prev) return $winning ? $hold : $hold-1;
        if ($winning !== $winning_next) return $winning ? $hold : $hold+1;

        /* state changed, reverse state check */
        if ($winning === $state) {
            $dir = $dir === 'left' ? 'right' : 'left';
            $state = !$state;
        }

        if ($dir === 'left') $max = $hold;
        if ($dir === 'right') $min = $hold;

        return $this->find_edge($time,(int)floor(($max+$min)/2), $min, $max, $distance, $dir , $state);
    }

    public function distance(int $time, int $hold) : int
    {
        return $hold * ($time-$hold);
    }

    public function parse_input() : array
    {
        $times     = collect(preg_split('/\s+/', trim(substr($this->input[0], 9))))->map(fn($i) => (int)$i);
        $distances = collect(preg_split('/\s+/', trim(substr($this->input[1], 9))))->map(fn($i) => (int)$i);
        $single_time = $times->reduce(fn($c,$i) => $c . (string)$i,'');
        $single_distance = $distances->reduce(fn($c,$i) => $c . (string)$i,'');
        return [$times->zip($distances), collect([[$single_time, $single_distance]])];
    }
 }
