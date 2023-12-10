<?php namespace day10_pipe_maze;
use Lib\solver;

class day10_pipe_maze extends solver
{
    public array $path = [];

    public function solve() : void
    {
        $maze = $this->parse_input();

        /* find the start position, the next step we take, and what kind of pipe S is */
        [$x, $y, $nx, $ny, $s] = $this->find_start($maze);

        /* replace S in the maze */
        $maze[$y][$x] = $s;

        $this->solution('10a', $this->part1($maze, $x, $y, $nx, $ny));
        $this->solution('10b', $this->part2($maze));
    }

    public function part1(array $maze, int $x, int $y, int $nx, int $ny) : int
    {
        /* starting position */
        $this->path[$y][$x] = 1;

        /* walk the maze to the end */
        return (int)$this->walk_maze($maze, $nx, $ny, $x, $y, $x, $y)/2;
    }

    public function part2(array $maze) : int
    {
        $enclosed = 0;

        /* loop through the whole maze */
        foreach($maze as $y => $row) {
            foreach ($row as $x => $pipe) {
                /* check if this point is not on the path */
                if (!isset($this->path[$y][$x])) {
                    $intersects = $this->raycast($maze, $x, $y);

                    if (($intersects % 2) === 1) {
                        $enclosed++;
                    }
                }
            }
        }
        return $enclosed;
    }

    public function walk_maze(array $maze, int $x, int $y, int $from_x, int $from_y, int $to_x, int $to_y) : int
    {
        /* find the next step in the maze */
        [$nx, $ny, $pipe] = $this->find_next($maze, $x, $y, $from_x, $from_y);

        /* we reached the destination */
        if ($x === $to_x && $y === $to_y) return 1;

        /* keep track of our path */
        $this->path[$y][$x] = 1;

        /* recurse into the maze */
        return 1 + $this->walk_maze($maze, $nx, $ny, $x, $y, $to_x, $to_y);
    }

    public function find_next(array $maze, int $x, int $y, int $from_x, int $from_y) : array
    {
        $pipe = $maze[$y][$x];
        switch($pipe) {
            case '|':
                return $from_y === $y-1 ? [$x, $y+1, $pipe] : [$x, $y-1, $pipe];
            case '-':
                return $from_x === $x-1 ? [$x+1, $y, $pipe] : [$x-1, $y, $pipe];
            case 'L':
                return $from_y === $y ? [$x, $y-1, $pipe] : [$x+1, $y, $pipe];
            case 'F':
                return $from_y === $y+1 ? [$x+1, $y, $pipe] : [$x, $y+1, $pipe];
            case '7':
                return $from_y === $y+1 ? [$x-1, $y, $pipe] : [$x, $y+1, $pipe];
            case 'J':
                return $from_y === $y-1 ? [$x-1, $y, $pipe] : [$x, $y-1, $pipe];
        }
    }

    public function raycast(array $maze, int $x, int $y) : int
    {
        $intersects = 0;

        /* raycase from point to the left */
        for($i=$x-1; $i>=0; $i--) {
            $c = $maze[$y][$i];

            /* we are crossing the path */
            if (isset($this->path[$y][$i]) && in_array($c, ['|', 'J', 'L'])) $intersects++;
        }
        return $intersects;
    }

    public function find_start(array $maze) : array
    {
        $width = count($maze[0]);
        $height = count($maze);

        /* first find the S character */
        foreach($maze as $y => $row)
            foreach($row as $x => $pipe)
                if ($pipe === 'S') break 2;

        $left = $right = $above = $below = 0;

        /* check the pipe to the left of S */
        if ($x > 0) {
            $next_pipe = $maze[$y][$x-1];
            if (in_array($next_pipe, ['-', 'L', 'F'])) {
                $left = 1;
                $valid_neighbors[] = [$x-1, $y];
            }
        }
        /* check the pipe to the right of S */
        if ($x < $width-1)  {
            $next_pipe = $maze[$y][$x+1];
            if (in_array($next_pipe, ['-', '7', 'J'])) {
                $right = 1;
                $valid_neighbors[] = [$x+1, $y];
            }
        }
        /* check the pipe to the above of S */
        if ($y > 0) {
            $next_pipe = $maze[$y-1][$x];
            if (in_array($next_pipe, ['|', '7', 'F'])) {
                $above = 1;
                $valid_neighbors[] = [$x, $y-1];
            }
        }
        /* check the pipe to the below of S */
        if ($y < $height-1) {
            $next_pipe = $maze[$y+1][$x];
            if (in_array($next_pipe, ['|', 'L', 'J'])) {
                $below = 1;
                $valid_neighbors[] = [$x, $y+1];
            }
        }

        /* decide which kind of pipe S must be */
        if ($left && $right)      $s = '-';
        elseif ($above && $below) $s = '|';
        elseif ($left && $above)  $s = 'J';
        elseif ($right && $above) $s = 'L';
        elseif ($left && $below)  $s = '7';
        elseif ($right && $below) $s = 'F';

        return [$x, $y, $valid_neighbors[0][0], $valid_neighbors[0][1], $s];
    }

    public function parse_input() : array
    {
        return $this->input->map(fn($m) => str_split($m))->toArray();
    }
 }
