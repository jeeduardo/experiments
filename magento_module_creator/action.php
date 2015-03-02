<?php

require_once 'lib.php';

$builder = new ModuleCreationBuilder();
$director = new ModuleCreationDirector($builder, $_POST);
$director->buildModule();
$director->buildModuleZip();
$director->buildModuleHelper();

