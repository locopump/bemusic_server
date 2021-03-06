<?php namespace App\Services\Providers;

use App;
use App\Services\Settings;

class ProviderResolver
{
    /**
     * Settings service instance.
     *
     * @var Settings
     */
    private $settings;

    /**
     * Default data type to data provider map.
     *
     * @var array
     */
    private $defaults = [
        'artist' => 'local',
        'album' => 'local',
        'search' => 'local',
        'audio_search' => 'youtube',
        'new_releases' => 'local',
        'top_albums' => 'local',
        'top_tracks' => 'local',
        'genres' => 'local',
        'radio' => 'local'
    ];

    /**
     * Create new ProviderResolver instance.
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Resolve correct data provider for given data type.
     *
     * @param string $type
     * @return mixed
     */
    public function get($type)
    {
        $provider = $this->getProviderNameFor($type);

        $type = ucfirst(camel_case($type));
        $namespace = 'App\Services\Providers\\' . $provider . '\\' . $provider . $type;

        return App::make($namespace);
    }

    /**
     * Get user specified or default provider for given data type.
     *
     * @param $type
     * @return string
     */
    public function getProviderNameFor($type)
    {
        $type = $this->settings->get($type . '_provider', $this->defaults[$type]);
        return ucfirst(camel_case($type));
    }

}