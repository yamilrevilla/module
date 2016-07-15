<?php

namespace Yrevilla\Modules\Contracts;

interface RepositoryInterface
{
	
	public function all();
	
	public function slugs();
	
	public function where($key, $value);
	
	public function sortBy($key);
	 
	public function sortByDesc($key);

	public function exists($slug);

	public function count();

	public function getManifest($slug);

	public function get($property, $default = null);

	public function set($property, $value);

	public function enabled();

	public function disabled();
	
	public function isEnabled($slug);
	
	public function isDisabled($slug);
	
	public function enable($slug);
	
	public function disable($slug);
	
}