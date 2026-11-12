<?php

namespace App\Publishers;

interface DeployerInterface
{
    public function test(): string;
    public function deploy(string $zipPath): string; // returns live URL
}
