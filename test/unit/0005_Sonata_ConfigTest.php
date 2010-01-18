<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 */

require_once dirname(__FILE__).'/bootstrap.php';

$t = new LimeTest();

// @Test: ->set() / ->get()

$t->is(Sonata_Config::get('quux'), null, 'Returns NULL for non-existing values if no fallback was given');
$t->is(Sonata_Config::get('quux', 'fallback'), 'fallback', 'Otherwiese the fallback is returned');

Sonata_Config::set('quux', 'Can I haz quux?');
$t->is(Sonata_Config::get('quux'), 'Can I haz quux?', 'Returns the right value if set');

// @Test: ->load()

$parserStub = $t->stub('Sonata_Parser_Config');
$parserStub->parse('some_non_existing_file.yml')->throws('RuntimeException');
$parserStub->parse('some_random_config_file.yml')->returns(array('foo' => 42, 'bar' => 4711));
$parserStub->parse('some_other_random_config_file.yml')->returns(array('rex' => 'fido'));
$parserStub->replay();

try
{
  Sonata_Config::load('some_non_existing_file.yml', $parserStub);
  $t->fail('No code should be executed after this');
}
catch(RuntimeException $ex)
{
  $t->pass('An exception is thrown if the config file cannot be found');
}

Sonata_Config::load('some_random_config_file.yml', $parserStub);
$t->is((Sonata_Config::get('foo') == 42 && Sonata_Config::get('bar') == 4711), true, 'Parsed valus were saved correctly');

Sonata_Config::load('some_other_random_config_file.yml', $parserStub);
$t->is((Sonata_Config::get('rex') == 'fido' && Sonata_Config::get('foo') == null), true, 'Old values are cleared at every parse if not set otherwise');

Sonata_Config::load('some_random_config_file.yml', $parserStub, false);
$t->is((Sonata_Config::get('rex') == 'fido' && Sonata_Config::get('foo') == 42), true, 'If set, the new values are being merged into the old ones');
