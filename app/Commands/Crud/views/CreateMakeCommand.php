<?php

namespace App\Commands\Crud\Views;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use App\Commands\Helpers\PackageDetail;

class CreateMakeCommand extends GeneratorCommand
{
    use PackageDetail;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crud:view.create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a `create` view files for CRUD generator';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'view';


    protected function getStub()
    {
        return __DIR__ . '/../stubs/views/create.stub';
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->namespaceFromComposer() . 'resources/views';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    public function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        $path = getcwd() . $this->devPath() . '/src/resources/views/' . strtolower($this->argument('name'));
        return $path . '/create.blade.php';
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            [
                'DummyModelName',
                'DummyModelLower',
                'DummyModelPlural',
                'DummyPackageName::',
                'InputFieldsReplacer',
            ],
            [
                $this->argument('name'),
                strtolower($this->argument('name')),
                Str::plural($this->argument('name')),
                $this->replaceLayout(),
                $this->createFields()
            ],
            $stub
        );

        return $this;
    }

    public function replaceLayout()
    {
        $content = $this->getComposer();
        return $content->type == 'library' ? strtolower($this->getPackageName()) . '::' : '';
    }

    public function createFields()
    {
        $fields = cache()->get('structure')->fields;
        $inputFields = '';
        foreach ($fields as $field) {
            $inputFields .= $this->generateFieldStub($field);
        }
        return $inputFields;
    }

    public function generateFieldStub($field)
    {
        $stub = file_get_contents(__DIR__ . '/../stubs/views/inputFields/text.stub');
        return $this->replaceField($stub, $field);
    }

    public function replaceField($stub, $field)
    {
        return str_replace(
            [
                'DummyModelVariable',
                'DummyModelCapitalVariable'
            ],
            [
                $field->name,
                ucfirst($field->name)
            ],
            $stub
        );
    }
}