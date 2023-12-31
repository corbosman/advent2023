<?php namespace Lib;
use Illuminate\Support\Collection;
use ReflectionClass;

abstract class Solver implements SolverContract
{
    protected float $timer;
    protected array $solutions = [];
    protected Collection $input;
    protected ReflectionClass $reflection;

    public function __construct($options)
    {
        $this->reflection = new ReflectionClass($this);
        $puzzle = $options['e'] ?? $this->puzzle();
        $this->input = $this->get_input($puzzle);
    }

    public function start_timer() : self
    {
        $this->timer = microtime(true);
        return $this;
    }

    public function solver() : self
    {
        $this->solve();
        return $this;
    }

    /* add a solution */
    public function solution($puzzle, $value) : void
    {
        $time = microtime(true);
        $this->solutions[] = [$puzzle, $this->title(), $value, $time - $this->timer];
        $this->timer = $time;
    }

    public function solutions() : array
    {
        return $this->solutions;
    }

    /* get the puzzle number from the class name */
    public function puzzle() : string
    {
        return substr($this->reflection->getShortName(), 3, 2);
    }

    /* get the title from the class name */
    public function title() : string
    {
        return ucwords(str_replace('_', ' ', substr($this->reflection->getShortName(), 6)));
    }

    private function get_input(mixed $puzzle) : Collection
    {
        $filename = dirname($this->reflection->getFileName()) . "/input/{$puzzle}.txt";
        if (file_exists($filename)) {
            return $this->read_input($filename);
        }
        die("no input for puzzle {$puzzle}");
    }

    public function read_input($filename) : Collection
    {
        return collect(file($filename, FILE_IGNORE_NEW_LINES));
    }
}
