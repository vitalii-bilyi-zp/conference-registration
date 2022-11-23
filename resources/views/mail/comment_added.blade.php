<x-mail::message>
Добрый день, на конференции
<a href="{{ config('frontend.url') . config('frontend.conferences_url') . '/' . $lecture->conference->id }}">{{ $lecture->conference->title }}</a>
пользователь {{ $user->firstname }} {{ $user->lastname }} оставил комментарий на ваш доклад
<a href="{{ config('frontend.url') . config('frontend.lectures_url') . '/' . $lecture->id }}">{{ $lecture->title }}</a>.
</x-mail::message>
