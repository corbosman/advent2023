<?php namespace day03_gear_ratios;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day03_gear_ratios extends solver
{
    public function solve() : array
    {
        $input = $this->parse_input($this->input);
        $this->solution('3a', $this->part1($input));
        return $this->solutions;
    }

    public function part1(Collection $input) : int
    {
        $numbers = collect();
        $rows = count($input);
        $cols = count($input[0]);

        for($y=0; $y < $rows; $y++) {
            for($x=0; $x < $cols; $x++) {
                if (!is_numeric($input[$y][$x])) continue;
                [$number, $is_part] = $this->read_number($x, $y, $rows, $cols, $input);
                if ($is_part) $numbers->push($number);
                $x += count($number)-1;
            }
        }
        return $numbers->map(fn($n) => (int)$n->implode(''))->sum();
    }

    public function read_number(int $x, int $y, int $rows, int $cols, Collection $input) : array
    {
        $number = collect();
        $is_part_number = false;
        $gears = collect();

        for($i=$x; $i<$cols; $i++) {
            if (!is_numeric($input[$y][$i])) break;
            $number->push($input[$y][$i]);
            if ($is_part_number === false && $this->is_part_number($i, $y, $rows, $cols, $input)) $is_part_number = true;
        }
        return [$number, $is_part_number];
    }

    public function is_part_number(int $x, int $y, $rows, $cols, Collection $input) : bool
    {
        foreach([[-1,-1],[-1,0],[-1,1],[0,-1],[0,1],[1,-1],[1,0],[1,1]] as $d) {
            $dy = $y+$d[0]; $dx = $x+$d[1];
            if ($dy<0 || $dx < 0 || $dy>=$rows || $dx >= $cols) continue;
            $c = $input[$dy][$dx];
            if (!is_numeric($c) && $c !== '.') return true;
        }
        return false;
    }

    public function parse_input(Collection $input) : Collection
    {
        return $input->map(fn($i) => collect(str_split($i)));
    }
 }
