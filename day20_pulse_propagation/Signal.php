<?php namespace day20_pulse_propagation;
use day20_pulse_propagation\Enums\Pulse;

class Signal
{
    public function __construct(public string $source, public string $destination, public Pulse $pulse) {}
}
