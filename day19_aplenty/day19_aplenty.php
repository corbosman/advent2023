<?php namespace day19_aplenty;
use Lib\solver;
use Illuminate\Support\Collection;

class day19_aplenty extends solver
{
    public function solve() : void
    {
        [$workflows, $parts] = $this->parse_input();
        $this->solution('19a', $this->part1($workflows, $parts));
        $this->solution('19b', $this->part2($workflows, $parts));
    }

    public function part1(Collection $workflows, Collection $parts) : int
    {
        return $parts->filter(fn($part) => $this->is_accepted($part, $workflows, 'in'))
            ->map(fn($part) => $part->x + $part->m + $part->a + $part->s)->sum();
    }

    public function part2(Collection $workflows, Collection $parts) : int
    {
        return $this->combinations($workflows, new Ranges, 'in');
    }

    /* part 1 methods */
    public function is_accepted(Part $part, Collection $workflows, string $name) : bool
    {
        return match($name) {
            'R'     => false,
            'A'     => true,
            default => $this->check_rule($part, $workflows, $name)
        };
    }

    public function check_rule(Part $part, Collection $workflows, string $name) : int
    {
        $workflow = $workflows[$name];
        foreach($workflow->rules as $rule) {
            if ($this->compare($part->{$rule->category}, $rule->cmp, $rule->num)) return $this->is_accepted($part, $workflows, $rule->next);
        }
        return $this->is_accepted($part, $workflows, $workflow->last);
    }

    public function compare(int $rating, string $cmp, int $num) : bool
    {
        return match($cmp) {
            '<' => $rating < $num,
            '>' => $rating > $num,
            default => assert(false)
        };
    }


    /* part 2 methods */
    public function combinations(Collection $workflows, Ranges $ranges, string $name) : int
    {
        return match($name) {
            'R'     => 0,
            'A'     => $this->accepted($ranges),
            default => $this->check_rules_for_ranges($workflows, $ranges, $name)
        };
    }

    public function check_rules_for_ranges(Collection $workflows, Ranges $ranges, string $name) : int
    {
        $workflow = $workflows[$name];  /* current workflow */
        $combinations = 0;              /* total combinations for true and false side of the checks */
        $last = true;                   /* do we need to run the last rule */

        /* go through the rules */
        foreach($workflow->rules as $i => $rule) {
            $new_ranges  = clone($ranges);
            $category    = $rule->category;             /* the current category for this rule: xmas */
            $next        = $rule->next;                 /* the next rule to go to when rule is true */
            $num         = $rule->num;                  /* the value to check against */
            [$min, $max] = $ranges->{$category};        /* the min and max of the range belonging to this category */

            switch($rule->cmp) {
                case '<':
                    /* true side of the equation */
                    [$tmin, $tmax] = [$min, $num - 1];
                    if ($tmin <= $tmax) {
                        $new_ranges->{$category} = [$tmin, $tmax];
                        /* equation is true, go to the next workflow */
                        $combinations += $this->combinations($workflows, $new_ranges, $next);
                    }

                    /* equation is false, now we need to check more rules on the remainder of the range */
                    [$fmin, $fmax] = [$num, $max];
                    if ($fmin <= $fmax) {
                        $ranges->{$category} = [$fmin, $fmax];
                    } else {
                        /* there is nothing left on the false side, we don't need to run the last rule */
                        $last = false;
                    }
                    break;
                case '>':
                    /* true side of the equation */
                    [$tmin, $tmax] = [$num + 1, $max];
                    if ($tmin <= $tmax) {
                        $new_ranges->{$category} = [$tmin, $tmax];
                        /* equation is true, go to the next workflow */
                        $combinations += $this->combinations($workflows, $new_ranges, $next);
                    }

                    /* equation is false, now we need to check more rules on the remainder of the range */
                    [$fmin, $fmax] = [$min, $num];
                    if ($fmin <= $fmax) {
                        $ranges->{$category} = [$fmin, $fmax];
                    } else {
                        /* there is nothing left on the false side, we don't need to run the last rule */
                        $last = false;
                    }
                    break;
                default:
                    assert(false);
            }
        }
        /* we need to run the last rule because the rules didn't cover all the ranges */
        if ($last) $combinations += $this->combinations($workflows, $ranges, $workflow->last);

        return $combinations;
    }

    public function accepted(Ranges $ranges) : int
    {
        return ($ranges->x[1] - $ranges->x[0] + 1) *
               ($ranges->m[1] - $ranges->m[0] + 1) *
               ($ranges->a[1] - $ranges->a[0] + 1) *
               ($ranges->s[1] - $ranges->s[0] + 1);
    }

    public function parse_input() : array
    {
        [$workflows, $parts] = $this->input->chunkWhile(fn($c) => $c!=='');
        $workflows = $workflows->mapWithKeys(function($w) {
            [$name, $rules] = explode('{', rtrim($w, '}'));

            $rules = explode(',', $rules);
            $last = array_pop($rules);

            $rules = array_map(function($m) {
                [$rule, $next] = explode(':', $m);
                return new Rule($rule[0], $rule[1], substr($rule,2), $next);
            }, $rules);

            return [$name => new Workflow($rules, $last)];
        });

        $parts = collect($parts)
            ->filter(fn($r) => $r !== '')
            ->map(fn($r) => trim($r, '{}'))
            ->map(fn($r) => explode(',', $r))
            ->map(fn($r) => new Part((int)substr($r[0],2),(int)substr($r[1],2),(int)substr($r[2],2),(int)substr($r[3],2)));

        return [$workflows, $parts];
    }
 }
