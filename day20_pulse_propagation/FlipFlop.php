<?php namespace day20_pulse_propagation;

use day20_pulse_propagation\Enums\Mode;
use day20_pulse_propagation\Enums\Pulse;
use Illuminate\Support\Collection;

class FlipFlop extends Module implements ModuleSpec
{
    public Mode $mode = Mode::OFF;

    public function process(Signal $signal) : Collection
    {
        /* receiving high pulse, just do nothing */
        if ($signal->pulse === Pulse::HIGH) return $this->send(null);

        /* switch between on and off */
        $this->mode = $this->mode === Mode::OFF ? Mode::ON : Mode::OFF;

        return $this->send($this->mode === Mode::ON ? Pulse::HIGH : Pulse::LOW);
    }

    public function reset() : void
    {
        $this->mode = Mode::OFF;
    }
}
