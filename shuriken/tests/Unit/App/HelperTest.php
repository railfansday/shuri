<?php

use App\Helper;

it("spintax memilih salah satu dari dua opsi", function () {
    $result = Helper::spintax("{hello|world}");
    expect($result)->toBeIn(["hello", "world"]);
});

it("spintax nested bekerja dengan benar", function () {
    $result = Helper::spintax("{a {b|c}|d}");
    expect($result)->toBeIn(["a b", "a c", "d"]);
});

it("spintax teks tanpa kurung kurawal dikembalikan apa adanya", function () {
    expect(Helper::spintax("plain text"))->toBe("plain text");
});

it("spintax teks kosong dikembalikan apa adanya", function () {
    expect(Helper::spintax(""))->toBe("");
});
