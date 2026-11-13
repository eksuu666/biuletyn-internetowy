<?php

namespace Modules\Comment\App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Article\App\Models\Article;

class CommentRequest extends FormRequest
{
    public const MAX_LENGTH = 2000;

    protected $errorBag = 'discussion';

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if (! Auth::check()) {
            $guest_rules = [
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|string|email|max:255',
            ];
        }

        $rules = [
            'commentable_type' => ['required', Rule::in([Article::class])],
            'commentable_id' => 'required|integer|min:1',
            'comment' => 'required|string|max:'.self::MAX_LENGTH,
        ];

        return array_merge($guest_rules ?? [], $rules);
    }

    public function attributes(): array
    {
        return [
            'guest_name' => 'nazwa',
            'guest_email' => 'adres e-mail',
            'commentable_type' => 'typ publikacji',
            'commentable_id' => 'publikacja',
            'comment' => 'treść komentarza',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function getRedirectUrl(): string
    {
        $article = Article::publiclyVisible()->find($this->integer('commentable_id'));

        return $article ? $article->getUrl().'#comments' : parent::getRedirectUrl();
    }
}
