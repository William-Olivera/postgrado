@extends('layouts.app')

@section('title', 'Bienvenido')

@section('content')
<script>
    window.location.href = "{{ route('login') }}";
</script>
@endsection