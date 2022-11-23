<x-mail::message>
Добрый день, на конференции
<a href="{{ config('frontend.url') . config('frontend.conferences_url') . '/' . $lecture->conference->id }}">{{ $lecture->conference->title }}</a>
участник {{ $lecture->user->firstname }} {{ $lecture->user->lastname }} с докладом на тему
<a href="{{ config('frontend.url') . config('frontend.lectures_url') . '/' . $lecture->id }}">{{ $lecture->title }}</a>
перенес доклад на другое время.
<br />
<br />
Новое время доклада: {{ $lecture->lecture_start }} - {{ $lecture->lecture_end }}
</x-mail::message>
