<x-mail::message>
Добрый день, на конференцию
<a href="{{ config('frontend.url') . config('frontend.conferences_url') . '/' . $conference->id }}">{{ $conference->title }}</a>
присоединился новый слушатель {{ $user->firstname }} {{ $user->lastname }}.
</x-mail::message>
