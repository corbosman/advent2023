<?php namespace day03_gear_ratios;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day03_gear_ratios extends solver
{
    public array $gears;

    public function solve() : void
    {
        $input = $this->parse_input($this->input);
        $this->solution('3a', $this->find_part_numbers($input)->sum());
        $this->solution('3b', collect($this->gears)->filter(fn($g) => count($g) === 2)->map(fn($x)=>$x[0] * $x[1])->sum());
    }

    public function find_part_numbers(Collection $input) : Collection
    {
        $part_numbers = collect();
        $rows = count($input);
        $cols = count($input[0]);

        for($y=0; $y < $rows; $y++) {
            for($x=0; $x < $cols; $x++) {
                if (!is_numeric($input[$y][$x])) continue;
                [$number, $len, $gears] = $this->read_number($x, $y, $rows, $cols, $input);
                $x += $len;

                $part_numbers->push($number);

                /* this number is part of one or more gears, add it to the gears for part2 */
                foreach($gears as $gear) {
                    $this->gears["{$gear[0]},{$gear[1]}"][] = $number;
                }
            }
        }
        return $part_numbers;
    }

    public function read_number(int $x, int $y, int $rows, int $cols, Collection $input) : array
    {
        $number = collect();
        $is_part_number = false;
        $gears = collect();

        for($i=$x; $i<$cols; $i++) {
            if (!is_numeric($input[$y][$i])) break;
            $number->push($input[$y][$i]);
            [$is_part_number, $gears] = $this->check_for_part($i, $y, $rows, $cols, $input, $is_part_number, $gears);
        }

        return [$is_part_number ? (int)$number->implode('') : null, count($number)-1, $gears->unique()];
    }

    public function check_for_part(int $x, int $y, $rows, $cols, Collection $input, bool $is_part_number, Collection $gears) : array
    {
        $g = collect();

        foreach([[-1,-1],[-1,0],[-1,1],[0,-1],[0,1],[1,-1],[1,0],[1,1]] as [$dy, $dx]) {
            if ($y+$dy<0 || $x+$dx < 0 || $y+$dy>=$rows || $x+$dx >= $cols) continue;
            $c = $input[$y+$dy][$x+$dx];
            if (!is_numeric($c) && $c !== '.') $is_part_number |= true;
            if ($c === '*') $g->push([$y+$dy,$x+$dx]);
        }

        return [$is_part_number, $gears->merge($g)];
    }

    public function parse_input(Collection $input) : Collection
    {
        return $input->map(fn($i) => collect(str_split($i)));
    }
 }
