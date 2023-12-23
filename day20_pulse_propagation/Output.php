<?php namespace day20_pulse_propagation;

use Illuminate\Support\Collection;

class Output extends Module implements ModuleSpec
{
    public function process(Signal $signal) : Collection
   {
        return collect();
   }
}
