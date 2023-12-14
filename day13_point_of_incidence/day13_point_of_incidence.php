<?php namespace day13_point_of_incidence;
use Illuminate\Support\Collection;
use Lib\solver;

class day13_point_of_incidence extends solver
{
    public function solve() : void
    {
        $patterns = $this->parse_input();
        $this->solution('13a', $this->check_reflections($patterns));
        $this->solution('13b', $this->check_reflections($patterns, true));
    }

    public function check_reflections(Collection $patterns, bool $smudge = false) : int
    {
        return $patterns->map(fn($pattern) => $this->check_reflection($pattern[0], 100, $smudge) ?? $this->check_reflection($pattern[1], 1, $smudge))->sum();
    }

    public function check_reflection(Collection $pattern, int $factor, bool $smudge) : int|null
    {

        $length = count($pattern);
        $r = null;
        for($i=0; $i<$length-1 && $r === null; $i++) {

            /* the two are the same, ripple outwards */
            if($pattern[$i] === $pattern[$i+1]) {
                if ($this->ripple_outwards($pattern, $i, $i+1, $length, 0, $smudge)) $r =  (($i+1) * $factor);
            } elseif($smudge && $this->differ_by_one($pattern[$i], $pattern[$i+1])) {
                if ($this->ripple_outwards($pattern, $i, $i+1, $length, 1, $smudge)) $r =  (($i+1) * $factor);
            }
        }
        return $r;
    }

    public function ripple_outwards(Collection $p, int $i1, int $i2, $length, int $diff, bool $smudge) : int
    {
        $j = 1;
        while($i1-$j>=0 && $i2+$j<$length && $diff < 2) {
            /* they are a reflection, continue looking */
            if ($p[$i1-$j] === $p[$i2+$j]) {
                $j++;
                continue;
            }
            /* we allow a smudge, continue looking */
            if ($smudge && $this->differ_by_one($p[$i1-$j], $p[$i2+$j])) {
                $diff++;
                $j++;
                continue;
            }
            /* they are not a reflection */
            return false;
        }
        return $smudge ? $diff === 1 : $diff === 0;
    }

    public function differ_by_one(int $a, int $b) : bool
    {
        $xor = $a ^ $b;
        $and = $xor & ($xor - 1);
        return $and === 0;
    }

    public function parse_input() : Collection
    {
        return $this
            ->input
            ->map(fn($r) =>str_replace(['#', '.'], [1, 0], $r))
            ->chunkWhile(fn (string $value) => $value !== "")
            ->map(fn($m) => collect($m)->filter(fn($r) => $r !== ""))
            ->map(function($m) {
                $m1 = $m->map(fn($n) => str_split($n));
                $m2 = collect(transpose($m1->toArray()))->values();
                return [
                    $m1->map(fn($n) => bindec(implode('',$n)))->values(),
                    $m2->map(fn($n) => bindec(implode('', $n)))->values()
                ];
            });
    }
 }
