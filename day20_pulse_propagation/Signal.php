<?php namespace day20_pulse_propagation;
class Signal
{
    public function __construct(public string $source, public string $destination, public Pulse $pulse) {}
}
