<?php namespace day20_pulse_propagation;

use Illuminate\Support\Collection;

class Broadcaster extends Module implements ModuleSpec
{
    public function process(Signal $signal) : Collection
    {
        return $this->send($signal->pulse);
    }
}
