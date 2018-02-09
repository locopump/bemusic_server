<?php namespace App\Http\Middleware;

use App\Services\Mail\MailTemplates;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class RestrictDemoSiteFunctionality
{
    /**
     * @var MailTemplates
     */
    private $mailTemplates;

    /**
     * Routes that are forbidden on demo site.
     *
     * @var array
     */
    private $forbiddenRoutes = [
        //default
        ['method' => 'POST', 'name' => 'settings'],
        ['method' => 'POST', 'name' => 'admin/appearance'],
        ['method' => 'DELETE', 'name' => 'users/delete-multiple'],
        ['method' => 'DELETE', 'name' => 'groups/*'],
        ['method' => 'DELETE', 'name' => 'pages'],
        ['method' => 'DELETE', 'name' => 'admin/localizations/*'],
        ['method' => 'PUT', 'name' => 'admin/localizations/*'],
        ['method' => 'PUT', 'name' => 'mail-templates/*'],
        ['method' => 'POST', 'name' => 'cache/clear'],
        ['method' => 'POST', 'name' => 'users/{id}/password/change'],
        ['method' => 'POST', 'name' => 'groups/{id}/add-users'],
        ['method' => 'POST', 'name' => 'groups/{id}/remove-users'],

        //artists
        ['method' => 'DELETE', 'name' => 'artists'],
        ['method' => 'PUT', 'name' => 'artists/{id}'],
        ['method' => 'POST', 'name' => 'artists'],

        //albums
        ['method' => 'DELETE', 'name' => 'albums'],
        ['method' => 'PUT', 'name' => 'albums/{id}'],
        ['method' => 'POST', 'name' => 'albums'],

        //tracks
        ['method' => 'DELETE', 'name' => 'tracks'],
        ['method' => 'PUT', 'name' => 'tracks/{id}'],
        ['method' => 'POST', 'name' => 'tracks'],

        //lyrics
        ['method' => 'DELETE', 'name' => 'lyrics'],
        ['method' => 'PUT', 'name' => 'lyrics/{id}'],
        ['method' => 'POST', 'name' => 'lyrics'],

        //playlists
        ['method' => 'DELETE', 'name' => 'playlists'],

        //prevent adding/removing groups and permissions from special users
        ['method' => 'POST', 'name' => 'users/{id}'],
        ['method' => 'PUT', 'name' => 'users/{id}'],
        ['method' => 'DELETE', 'name' => 'users/{id}'],
        ['method' => 'POST', 'name' => 'users/{id}/groups/attach'],
        ['method' => 'POST', 'name' => 'users/{id}/groups/detach'],
    ];

    /**
     * RestrictDemoSiteFunctionality constructor.
     *
     * @param MailTemplates $mailTemplates
     */
    public function __construct(MailTemplates $mailTemplates)
    {
        $this->mailTemplates = $mailTemplates;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('site.demo') && $this->shouldForbidRequest($request)) {
            return response(['message' => "You can't do that on demo site."], 403);
        }

        if (config('site.demo') && $request->route()->uri() === 'secure/settings') {
            return $this->manglePrivateSettings($next($request));
        }

        return $next($request);
    }

    /**
     * Check if specified request should be forbidden on demo site.
     *
     * @param Request $request
     * @return bool
     */
    private function shouldForbidRequest(Request $request)
    {
        if ($this->shouldForbidTempleRenderRequest($request)) {
            return true;
        }

        $method = $request->method();
        $uri = str_replace('secure/', '', $request->route()->uri());

        foreach ($this->forbiddenRoutes as $route) {
            if ($method === $route['method'] && $uri === $route['name']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if current request is for mail template render and if it should be denied.
     *
     * @param Request $request
     * @return bool
     */
    private function shouldForbidTempleRenderRequest(Request $request)
    {
        if ($request->is('*mail-templates/render*')) {
            $defaultContents = $this->mailTemplates->getContents($request->get('file_name'), 'default');
            $defaultContents = $defaultContents[$request->get('type')];

            //only allow mail template preview to be rendered on demo site if its contents
            //have not been changed, to prevent user from executing random php code
            if ($defaultContents !== $request->get('contents')) return true;
        }
    }

    /**
     * Mangle settings values, so they are not visible on demo site.
     *
     * @param Response $response
     * @return Response
     */
    private function manglePrivateSettings(Response $response)
    {
        $serverKeys = ['google_id', 'google_secret', 'twitter_id', 'twitter_secret', 'facebook_id',
            'facebook_secret', 'spotify_id', 'spotify_secret', 'lastfm_api_key', 'soundcloud_api_key',
            'discogs_id', 'discogs_secret', 'sentry_dns', 'mailgun_secret', 'sentry_dsn'
        ];

        $clientKeys = ['youtube_api_key', 'logging.sentry_public', 'analytics.google_id'];

        $settings = json_decode($response->getContent(), true);

        $random = str_random(40);

        foreach ($serverKeys as $key) {
            if (isset($settings['server'][$key])) {
                $settings['server'][$key] = $random;
            }
        }

        foreach ($clientKeys as $key) {
            if (isset($settings['client'][$key])) {
                $settings['client'][$key] = $random;
            }
        }

        $response->setContent(json_encode($settings));

        return $response;
    }
}
