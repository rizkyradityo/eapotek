@extends('layouts.app')

@section('title', 'Point of Sale')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">Point of Sale (POS)</h2>
    </div>
    <div class="p-4">
        @livewire('pos-component')
    </div>
</div>
@endsection
