<?php

namespace Yrevilla\Modules\Repositories;


class LocalRepository extends Repository
{

    public function optimize()
    {
        $cachePath = $this->getCachePath();
        $cache     = $this->getCache();
        $basenames = $this->getAllBaseNames();
        $modules   = collect();
        $basenames->each(function ($module, $key) use ($modules, $cache) {
            $temp = collect($cache->get($module));
            $manifest = collect($this->getManifest($module));
            $modules->put($module, $temp->merge($manifest));
        });
        $modules->each(function ($module) {
            if (!$module->has('enabled')) {
                $module->put('enabled', true);
            }
            if (!$module->has('order')) {
                $module->put('order', 9001);
            }
            return $module;
        });
        $content = json_encode($modules->all(), JSON_PRETTY_PRINT);
        return $this->files->put($cachePath, $content);
    }

    public function all()
    {
        return $this->getCache()->sortBy('order');
    }

    public function slugs()
    {
        $slugs = collect();
        $this->all()->each(function ($item, $key) use ($slugs) {
            $slugs->push($item['slug']);
        });
        return $slugs;
    }

    public function where($key, $value)
    {
        return collect($this->all()->where($key, $value)->first());
    }

    public function sortBy($key)
    {
        $collection = $this->all();
        return $collection->sortBy($key);
    }

    public function sortByDesc($key)
    {
        $collection = $this->all();
        return $collection->sortByDesc($key);
    }

    public function exists($slug)
    {
        return $this->slugs()->contains(str_slug($slug));
    }

    public function count()
    {
        return $this->all()->count();
    }

    public function get($property, $default = null)
    {
        list($slug, $key) = explode('::', $property);
        $module = $this->where('slug', $slug);
        return $module->get($key, $default);
    }

    public function set($property, $value)
    {
        list($slug, $key) = explode('::', $property);
        $cachePath = $this->getCachePath();
        $cache     = $this->getCache();
        $module    = $this->where('slug', $slug);
        if (isset($module[$key])) {
            unset($module[$key]);
        }
        $module[$key] = $value;

        $module = collect([$module['name'] => $module]);
        $merged  = $cache->merge($module);
        $content = json_encode($merged->all(), JSON_PRETTY_PRINT);
        //var_dump($cache);
        //var_dump($module);
        return $this->files->put($cachePath, $content);
    }

    public function enabled()
    {
        return $this->all()->where('enabled', true);
    }

    public function disabled()
    {
        return $this->all()->where('enabled', false);
    }

    public function isEnabled($slug)
    {
        $module = $this->where('slug', $slug);
        return $module['enabled'] === true;
    }

    public function isDisabled($slug)
    {
        $module = $this->where('slug', $slug);
        return $module['enabled'] === false;
    }

    public function enable($slug)
    {
        return $this->set($slug.'::enabled', true);
    }

    public function disable($slug)
    {
        return $this->set($slug.'::enabled', false);
    }

    public function getCache()
    {
        $cachePath = $this->getCachePath();
        if (!$this->files->exists($cachePath)) {
            $content = json_encode(array(), JSON_PRETTY_PRINT);
            $this->files->put($cachePath, $content);
            $this->optimize();
            return collect(json_decode($content, true));
        }
        return collect(json_decode($this->files->get($cachePath), true));
    }

    protected function getCachePath()
    {
        $path = realpath(__DIR__ . '/../../../../../../');
        if(defined('_PATH'))
        {
            $path = _PATH;
        }
        $path .= DIRECTORY_SEPARATOR. getenv('MODULE_CACHE_FOLDER');
        return $path;
    }


}