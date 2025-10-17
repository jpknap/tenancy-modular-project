@extends('layouts.layout_menu_sidebar')

@section('title', 'Tenant List')

@section('content')
    @foreach($admin->getListableAttributes() as $attribute)
        <div>{{ $attribute }}</div>
    @endforeach
@endsection
