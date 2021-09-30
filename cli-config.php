<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
//use Doctrine\ORM\EntityManager;

// replace this with the path to your own project bootstrap file.
require_once 'bootstrap.php';

// replace with mechanism to retrieve EntityManager in your app
//$entityManager = GetEntityManager();

return ConsoleRunner::createHelperSet($entityManager);