<?php

require_once './Database/Connection.php';
require_once './Models/Model.php';
require_once './Models/Animal.php';

$animal = new Animal();
$animal->setAttributes('name', 'veseli');
$animal->setAttributes('type', 'konj');
$animal->save();
sleep(3);
$animal->setAttributes('type', 'gazela');
$animal->save();
sleep(3);
$animal->delete();


