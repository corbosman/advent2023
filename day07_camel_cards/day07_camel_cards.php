<?php namespace day07_camel_cards;
use Lib\solver;
use Illuminate\Support\Collection;

class day07_camel_cards extends solver
{
    public function solve() : void
    {
        [$hands1, $hands2] = $this->parse_input();
        $this->solution('7a', $this->part12($hands1));
        $this->solution('7b', $this->part12($hands2));
    }

    public function part12(Collection $hands) : int
    {
        return $hands->sortBy([fn (Hand $hand1, Hand $hand2) => $hand1->compare($hand2)])
                     ->values()
                     ->map(fn($h, $i) => ($i+1) * $h->bid)
                     ->sum();
    }

    public function parse_input() : array
    {
        $hands = $this->input->map(fn($h) => preg_split('/\s+/', $h))->map(fn($h) => [str_split($h[0], 1), (int)$h[1]]);
        return [
            $hands->map(fn($h) => (new Hand($h[0], $h[1]))->score()),
            $hands->map(fn($h) => (new Hand($h[0], $h[1], true))->score()),
        ];
    }
 }
