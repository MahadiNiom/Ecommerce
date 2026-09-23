@extends('layouts.app')

@section('content')
    <h1>Dashboard</h1>

    <p>Welcome, {{ $user->name }} ({{ $roles->implode(', ') }}).</p>

    <h2>Overview</h2>
    <ul>
        <li>Products: {{ $productsCount }}</li>
        <li>Variants: {{ $variantsCount }}</li>
        <li>Product variants: {{ $productVariantsCount }}</li>
    </ul>

    @can('manage products')
        <p><a href="{{ route('products.index') }}">Manage products</a></p>
    @endcan
@endsection