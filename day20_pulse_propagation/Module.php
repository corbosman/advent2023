<?php namespace day20_pulse_propagation;

use day20_pulse_propagation\Enums\Pulse;
use Illuminate\Support\Collection;

abstract class Module
{
    public Pulse $pulse;

    public function __construct(public string $name, public Collection $outputs) {
        $this->outputs = $outputs;
    }

    public function send(?Pulse $pulse) : Collection
    {
        if ($pulse === null) return collect();

        return $this->outputs->map(fn($output) => new Signal($this->name, $output, $pulse));
    }

    public function reset() : void {}
}
