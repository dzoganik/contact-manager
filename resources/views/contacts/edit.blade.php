@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-semibold mb-4">Edit Contact</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('contacts.update', $contact) }}" method="POST" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Name:</label>
            <input type="text" name="name" id="name" value="{{ old('name', $contact->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
        </div>
        <div class="mb-4">
            <label for="surname" class="block text-gray-700 font-bold mb-2">Surname:</label>
            <input type="text" name="surname" id="surname" value="{{ old('surname', $contact->surname) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-bold mb-2">Email:</label>
            <input type="email" name="email" id="email" value="{{ old('email', $contact->email) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Update Contact
            </button>
            <a href="{{ route('contacts.index') }}" class="text-gray-600 hover:text-gray-800 ml-4">Cancel</a>
        </div>
    </form>
@endsection
