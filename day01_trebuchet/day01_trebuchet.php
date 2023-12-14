<?php namespace day01_trebuchet;
use Lib\solver;

class day01_trebuchet extends solver
{
    private array $digits = ['one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 6, 'seven' => '7', 'eight' => 8, 'nine' => 9];

    public function solve() : void
    {
        $sum = $this->input->map(fn($l) => preg_replace("~\D~", '', $l))->map(fn($c) => (int)$c[0] . $c[-1])->sum();
        $this->solution('1a', $sum);

        $sum = $this->input->map(function($line) {
            preg_match('/^.*(one|two|three|four|five|six|seven|eight|nine|\d)/U', $line, $matches);
            $first = is_numeric($matches[1]) ? $matches[1] : $this->digits[$matches[1]];

            preg_match('/^.*(one|two|three|four|five|six|seven|eight|nine|\d).*$/', $line, $matches);
            $last = is_numeric($matches[1]) ? $matches[1] : $this->digits[$matches[1]];

            return (int)"{$first}{$last}";
        })->sum();

        $this->solution('1b', $sum);
    }
 }
