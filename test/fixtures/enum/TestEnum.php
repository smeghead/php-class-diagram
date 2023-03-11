<?php
namespace Hoge\TestEnum;

/** スート */
enum Suit
{
    /** ハート */
    case Hearts;
    /** ダイヤ */
    case Diamonds;
    /** クローバー */
    case Clubs;
    /**
     * スペード
     * 説明コメント
     */
    case Spades;
}

function do_stuff(Suit $s)
{
    // ...
}

do_stuff(Suit::Spades);
