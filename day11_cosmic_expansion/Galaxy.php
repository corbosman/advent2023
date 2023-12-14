<?php namespace day11_cosmic_expansion;
use Lib\Point2D;

class Galaxy extends Point2D {
    public function expand(int $factor, int $ex, int $ey) : void
    {
        $this->x += $factor*$ex;
        $this->y += $factor*$ey;
    }
}
