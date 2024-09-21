<?php
class SimpleHooks
{
    public string $bar;
 
    public string $baz {
        get => $this->baz;
        set => strtoupper($value);
    }

    public string $onlyGet {
        get => $this->onlyGet;
    }

    public string $onlySet {
        set => strtoupper($value);
    }

    public string $ref {
        &get => $this->ref;
        set => strtoupper($value);
    }
}