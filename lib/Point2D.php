<?php namespace Lib;

class Point2D
{
    public function __construct(
        public int $x,
        public int $y,
    ) {}

    /* calculate the manhattan distance to another point */
    public function manhattan(Point2D $p) : int
    {
        return abs($this->x - $p->x) + abs($this->y - $p->y);
    }

}
