@extends('layouts.app')

@section('title', 'Add Category')

@section('content')
    <div class="max-w-5xl mx-auto py-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Category Name
                    </label>
                    <input type="text" name="name" id="name" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:text-white" 
                        required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.categories.index') }}" class="text-gray-600 hover:underline">Cancel</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Save Category
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection