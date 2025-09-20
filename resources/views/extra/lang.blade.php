@php
    use Stichoza\GoogleTranslate\GoogleTranslate;
@endphp



@foreach ($langtrans as $langtran)
        '{{ $langtran->key }}'=> '{{ GoogleTranslate::trans($langtran->lang, 'zh-TW') }}',
        <br>
@endforeach