<?php

class ModuleCreationDirector
{
	protected $builder;

	protected $params;

	public function __construct(ModuleCreationBuilder $builder, array $params)
	{
		$this->builder = $builder;
		$this->params = $params;
		if (!isset($this->params['directories'])) 
			$this->params['directories'] = 'Block,controllers,etc,Helper,Model,sql';
	}

	public function buildModule()
	{
		$this->builder->setNamespace($this->params['namespace']);
		$this->builder->setModule($this->params['module']);
		$this->builder->setFullModule($this->params['namespace'], $this->params['module']);
		$this->builder->setRootDirectory(dirname(__FILE__)); // for NOW
		$this->builder->setDirectories($this->params['directories']);
		$this->builder->setVersion($this->params['version']);
		$this->builder->createDirectories();
		$this->builder->createConfig();
	}

	public function buildModuleZip()
	{
		$this->builder->createModuleZip();
	}

    public function buildModuleHelper()
    {
        $this->builder->createModuleHelper();
    }
}
