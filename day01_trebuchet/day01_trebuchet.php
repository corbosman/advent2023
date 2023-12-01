<?php namespace day01_trebuchet;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day01_trebuchet extends solver
{
    public function solve() : array
    {
        $this->start_timer();

        $digits = ['one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 6, 'seven' => '7', 'eight' => 8, 'nine' => 9];

        $sum = $this->input->map(fn($l) => collect(str_split($l))->filter(fn($c) => is_numeric($c))->implode(''))->map(fn($c) => (int)$c[0] . $c[-1])->sum();
        $this->solution('1a', $sum);

        $sum = $this->input->map(function($line) use ($digits) {
            preg_match('/^.*(one|two|three|four|five|six|seven|eight|nine|\d)/U', $line, $matches);
            $first = is_numeric($matches[1]) ? $matches[1] : $digits[$matches[1]];

            preg_match('/^.*(one|two|three|four|five|six|seven|eight|nine|\d).*$/', $line, $matches);
            $last = is_numeric($matches[1]) ? $matches[1] : $digits[$matches[1]];

            return $first.$last;
        })->map(fn($i) => (int)$i)->sum();

        $this->solution('1b', $sum);

        return $this->solutions;
    }
 }
