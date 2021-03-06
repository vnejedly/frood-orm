<?php

$projectRoot = __DIR__ . '/../../../..';

require_once $projectRoot . "/vendor/autoload.php";

$config = json_decode(file_get_contents("$projectRoot/config/orm.json"), true);

$projectNamespace = $config['projectNamespace'];
$database = $config['database'];
$relation = $config['relation'];
$persistence = $config['persistence'];

$container = new \Hooloovoo\DI\Container\Container();
$container->addDefinitionClass(new \Hooloovoo\ORM\Generator\Relation\ContainerDefinition($config));

$entityDir = $projectRoot . '/src/' . $relation['directory'] . '/Entity';
$genericManagerDir = $projectRoot . '/src/' . $relation['directory'] . '/GenericManager';
$managerDir = $projectRoot . '/src/' . $relation['directory'] . '/Manager';
$exportEntityDir = $projectRoot . '/export/src/' . $relation['directory'] . '/Entity';
$exportDeserializerDir = $projectRoot . '/export/src/' . $relation['directory'] . '/Deserializer';
$exportConfigDir = $projectRoot . '/export/config';

$relationEntityNamespace = $relation['namespace'] . '\\Entity';
$persistenceEntityNamespace = $persistence['namespace'] . '\\Entity';
$relationManagerGenericNamespace = $relation['namespace'] . '\\GenericManager';
$relationManagerNamespace = $relation['namespace'] . '\\Manager';
$relationDeserializerNamespace = $relation['namespace'] . '\\Deserializer';
$persistenceManagerNamespace = $persistence['namespace'] . '\\Manager';
$persistenceDeserializerNamespace = $persistence['namespace'] . '\\Deserializer';

if (!is_dir($entityDir)) mkdir($entityDir);
if (!is_dir($genericManagerDir)) mkdir($genericManagerDir);
if (!is_dir($managerDir)) mkdir($managerDir);
if (!is_dir($exportEntityDir)) mkdir($exportEntityDir);
if (!is_dir($exportDeserializerDir)) mkdir($exportDeserializerDir);

$servicesPattern = new \Hooloovoo\Generator\Pattern\SingleFile(
    "$exportConfigDir/services.yml",
    "$exportConfigDir/services-temp.yml",
    __DIR__ . '/../template/services-yml/item-relation.php'
);
$servicesPattern->setContentPlaceholder('{{{_content_relation_}}}');

/** @var \Hooloovoo\Generator\Generator $entitiesGenerator */
$generator = $container->get(Hooloovoo\Generator\Generator::class);
$generator->setExternalVariable('projectNamespace', $projectNamespace);
$generator->setExternalVariable('persistenceEntityNamespace', $persistenceEntityNamespace);
$generator->setExternalVariable('persistenceManagerNamespace', $persistenceManagerNamespace);
$generator->setExternalVariable('persistenceDeserializerNamespace', $persistenceDeserializerNamespace);
$generator->setExternalVariable('relationEntityNamespace', $relationEntityNamespace);
$generator->setExternalVariable('relationManagerGenericNamespace', $relationManagerGenericNamespace);
$generator->setExternalVariable('relationManagerNamespace', $relationManagerNamespace);
$generator->setExternalVariable('relationDeserializerNamespace', $relationDeserializerNamespace);
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($entityDir, __DIR__ . '/../template/relation/entity.php'));
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($genericManagerDir, __DIR__ . '/../template/relation/generic-manager.php'));
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($managerDir, __DIR__ . '/../template/relation/manager.php', false));
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($exportEntityDir, __DIR__ . '/../template/relation/entity.php'));
$generator->addPattern(new \Hooloovoo\Generator\Pattern\MultiFile($exportDeserializerDir, __DIR__ . '/../template/relation/deserializer.php'));
$generator->addPattern($servicesPattern);
$generator->run();

unlink("$exportConfigDir/services-temp.yml");