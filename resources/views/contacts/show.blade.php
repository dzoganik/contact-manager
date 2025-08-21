@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Contact Details</h2>
        <a href="{{ route('contacts.index') }}" class="text-gray-600 hover:text-gray-800">← Back to List</a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="mb-4">
            <strong class="block text-gray-700">Name:</strong>
            <span>{{ $contact->name }}</span>
        </div>
        <div class="mb-4">
            <strong class="block text-gray-700">Surname:</strong>
            <span>{{ $contact->surname }}</span>
        </div>
        <div class="mb-4">
            <strong class="block text-gray-700">Email:</strong>
            <span>{{ $contact->email }}</span>
        </div>
        <div class="mt-6">
            <a href="{{ route('contacts.edit', $contact) }}" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                Edit
            </a>
        </div>
    </div>
@endsection
