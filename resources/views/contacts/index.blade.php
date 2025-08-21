@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Contact List</h2>
        <a href="{{ route('contacts.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add New Contact
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md">
        @if($contacts->isEmpty())
            <p>No contacts found.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Surname</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($contacts as $contact)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $contact->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $contact->surname }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $contact->email }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    
    <div class="mt-4">
        {{ $contacts->links() }}
    </div>
@endsection
