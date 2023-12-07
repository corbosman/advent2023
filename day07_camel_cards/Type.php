<?php

namespace day07_camel_cards;

enum Type : int
{
    case FIVE_OF_A_KIND = 7;
    case FOUR_OF_A_KIND = 6;
    case FULL_HOUSE = 5;
    case THREE_OF_A_KIND = 4;
    case TWO_PAIRS = 3;
    case ONE_PAIR = 2;
    case HIGH_CARD = 1;
}
