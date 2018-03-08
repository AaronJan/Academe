<?php

namespace Academe\Laravel\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class BlueprintMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'academe:make:blueprint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new blueprint class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Blueprint';

    /**
     * @var string
     */
    protected $relativeDirectory;

    /**
     * BlueprintMakeCommand constructor.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct($files);

        $this->relativeDirectory = config('academe.laravel.blueprint_directory');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/blueprint.stub';
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
        $appendNamespace = '\\' . trim(
                str_replace('/', '\\', $this->relativeDirectory),
                '\\'
            );

        return $rootNamespace . $appendNamespace;
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

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['primary', 'p', InputOption::VALUE_OPTIONAL, 'Set primary key field for this Blueprint.'],
            ['subject', 's', InputOption::VALUE_OPTIONAL, 'Set subject for this Blueprint.'],
        ];
    }
}