@extends('layouts.app')
@section('title', 'Edit Category')
@section('content')
<div class="max-w-3xl mx-auto py-6">

    <h2 class="text-2xl font-bold mb-6">Edit Category</h2>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block mb-2 font-medium">Category Name</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $category->name) }}"
                       class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                       required>

                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.categories.index') }}"
                   class="px-4 py-2 bg-gray-200 rounded-lg">
                    Cancel
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update
                </button>
            </div>

        </form>
    </div>

</div>
@endsection