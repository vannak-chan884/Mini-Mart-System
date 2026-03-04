@extends('layouts.app')
@section('title', 'Categories')
@section('content')
    <div class="max-w-5xl mx-auto py-6">

        <div class="flex justify-between items-center mb-6">
            <h2 class="dark:text-white text-2xl font-bold">Categories</h2>

            <a href="{{ route('admin.categories.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                + Add Category
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4">#</th>
                        <th class="p-4">Name</th>
                        <th class="p-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr class="border-t">
                            <td class="p-4">{{ $loop->iteration }}</td>
                            <td class="p-4">{{ $category->name }}</td>
                            <td class="p-4 text-right space-x-2">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                    class="text-blue-600 hover:underline">Edit</a>

                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('Delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-4 text-center text-gray-500">
                                No categories found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection
