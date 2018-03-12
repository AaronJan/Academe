<?php

namespace Academe\Laravel\Console;

use Academe\Laravel\Console\Traits\MakeCommandHelper;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class BondMakeCommand extends GeneratorCommand
{
    use MakeCommandHelper;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'academe:make:bond';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Bond class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Bond';

    /**
     * @var string
     */
    protected $relativeDirectory;

    /**
     * @var string
     */
    protected $blueprintRelativeDirectory;

    /**
     * BlueprintMakeCommand constructor.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct($files);

        $this->relativeDirectory          = config('academe.laravel.bond_directory');
        $this->blueprintRelativeDirectory = config('academe.laravel.blueprint_directory');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/bond.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        // Convert directory path to namespace.
        return $this->convertPathToNamespace($this->relativeDirectory, $rootNamespace);
    }

    /**
     * Build the class with the given name.
     *
     * @param  string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $replace = [];

        if ($this->option('primary')) {
            $replace['DummyPrimaryKey'] = $this->option('primary');
        }

        $replace['DummySubject'] = $this->option('subject') ?: strtolower($name);

        $lastBlueprintNamespace = array_last(explode('/', $this->blueprintRelativeDirectory));
        $lastBlueprintNamespace = $lastBlueprintNamespace ?
            "{$lastBlueprintNamespace}\\" :
            '';

        list($hostBlueprintClass, $hostPrimaryKey) = $this->splitString(($this->option('host') ?: ''), ':', 2, '');
        $replace['DummyHostBlueprintClass'] = $hostBlueprintClass ? "{$lastBlueprintNamespace}{$hostBlueprintClass}::class" : '';
        $replace['DummyHostKeyField']       = $hostPrimaryKey;

        list($guestBlueprintClass, $guestPrimaryKey) = $this->splitString(($this->option('guest') ?: ''), ':', 2, '');
        $replace['DummyGuestBlueprintClass'] = $guestBlueprintClass ? "{$lastBlueprintNamespace}{$guestBlueprintClass}::class" : '';
        $replace['DummyGuestKeyField']       = $guestPrimaryKey;

        if ($hostBlueprintClass !== '' || $guestBlueprintClass !== '') {
            $replace['DummyBlueprintImport'] = PHP_EOL . 'use ' . $this->convertPathToNamespace($this->blueprintRelativeDirectory, $this->rootNamespace());
        } else {
            $replace['DummyBlueprintImport'] = '';
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * @param $string
     * @param $separator
     * @param $total
     * @param $default
     * @return array
     */
    protected function splitString($string, $separator, $total, $default = '')
    {
        $partials = explode($separator, $string);
        $count    = count($partials);

        return $count >= $total ?
            $partials :
            array_pad($partials, $total, $default);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['primary', 'p', InputOption::VALUE_OPTIONAL, 'Set primary key field for this Bond.'],
            ['subject', 's', InputOption::VALUE_OPTIONAL, 'Set subject for this Bond.'],
            ['host', null, InputOption::VALUE_OPTIONAL, 'Set host Blueprint class (and primary key) for this Bond.'],
            ['guest', null, InputOption::VALUE_OPTIONAL, 'Set guest Blueprint class (and primary key) for this Bond.'],
        ];
    }
}