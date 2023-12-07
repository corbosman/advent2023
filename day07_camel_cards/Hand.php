<?php

namespace day07_camel_cards;

class Hand
{
    public array $cards;
    public int $bid;
    public Type $type;
    public array $scores;
    public int $order;

    public function __construct(public bool $jokers = false) {}

    public function create(array $cards, int $bid) : self
    {
        $this->cards = $cards;
        $this->bid = $bid;
        $this->scores = $this->jokers
            ? ['T' => 'A', 'Q' => 'B', 'K' => 'C', 'A' => 'D', 'J' => 1]
            : ['T' => 'A', 'J' => 'B', 'Q' => 'C', 'K' => 'D', 'A' => 'E'];

        $this->type  = $this->check_hand($cards);
        $this->order = $this->order_score($cards);

        return $this;
    }

    public function check_hand(array $cards) : Type
    {
        $hand = collect(array_count_values($cards));

        if ($this->jokers) {
            $jokers = $hand['J'] ?? 0;
            $hand = $hand->forget('J');
        } else {
            $jokers = 0;
        }

        $has_five_of_a_kind  = $hand->contains(5);
        $has_four_of_a_kind  = $hand->contains(4);
        $has_three_of_a_kind = $hand->contains(3);
        $has_one_pair        = $hand->contains(2);
        $has_full_house      = $has_three_of_a_kind && $has_one_pair;
        $has_two_pairs       = $hand->filter(fn($h) => $h === 2)->count() === 2;

        if ($has_five_of_a_kind)      $type = TYPE::FIVE_OF_A_KIND;
        elseif ($has_four_of_a_kind)  $type = TYPE::FOUR_OF_A_KIND;
        elseif ($has_full_house)      $type = TYPE::FULL_HOUSE;
        elseif ($has_three_of_a_kind) $type = TYPE::THREE_OF_A_KIND;
        elseif ($has_two_pairs)       $type = TYPE::TWO_PAIRS;
        elseif ($has_one_pair)        $type = TYPE::ONE_PAIR;
        else                          $type = TYPE::HIGH_CARD;

        // convert a type to another type depending on how many jokers we have
        return match($jokers) {
            5,4 => TYPE::FIVE_OF_A_KIND,
            3 => match($type) {
                TYPE::ONE_PAIR => TYPE::FIVE_OF_A_KIND,
                default => TYPE::FOUR_OF_A_KIND
            },
            2 => match($type) {
                TYPE::THREE_OF_A_KIND => TYPE::FIVE_OF_A_KIND,
                TYPE::ONE_PAIR => TYPE::FOUR_OF_A_KIND,
                TYPE::HIGH_CARD => TYPE::THREE_OF_A_KIND
            },
            1 => match($type) {
                TYPE::FOUR_OF_A_KIND => TYPE::FIVE_OF_A_KIND,
                TYPE::THREE_OF_A_KIND => TYPE::FOUR_OF_A_KIND,
                TYPE::TWO_PAIRS => TYPE::FULL_HOUSE,
                TYPE::ONE_PAIR => TYPE::THREE_OF_A_KIND,
                TYPE::HIGH_CARD => TYPE::ONE_PAIR
            },
            0 => $type
        };
    }

    public function compare(Hand $hand) : int
    {
        return $hand->type !== $this->type
            ? $this->type->value <=> $hand->type->value
            : $this->order <=> $hand->order;
    }

    public function order_score(array $cards) : string
    {
        return hexdec(implode('', array_map(fn($card) =>  $this->scores[$card] ?? $card, $cards)));
    }
}
