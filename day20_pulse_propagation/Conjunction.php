<?php namespace day20_pulse_propagation;

use Illuminate\Support\Collection;

class Conjunction extends Module implements ModuleSpec
{
    public array $inputs = [];

    public function process(Signal $signal) : Collection
    {
        /* remember pulse for this source */
        $this->inputs[$signal->source] = $signal->pulse;
        $all_high = count(array_filter($this->inputs, fn($i) => $i === Pulse::LOW)) === 0;

        return $this->send($all_high ? Pulse::LOW : Pulse::HIGH);
    }

    public function add_input(string $name): void
    {
        $this->inputs[$name] = Pulse::LOW;
    }

    public function reset() : void
    {
        foreach($this->inputs as $i => $v) $this->inputs[$i] = Pulse::LOW;
    }
}
