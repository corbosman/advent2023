<?php namespace day04_scratchcards;
use Lib\solver;
use Tightenco\Collect\Support\Collection;

class day04_scratchcards extends solver
{
    public function solve() : array
    {
        $scratchcards = $this->parse_input();
        $this->solution('4a', $this->part1($scratchcards));
        $this->solution('4b', $this->part2($scratchcards));
        return $this->solutions;
    }

    public function part1(Collection $scratchcards) : int
    {
        return $scratchcards->map(fn($card) => $card->score())->sum();
    }

    public function part2(Collection $scratchcards) : int
    {
        $total_cards = $scratchcards->count();

        foreach($scratchcards as $current_card => $card) {
            $winners = $card->winners()->count();
            $max = min($current_card+$winners+1, $total_cards);
            for($j=$current_card+1; $j<$max; $j++) {
                $scratchcards[$j]->add($card->count);
            }
        }
        return $scratchcards->map(fn($card) => $card->count)->sum();
    }

    public function parse_input() : Collection
    {
        return $this->input
            ->map(fn($i) => explode('|', trim(substr($i, 8))))
            ->map(function($a) {
               $winning = collect(preg_split('/\s+/', trim($a[0])));
               $numbers = collect(preg_split('/\s+/', trim($a[1])));
               return new Scratchcard($winning, $numbers);
            });
    }
 }
