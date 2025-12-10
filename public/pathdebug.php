<?php
echo "__DIR__ = " . __DIR__ . "<br>";
echo "dirname(__DIR__) = " . dirname(__DIR__) . "<br>";
echo "ROOT CONTENT:<br>";

$root = dirname(__DIR__);
foreach (scandir($root) as $f) {
    echo $f . "<br>";
}