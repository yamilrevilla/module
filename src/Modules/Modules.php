<?php

namespace Yrevilla\Modules;


use Yrevilla\Modules\Contracts\RepositoryInterface;

class Modules implements RepositoryInterface
{
    protected $repository;


    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function optimize()
    {
        return $this->repository->optimize();
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function slugs()
    {
        return $this->repository->slugs();
    }

    public function where($key, $value)
    {
        return $this->repository->where($key, $value);
    }

    public function sortBy($key)
    {
        return $this->repository->sortBy($key);
    }

    public function sortByDesc($key)
    {
        return $this->repository->sortByDesc($key);
    }

    public function exists($slug)
    {
        return $this->repository->exists($slug);
    }

    public function count()
    {
        return $this->repository->count();
    }

    public function getPath()
    {
        return $this->repository->getPath();
    }

    public function setPath($path)
    {
        return $this->repository->setPath($path);
    }

    public function getModulePath($slug)
    {
        return $this->repository->getModulePath($slug);
    }

    public function getManifest($slug)
    {
        return $this->repository->getManifest($slug);
    }

    public function get($property, $default = null)
    {
        return $this->repository->get($property, $default);
    }

    public function set($property, $value)
    {
        return $this->repository->set($property, $value);
    }

    public function enabled()
    {
        return $this->repository->enabled();
    }

    public function disabled()
    {
        return $this->repository->disabled();
    }

    public function isEnabled($slug)
    {
        return $this->repository->isEnabled($slug);
    }

    public function isDisabled($slug)
    {
        return $this->repository->isDisabled($slug);
    }

    public function enable($slug)
    {
        return $this->repository->enable($slug);
    }

    public function disable($slug)
    {
        return $this->repository->disable($slug);
    }

    public function register()
    {
        $modules = $this->repository->enabled();
        $modules->each(function ($properties, $slug) {
            $this->autoloadFiles($properties);
        });
    }

    protected function autoloadFiles($properties)
    {
        if (isset($properties['autoload'])) {
            $namespace = $this->resolveNamespace($properties);
            $path      = $this->repository->getPath()."/{$namespace}/";
            foreach ($properties['autoload'] as $file) {
                include $path.$file;
            }
        }
    }


    public function getNamespace()
    {
        return $this->repository->getNamespace();
    }

    public function resolveNamespace($properties)
    {
        return isset($properties['namespace'])
            ? $properties['namespace']
            : studly_case($properties['slug'])
            ;
    }

}