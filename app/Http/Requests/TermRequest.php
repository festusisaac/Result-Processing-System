<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TermRequest extends FormRequest
{
    public function authorize()
    {
        // Only authenticated users should be able to manage terms — routes are in auth middleware
        return auth()->check();
    }

    public function rules()
    {
        return [
            'term_name' => 'required|in:FIRST TERM,SECOND TERM,THIRD TERM',
            'term_begins' => 'required|date',
            'term_ends' => 'required|date|after_or_equal:term_begins',
            'school_opens' => 'required|integer|min:0',
            'terminal_duration' => 'nullable|string|max:255',
            'next_term_begins' => 'required|date|after:term_ends',
        ];
    }

    public function messages()
    {
        return [
            'term_name.in' => 'Select a valid term (FIRST TERM, SECOND TERM, or THIRD TERM).',
            'term_ends.after_or_equal' => 'Term end date must be the same or after the begin date.',
            'next_term_begins.after' => 'Next term begins must be after this term ends.',
        ];
    }
}
