@extends('layouts.app')

@section('content')
    <div x-data="{ isOpen: false }" class="h-screen flex overflow-hidden">
        <x-main>
            <x-torchlight-code language='php' theme='{{ $paste->color_scheme ?? "github-dark" }}' style="white-space: pre;">
                {!! $paste->code !!}
            </x-torchlight-code>
        </x-main>

        <x-nav>
            <x-nav-item label="New" :link="route('home')" icon="heroicon-o-document-plus" />
            <x-nav-item
                label="Copy"
                type="button"
                icon="heroicon-o-clipboard"
                class="copy-btn"
                data-clipboard-text="{{ $paste->code }}"
            />
            <x-nav-item label="Fork" :link="route('edit', $paste->hash)" icon="heroicon-o-document-duplicate" />
            <x-nav-item label="Raw" :link="route('raw', $paste->hash)" icon="heroicon-o-arrow-top-right-on-square" blank />
        </x-nav>
    </div>
@stop
