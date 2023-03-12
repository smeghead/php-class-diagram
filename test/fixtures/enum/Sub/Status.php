<?php
namespace Hoge\TestEnum\Sub;

/** ゲームのステータス */
enum Status {
    /** プレイヤーのターン */
    case Player;
    /** コンピュータのターン */
    case Computer;
    /** ゲーム終了 */
    case GameSet;
}

enum MyExceptionCase {
    case InvalidMethod;
    case InvalidProperty;
    case Timeout;
}

enum Size
{
    public static function getSmallest(): self
    {
        return self::Small;
    }
    case Small;
    case Medium;
    case Large;
}