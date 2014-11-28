<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Handle the running of DB upgrade patch files
 *
 *
 * @version 1.1
 * @changes 1.1		Allow running of patch files out of order
 *
 */
class dbpatch extends CI_Controller
{
	protected $_table = 'patch_history';
	protected $_key   = '7k1c3gBh';

	private $log_dir;
	private $log_file;
	private $log_latest;

	public function __construct()
	{
		// init parent
		parent::__construct();

		// loads
		$this->load->helper(array(
			'directory',
			'file',
		));

		$this->log_dir = APPPATH . 'logs/sql/';
		$this->log_file = $this->log_dir . 'sql-' . date('Y-m-d_H-i-s') . '.php';
		$this->log_latest = $this->log_dir . 'sql-latest.php';

		// do we have the log dir?
		if ( ! is_dir($this->log_dir))
		{
			mkdir($this->log_dir);
		}

		file_put_contents($this->log_latest, "<?php defined('BASEPATH') or exit('No direct script access allowed');\n\n");
	}

	private function _debug($str = '')
	{
		// ouput the string
		echo "<p>$str</p>\n";
	}

	private function _log($sql = '')
	{
		// do we have the file?
		if ( ! file_exists($this->log_file))
		{
			file_put_contents($this->log_file, "<?php defined('BASEPATH') or exit('No direct script access allowed');\n\n");
		}

		// log the sql
		file_put_contents($this->log_file, $sql . PHP_EOL, FILE_APPEND | LOCK_EX);
		file_put_contents($this->log_latest, $sql . PHP_EOL, FILE_APPEND | LOCK_EX);
	}

	/**
	 * Run the upgrades
	 */
	public function run($key = false)
	{
		if ($key != $this->_key)
		{
			show_404();
		}

		// Check the patch_history table exists
		if ( ! $this->db->table_exists($this->_table))
		{
			// Doesn't exist, create.
			$this->_debug("Creating table {$this->_table}.");

			$sql = "CREATE TABLE `{$this->_table}` (
				`num` smallint(5) unsigned NOT NULL,
				`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`num`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

			$this->db->query($sql);

			// log the sql
			$this->_log($this->db->last_query());

			// log the sql
			$this->db->insert($this->_table, array('num' => 0));
			$this->_log($this->db->last_query());
		}

		$this->_debug("Table exists ({$this->_table}).");

		// Get all patches installed
		$results = $this->db->select('num')->where('num > ', 0)->get($this->_table)->result_array();
		foreach ($results as $row)
		{
			$installed_patches[ $row['num'] ] = TRUE;
		}

		$this->_debug("Total patches already installed: " . count($installed_patches));

		// Get the highest patch level available to install
		$path = FCPATH . '../sql/';
		$files = get_filenames($path);
		asort($files);

		$file_count = 0;
		$file_list = array();		// sorted file list by key of patch number, so it can be oredered properly.

		foreach ($files as $filename)
		{
			// Get patch number from filename
			if ( ! preg_match('/patch([0-9]+).sql/', $filename, $matches)) continue;

			$patch_num = (int) $matches[1];
			$file_list[$patch_num] = $filename;
		}

		// Sort patch file array by key (patch number)
		ksort($file_list);

		$this->_debug("Total patch files: " . count($file_list));

		foreach ($file_list as $patch_num => $filename)
		{
			if ( ! isset($installed_patches[$patch_num]))
			{
				$out = "Running {$filename} ... ";

				$contents = read_file($path . $filename);
				$queries = preg_split('#(?<!\\\\);#', $contents);
				$queries = array_map('trim', $queries);

				foreach ($queries as $sql)
				{
					if (strlen($sql) < 1)
					{
						continue;
					}

					// log the sql
					$this->_log($sql);

					if ( ! $this->db->query($sql))
					{
						$out .= 'Error!<br>';

						$this->_debug($out);
						$this->_debug("<pre><code>$sql</code></pre>");
						exit;
					}
				}

				// insert the id into the db
				$this->db->insert($this->_table, array('num' => $patch_num));

				$out .= 'Done!';
				$this->_debug($out);

				$file_count++;
			}
			else
			{
				$this->_debug("Skipping {$filename}...");
			}
		}

		if ($file_count == 0)
		{
			$this->_debug("No patch files to run. All up to date.");
		}
		else
		{
			$this->_debug("Successfully installed $file_count files.");
		}

		$this->_debug(anchor('/', 'Continue to site.'));
	}
}
