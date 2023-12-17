<?php namespace Lib;
use SplPriorityQueue;

class Heap extends SplPriorityQueue
{
    public function compare($priority1, $priority2) : int
    {
        return parent::compare($priority2,$priority1);
    }
}
