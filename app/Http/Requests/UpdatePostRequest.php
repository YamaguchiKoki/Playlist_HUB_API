<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'updatedSongs' => 'nullable|array',
            'updatedSongs.*.id' => 'exists:songs,id', // SongのIDがデータベースに存在することを確認
            'updatedSongs.*.title' => 'required|string|max:255',
            'updatedSongs.*.artist' => 'required|string|max:255',
            'updatedSongs.*.url' => 'nullable|string|max:255',
            'updatedSongs.*.platform' => 'nullable|integer|max:255',

            'newSongs' => 'nullable|array',
            'newSongs.*.title' => 'required|string|max:255', // 新規Songに対するバリデーションルール
            'newSongs.*.artist' => 'required|string|max:255',
            'newSongs.*.url' => 'nullable|string|max:255',
            'newSongs.*.platform' => 'nullable|integer|max:255',

            'deletedSongIds' => 'nullable|array',
            'deletedSongIds.*' => 'exists:songs,id', // 削除するSongのIDがデータベースに存在することを確認
        ];
    }
}
