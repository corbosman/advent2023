<?php namespace day20_pulse_propagation;
use day20_pulse_propagation\Enums\Pulse;
use Ds\Deque;
use Illuminate\Support\Collection;
use Lib\solver;

class day20_pulse_propagation extends solver
{
    public function solve() : void
    {
        $modules = $this->parse_input();
        $this->solution('20a', $this->part1($modules));
        $this->solution('20b', $this->part2($modules));
    }

    public function part1(Collection $modules) : int
    {
        $queue = new Deque;
        $pulses = [0,0];

        for($i=0; $i<1000; $i++) {
            $queue->push(new Signal('button', 'broadcaster', Pulse::LOW));
            $pulses[0]++;
            while($queue->count() > 0) {
                $signal = $queue->shift();

                /* destination module might not exist */
                if (($destination_module = $modules[$signal->destination] ?? null) === null) continue;

                /* process all the signals and fire off new signals */
                foreach($destination_module->process($signal) as $new_signal) {
                    $pulses[$new_signal->pulse->value]++;
                    $queue->push($new_signal);
                }
            }
        }
        return $pulses[0] * $pulses[1];
    }

    /*
        - rx is the output of a Conjunction module (in my input)
        - this means all inputs have to be HIGH to produce a LOW input to rx.
        - find all outputs that feed into this Conjunction module and keep track of when they're HIGH for the first time
        - then take the LCM of these values.
    */
    public function part2(Collection $modules) : int
    {
        /* first reset all modules */
        $modules->each->reset();

        /* find the module that feeds into rx */
        $module = $modules->filter(fn($m) => $m->outputs->contains('rx'))->first();

        /* find the modules that feed into that module */
        $modules_to_track = $modules->filter(fn($m) => $m->outputs->contains($module->name))->mapWithKeys(fn($m) => [$m->name => 0])->toArray();

        $queue = new Deque;
        $presses = 0;

        while(true) {
            $queue->push(new Signal('button', 'broadcaster', Pulse::LOW));
            $presses++;
            while($queue->count() > 0) {
                $signal = $queue->shift();

                if ($signal->destination === 'rx') continue;

                $destination_module = $modules[$signal->destination];

                /* process all the signals and fire off new signals */
                foreach($destination_module->process($signal) as $new_signal) {
                    if (isset($modules_to_track[$new_signal->source]) && $new_signal->destination === $module->name && $new_signal->pulse === Pulse::HIGH) {

                        /* seen it for the first time? */
                        if ($modules_to_track[$new_signal->source] === 0) {
                            $modules_to_track[$new_signal->source] = $presses;

                            /* seen them all?  We're done! */
                            if (array_product($modules_to_track) !== 0) break 3;
                        }
                    }
                    $queue->push($new_signal);
                }
            }
        }
        return array_reduce($modules_to_track, fn($c, $i) => (int)gmp_lcm($c, $i), 1);
    }

    public function parse_input() : Collection
    {
        $modules = collect();
        $modules['output'] = new Output('output', collect());

        foreach($this->input as $i) {
            [$m, $o] = explode(' -> ', $i);
            $o = explode(', ', $o);
            if ($m === 'broadcaster') {
                $name = $m;
                $module = new Broadcaster($name,collect($o));
            }
            elseif ($m[0] === '%') {
                $name = substr($m, 1);
                $module = new FlipFlop($name,collect($o));
            }
            elseif ($m[0] === '&') {
                $name = substr($m, 1);
                $module = new Conjunction($name,collect($o));
            }
            else assert(false);
            $modules[$name] = $module;
        }

        /* set inputs */
        foreach($modules as $name => $module) {
            foreach($module->outputs as $output) {
                if (isset($modules[$output]) && $modules[$output] instanceof Conjunction) $modules[$output]->add_input($name);
            }
        }
        return $modules;
    }
 }
