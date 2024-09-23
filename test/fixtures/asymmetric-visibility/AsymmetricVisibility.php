<?php
class AsymmetricVisibility
{
    public function __construct(
        public int $param1,
        private int $param2,
        public private(set) string $name,
        private(set) int $amount,
        protected(set) float $rate
    )
    {        
    }
}