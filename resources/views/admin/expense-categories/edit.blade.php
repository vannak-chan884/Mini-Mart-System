@extends('layouts.app')

@section('title', 'Edit Expense Category')

@section('content')

{{-- ── Page header ─────────────────────────────────────────── --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.expense-categories.index') }}"
       class="flex items-center justify-center w-8 h-8 rounded-[9px]
              bg-[rgba(0,0,0,0.04)] dark:bg-[rgba(255,255,255,0.05)]
              border border-[rgba(0,0,0,0.07)] dark:border-[rgba(255,255,255,0.07)]
              text-[#6B7280] dark:text-[#8B909E]
              hover:text-[#1A1A2E] dark:hover:text-[#EEEAE2]
              transition-colors no-underline">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </a>
    <div>
        <h1 class="font-['Playfair_Display'] text-[20px] font-bold
                   text-[#1A1A2E] dark:text-[#EEEAE2] leading-tight">
            Edit Category
        </h1>
        <p class="font-['DM_Sans'] text-[12px] text-[#9CA3AF] dark:text-[#555968]">
            Updating
            <span class="font-semibold text-[#6B7280] dark:text-[#8B909E]">
                {{ $expense_category->name }}
            </span>
        </p>
    </div>
</div>

{{-- ── Form card ────────────────────────────────────────────── --}}
<div class="max-w-md">
    <div class="bg-white dark:bg-[rgba(255,255,255,0.03)]
                border border-[rgba(0,0,0,0.07)] dark:border-[rgba(255,255,255,0.07)]
                rounded-[16px] overflow-hidden
                shadow-[0_1px_4px_rgba(0,0,0,0.05)] dark:shadow-none">

        {{-- Card header --}}
        <div class="flex items-center gap-3 px-6 py-4
                    border-b border-[rgba(0,0,0,0.06)] dark:border-[rgba(255,255,255,0.06)]
                    bg-[rgba(0,0,0,0.01)] dark:bg-[rgba(255,255,255,0.02)]">
            <div class="w-8 h-8 rounded-[9px] flex items-center justify-center text-base flex-shrink-0
                        bg-[rgba(245,158,11,0.1)] dark:bg-[rgba(245,158,11,0.15)]
                        border border-[rgba(245,158,11,0.22)]">
                ✏️
            </div>
            <div>
                <span class="font-['DM_Sans'] text-[11.5px] font-bold uppercase tracking-[0.7px]
                             text-[#9CA3AF] dark:text-[#555968]">
                    Editing Category
                </span>
                <span class="font-['IBM_Plex_Mono'] text-[10.5px]
                             text-[#D1D5DB] dark:text-[#374151] ml-2">
                    #{{ $expense_category->id }}
                </span>
            </div>
        </div>

        <form action="{{ route('admin.expense-categories.update', $expense_category->id) }}"
              method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Category name --}}
            <div class="group">
                <label for="name"
                    class="block font-['DM_Sans'] text-[11px] font-bold uppercase tracking-[0.8px] mb-1.5
                           text-[#6B7280] dark:text-[#555968]
                           group-focus-within:text-[#1D4ED8] dark:group-focus-within:text-[#3B82F6]
                           transition-colors duration-200">
                    Category Name
                </label>
                <div class="relative">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none
                                text-[#9CA3AF] dark:text-[#555968]
                                group-focus-within:text-[#3B82F6] transition-colors duration-200">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7"/>
                            <rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                        </svg>
                    </div>
                    <input id="name" name="name" type="text"
                           value="{{ old('name', $expense_category->name) }}"
                           required autofocus
                           class="w-full pl-10 pr-4 py-2.5 rounded-[10px]
                                  font-['DM_Sans'] text-[13.5px]
                                  bg-[#F7F8FC] dark:bg-[rgba(255,255,255,0.03)]
                                  border border-[rgba(0,0,0,0.08)] dark:border-[rgba(255,255,255,0.07)]
                                  text-[#1A1A2E] dark:text-[#EEEAE2]
                                  outline-none transition-all duration-200
                                  focus:border-[rgba(59,130,246,0.5)] focus:bg-[rgba(59,130,246,0.04)]
                                  focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]">
                </div>
                @error('name')
                    <p class="mt-1.5 font-['DM_Sans'] text-[11.5px] text-red-500 dark:text-red-400
                               flex items-center gap-1">
                        <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Divider --}}
            <div class="h-px bg-[rgba(0,0,0,0.06)] dark:bg-[rgba(255,255,255,0.05)]"></div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[10px]
                           font-['DM_Sans'] text-[13px] font-bold text-white
                           bg-gradient-to-br from-[#003087] to-[#3B82F6]
                           shadow-[0_3px_12px_rgba(0,48,135,0.3)]
                           hover:-translate-y-px hover:shadow-[0_6px_18px_rgba(0,48,135,0.4)]
                           active:translate-y-0 transition-all duration-150 border-0 cursor-pointer">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Update Category
                </button>
                <a href="{{ route('admin.expense-categories.index') }}"
                   class="font-['DM_Sans'] text-[13px] font-semibold
                          text-[#6B7280] dark:text-[#8B909E]
                          hover:text-[#1A1A2E] dark:hover:text-[#EEEAE2]
                          transition-colors no-underline">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>

@endsection