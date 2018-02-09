<?php

use App\Setting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;

class SettingsTableSeeder extends Seeder
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Setting
     */
    private $setting;

    /**
     * SettingsTableSeeder constructor.
     *
     * @param Filesystem $filesystem
     * @param Setting $setting
     */
    public function __construct(Filesystem $filesystem, Setting $setting)
    {
        $this->setting = $setting;
        $this->filesystem = $filesystem;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultSettings = $this->filesystem->getRequire(resource_path('value-lists/default-settings.php'));

        $names = [];

        $defaultSettings = array_map(function($setting) use(&$names) {
            $names[] = $setting['name'];

            $setting['created_at'] = Carbon::now();
            $setting['updated_at'] = Carbon::now();

            //make sure all settings have "private" field to
            //avoid db errors due to different column count
            if ( ! array_key_exists('private', $setting)) {
                $setting['private'] = 0;
            }

            return $setting;
        }, $defaultSettings);

        $existing = $this->setting->whereIn('name', $names)->pluck('name')->toArray();

        //only insert settings that don't already exist in database
        $new = array_filter($defaultSettings, function($setting) use($existing) {
            return ! in_array($setting['name'], $existing);
        });

        $this->setting->insert($new);
    }
}
