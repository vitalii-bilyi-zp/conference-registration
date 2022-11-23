<x-mail::message>
Добрый день, на конференцию
<a href="{{ config('frontend.url') . config('frontend.conferences_url') . '/' . $lecture->conference->id }}">{{ $lecture->conference->title }}</a>
присоединился новый участник {{ $lecture->user->firstname }} {{ $lecture->user->lastname }} с докладом на тему
<a href="{{ config('frontend.url') . config('frontend.lectures_url') . '/' . $lecture->id }}">{{ $lecture->title }}</a>.
<br />
<br />
Время доклада: {{ $lecture->lecture_start }} - {{ $lecture->lecture_end }}
</x-mail::message>
