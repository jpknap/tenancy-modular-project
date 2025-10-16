@extends('layouts.layout_menu_sidebar')

@section('title', 'Tenant List')

@section('side-menu')
    <h1> Menu lateral</h1>
@endsection

@section('top-bar')
    <h1> barra superior </h1>
@endsection

@section('content')
    @foreach($admin->getListableAttributes() as $attribute)
        <div>{{ $attribute }}</div>
    @endforeach
@endsection
