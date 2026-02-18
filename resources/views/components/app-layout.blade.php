@extends('layouts.app')

@section('title', $header ?? 'Dashboard Keuangan')

@section('content')
    {{ $slot }}
@endsection
