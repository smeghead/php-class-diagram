<?php
namespace Hoge\TestEnum\Sub;

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
    case Small;
    case Medium;
    case Large;
}