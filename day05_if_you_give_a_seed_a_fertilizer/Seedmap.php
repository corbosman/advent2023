<?php namespace day05_if_you_give_a_seed_a_fertilizer;

use Illuminate\Support\Collection;

class Seedmap
{
    public Collection $mappers;

    public function __construct(
        public string $name
    ) {
        $this->mappers = collect();
    }

    public function add_mapper(Mapper $mapper) : void
    {
        $this->mappers->push($mapper);
    }

    public function convert(int $seed) : int
    {
        foreach($this->mappers as $mapper) {
            if ($mapper->source_start <= $seed && $mapper->source_end >= $seed) {
                return $seed + $mapper->diff;
            }
        }
        return $seed;
    }

    public function convert_range(Collection $seeds) : Collection
    {
        $converted = collect();
        while($seeds->count()) {
            [$start, $end] = $seeds->shift();
            $found = false;
            foreach($this->mappers as $mapper) {
                if ($end < $mapper->source_start) continue;  // no overlap
                if ($start > $mapper->source_end) continue;  // no overlap

                $overlap_min = max($start, $mapper->source_start);
                $overlap_max = min($end, $mapper->source_end);

                /* we have an overlap */
                if ($overlap_min <= $overlap_max) {
                    $converted->push([$overlap_min + $mapper->diff, $overlap_max+$mapper->diff]);

                    /* original range on the left */
                    if ($overlap_min > $start) $seeds->push([$start, $overlap_min-1]);

                    /* original range on the right */
                    if ($end > $overlap_max) $seeds->push([$overlap_max+1, $end]);

                    $found = true;
                    break;
                }
            }
            /* we didn't find overlap, push the original range */
            if (!$found) $converted->push([$start, $end]);
        }
        return $converted;
    }
}
