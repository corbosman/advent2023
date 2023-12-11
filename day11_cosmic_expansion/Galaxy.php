<?php namespace day11_cosmic_expansion;
use Lib\Point2D;

class Galaxy extends Point2D {
    public function distance(Galaxy $g) : int
    {
        return $this->manhattan($g);
    }
}
