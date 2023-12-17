<?php namespace day16_the_floor_will_be_lava;

class Beam
{
    public function __construct(
        public int $x, public int $y, public int $dx, public int $dy
    ) {}

    public function next() : self
    {
        $this->x += $this->dx;
        $this->y += $this->dy;
        return $this;
    }

    public function empty_space(string $pos) : bool
    {
        return $pos === '.';
    }

    public function pass_through(string $pos) : bool
    {
        return match(true) {
            $pos === '-' && $this->dy === 0,
            $pos === '|' && $this->dx === 0 => true,
            default => false
        };
    }

    public function deflect(string $pos) : self
    {
        return match($pos) {
            '/'  => $this->set_dir(-$this->dy, -$this->dx)->next(),
            '\\' => $this->set_dir($this->dy, $this->dx)->next()
        };
    }

    public function split(string $pos) : array
    {
        if ($pos === '|') {
            $beam1 = new Beam($this->x, $this->y, 0, -1);
            $beam2 = new Beam($this->x, $this->y, 0, 1);
        } else {
            $beam1 = new Beam($this->x, $this->y, -1, 0);
            $beam2 = new Beam($this->x, $this->y, 1, 0);
        }
        return [$beam1->next(), $beam2->next()];
    }

    public function set_dir(int $dx, int $dy) : self
    {
        $this->dx = $dx;
        $this->dy = $dy;
        return $this;
    }
}
