<x-mail::message>
Добрый день, на конференции
<a href="{{ config('frontend.url') . config('frontend.conferences_url') . '/' . $conference->id }}">{{ $conference->title }}</a>
был удален ваш доклад администрацией.
</x-mail::message>
