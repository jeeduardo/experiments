<?php

class ModuleCreationBuilder
{
	private $namespace;
	private $module;
	private $fullModule;
	private $rootDirectory;
	private $directories;
	private $version;

	public function __construct()
	{
	}

	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;
	}

	public function setModule($module)
	{
		$this->module = $module;
	}

	public function setFullModule($namespace, $module)
	{
		$this->fullModule = "{$namespace}_{$module}";
	}

	public function setRootDirectory($rootDirectory) {
		$this->rootDirectory = $rootDirectory;
	}

	public function setDirectories($directories)
	{
		$this->directories = array();
		if (!is_array($directories)) {
			$directories = explode(',', $directories);
			foreach ($directories as $key => $directory) {
				$this->directories[$directory] = "{$this->namespace}/{$this->module}/$directory";
			}
		}
	}

	public function setVersion($version)
	{
		$this->version = $version;
	}

	/**
	 * namespace accessor
	 * @return 	string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * fullModule accessor
	 * @return 	string
	 */
	public function getFullModule()
	{
		return $this->fullModule;
	}

	/**
	 * accessor for namespace directory
	 */
	public function getNamespaceDirectory()
	{
		return "{$this->rootDirectory}/{$this->namespace}";
	}

	/**
	 * accessor for module's directory
	 */
	public function getModuleDirectory()
	{
		return "{$this->getNamespaceDirectory()}/{$this->module}";
	}

	public function getDirectories()
	{
		return $this->directories;
	}

	/**
	 * Create the module directories (and files?) here
	 */
	public function createDirectories()
	{
		mkdir("{$this->rootDirectory}/{$this->namespace}");
		mkdir("{$this->rootDirectory}/{$this->namespace}/{$this->module}");

		foreach ($this->directories as $key => $directory) {
			mkdir($this->rootDirectory . '/' . $directory);
		}
		// Inform that it was created? How?
	}

	/**
	 * Create the config necessary (i.e. config.xml and <Namespace>_<Module>.xml)
	 */
	public function createConfig()
	{
		// @todo: WHAT IF etc directory doesn't exist???
		// put config.xml
		$config_xml = "{$this->rootDirectory}/{$this->directories['etc']}/config.xml";
		touch($config_xml);

		// create <Namespace>_<Module>.xml inside the Namespace/Module folder
		$module_xml = "{$this->rootDirectory}/{$this->namespace}/{$this->module}/"
			. "{$this->fullModule}.xml";
		touch($module_xml);

		// edit config.xml
		$fp = fopen($config_xml, 'w');
		fwrite($fp, $this->addConfigXmlContent());
		fclose($fp);

		$fp = fopen($module_xml, 'w');
		fwrite($fp, $this->addModuleXmlContent());
		fclose($fp);

	}

	/**
	 * create zip file of the module
	 */
	public function createModuleZip()
	{
		$zip = new ZipArchive();
		$zip->open("{$this->rootDirectory}/{$this->fullModule}.zip", ZipArchive::CREATE);
		foreach ($this->directories as $directory)
			$zip->addEmptyDir("$directory");
		$zip->addFile("{$this->directories['etc']}/config.xml");
		$zip->addFile("{$this->namespace}/{$this->module}/{$this->fullModule}.xml");
		$zip->close();
	}

	/**
	 * add content to configuration file
	 */
	public function addConfigContent($xmltree)
	{
		$dom = new DOMDocument('1.0', 'utf-8');
		$dom->formatOutput = true;
		$rootkey = key($xmltree);
		if (is_array($xmltree)) {
			$root = $dom->createElement($rootkey);
			foreach ($xmltree[$rootkey] as $key => $node) {
				$child = $dom->createElement($key);
				$root->appendChild($this->buildTree($dom, $node, $child));
			}
		}
		else
			$root = $dom->createElement($rootkey, '');

		$dom->appendChild($root);

		return $dom->saveXML();
	}

	public function addConfigXmlContent()
	{
		// initialize xml tree
        $lmodule = strtolower($this->module);
		$xmltree = array(
			'config' => array(
				'modules' => array(
					$this->fullModule => array('version' => "{$this->version}"),
				),
				'global' => array(
					'helpers' => array(
						$lmodule => array(
							'class' => $this->fullModule . "_Helper",
						),
					),
				),
                'frontend' => array(
                    'routers' => array(
                        $lmodule => array(
                            'use' => 'standard',
                            'args' => array(
                                'module' => $this->fullModule,
                                'frontName' => $lmodule,
                            ),
                        ),
                    ),
                ),
				'default' => ''
			),
		);
		return $this->addConfigContent($xmltree);
	}

	public function addModuleXmlContent()
	{
		$xmltree = array(
			'config' => array(
				'modules' => array(
					$this->fullModule => array(
						'active' => 'true',
						'codePool' => 'local',
					),
				),
			),
		);
		return $this->addConfigContent($xmltree);
	}
	/**
	 * build the XML tree
	 * @param 	DOMDocument $dom
	 * @param 	array/string $treechild
	 * @param 	DOMElement $parent
	 * @return 	DOMElement
	 */
	private function buildTree($dom, $node, $parent)
	{
		if (is_array($node)) {
			foreach ($node as $key => $n) {
				$child = $dom->createElement($key);
				$parent->appendChild($this->buildTree($dom, $n, $child));
			}
		} else {
			$parent->nodeValue = $node;
			return $parent;
		}
		return $parent;
	}

    /**
     * build helper file
     */
    public function createModuleHelper()
    {
        $class = "{$this->fullModule}_Helper_Data";
        $parentClass = "Mage_Core_Helper_Abstract";
        $helperContent = "<?php\n\nclass $class extends $parentClass\n{\n}";
		$helperPhpFile = "{$this->rootDirectory}/{$this->directories['Helper']}/Data.php";

        touch($helperPhpFile);
        // might overwrite existing file (if there are any)!
        $fp = fopen($helperPhpFile, 'w');
        fwrite($fp, $helperContent);
        fclose($fp);

    }
}
