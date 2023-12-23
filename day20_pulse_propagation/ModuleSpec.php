<?php namespace day20_pulse_propagation;

use Illuminate\Support\Collection;

interface ModuleSpec
{
    public function process(Signal $signal) : Collection;
}
