<?php namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ModifyAlbums extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $albumId = $this->route('id');
        $artistId = $this->request->get('artist_id', 0);

        $rules = [
            'name' => [
                'required', 'string', 'min:1', 'max:255',
                Rule::unique('albums')->where('artist_id', $artistId)->ignore($albumId)
            ],
            'artist_id'          => 'required|integer|exists:artists,id',
            'spotify_popularity' => 'integer|min:1|max:100|nullable',
            'release_date'       => 'date|nullable',
            'image'              => 'url|nullable',
        ];

        return $rules;
    }
}
