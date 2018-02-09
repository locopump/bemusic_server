<?php namespace App\Http\Controllers;

use App;
use App\Playlist;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserPlaylistsController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Playlist
     */
    private $playlist;

    /**
     * PlaylistController constructor.
     *
     * @param Request $request
     * @param Playlist $playlist
     */
    public function __construct(
        Request $request,
        Playlist $playlist
    )
    {
        $this->request = $request;
        $this->playlist = $playlist;
    }

    /**
     * Fetch all playlists user has created or followed.
     *
     * @param integer $userId
     * @return Collection
     */
    public function index($userId)
    {
        $this->authorize('index', [Playlist::class, $userId]);

        return $this->request->user()
            ->playlists()
            ->withCount('tracks')
            ->with(['tracks' => function (BelongsToMany $q) {
                return $q->with('album')->limit(1);
            }, 'editors'])->get();
    }

    /**
     * Follow playlist with currently logged in user.
     *
     * @param int $id
     * @return integer
     */
    public function follow($id)
    {
        $playlist = $this->playlist->findOrFail($id);

        $this->authorize('show', $playlist);

        return $this->request->user()->playlists()->sync([$id], false);
    }

    /**
     * Un-Follow playlist with currently logged in user.
     *
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unfollow($id)
    {
        $playlist = $this->request->user()->playlists()->find($id);

        $this->authorize('show', $playlist);

        if ($playlist) {
            $this->request->user()->playlists()->detach($id);
        }

        return $this->success();
    }
}
