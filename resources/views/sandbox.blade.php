<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Conference registration</title>

        @vite('resources/css/sandbox.css')
    </head>
    <body>
        <form id="token-form">
            <input id="token-input" type="text" required>
            <button id="token-submit" type="submit">Submit</button>
        </div>

        <div class="export-buttons">
            <button id="export-conferences">Export Conferences</button>
            <button id="export-lectures">Export Lectures</button>
            <button id="export-listeners">Export Listeners</button>
            <button id="export-comments">Export Comments</button>
        </div>

        @vite('resources/js/sandbox.js')
    </body>
</html>
