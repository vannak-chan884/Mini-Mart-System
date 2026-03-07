@extends('layouts.app')

@section('title', 'Edit Expense')

@section('content')

    {{-- ── Page header ─────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.expenses.index') }}"
            class="flex items-center justify-center w-8 h-8 rounded-[9px]
              bg-[rgba(0,0,0,0.04)] dark:bg-[rgba(255,255,255,0.05)]
              border border-[rgba(0,0,0,0.07)] dark:border-[rgba(255,255,255,0.07)]
              text-[#6B7280] dark:text-[#8B909E]
              hover:text-[#1A1A2E] dark:hover:text-[#EEEAE2]
              transition-colors no-underline">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <polyline points="15 18 9 12 15 6" />
            </svg>
        </a>
        <div>
            <h1
                class="font-['Playfair_Display'] text-[20px] font-bold
                   text-[#1A1A2E] dark:text-[#EEEAE2] leading-tight">
                Edit Expense
            </h1>
            <p class="font-['DM_Sans'] text-[12px] text-[#9CA3AF] dark:text-[#555968]">
                Updating
                <span class="font-semibold text-[#6B7280] dark:text-[#8B909E]">
                    {{ $expense->title }}
                </span>
            </p>
        </div>
    </div>

    {{-- ── Form card ────────────────────────────────────────────── --}}
    <div class="max-w-xl">
        <div
            class="bg-white dark:bg-[rgba(255,255,255,0.03)]
                border border-[rgba(0,0,0,0.07)] dark:border-[rgba(255,255,255,0.07)]
                rounded-[16px] overflow-hidden
                shadow-[0_1px_4px_rgba(0,0,0,0.05)] dark:shadow-none">

            {{-- Card header --}}
            <div
                class="flex items-center gap-3 px-6 py-4
                    border-b border-[rgba(0,0,0,0.06)] dark:border-[rgba(255,255,255,0.06)]
                    bg-[rgba(0,0,0,0.01)] dark:bg-[rgba(255,255,255,0.02)]">
                <div
                    class="w-8 h-8 rounded-[9px] flex items-center justify-center text-base flex-shrink-0
                        bg-[rgba(245,158,11,0.1)] dark:bg-[rgba(245,158,11,0.15)]
                        border border-[rgba(245,158,11,0.22)]">
                    ✏️
                </div>
                <div class="min-w-0">
                    <span
                        class="font-['DM_Sans'] text-[11.5px] font-bold uppercase tracking-[0.7px]
                             text-[#9CA3AF] dark:text-[#555968]">
                        Editing Record
                    </span>
                    <span
                        class="font-['IBM_Plex_Mono'] text-[10.5px] text-[#D1D5DB] dark:text-[#374151]
                             ml-2">#{{ $expense->id }}</span>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('admin.expenses.update', $expense->id) }}" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                {{-- Category --}}
                <div class="group">
                    <label for="category_id"
                        class="block font-['DM_Sans'] text-[11px] font-bold uppercase tracking-[0.8px] mb-1.5
                           text-[#6B7280] dark:text-[#555968]
                           group-focus-within:text-[#1D4ED8] dark:group-focus-within:text-[#3B82F6]
                           transition-colors duration-200">
                        Category
                    </label>
                    <div class="relative">
                        <div
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none
                                text-[#9CA3AF] dark:text-[#555968]
                                group-focus-within:text-[#3B82F6] transition-colors duration-200">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <rect x="3" y="3" width="7" height="7" />
                                <rect x="14" y="3" width="7" height="7" />
                                <rect x="14" y="14" width="7" height="7" />
                                <rect x="3" y="14" width="7" height="7" />
                            </svg>
                        </div>
                        <select id="category_id" name="category_id"
                            class="w-full pl-10 pr-9 py-2.5 rounded-[10px] appearance-none
                               font-['DM_Sans'] text-[13.5px]
                               bg-[#F7F8FC] dark:bg-[rgba(255,255,255,0.03)]
                               border border-[rgba(0,0,0,0.08)] dark:border-[rgba(255,255,255,0.07)]
                               text-[#1A1A2E] dark:text-[#EEEAE2]
                               outline-none transition-all duration-200
                               focus:border-[rgba(59,130,246,0.5)] focus:bg-[rgba(59,130,246,0.04)]
                               focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div
                            class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none
                                text-[#9CA3AF] dark:text-[#555968]">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </div>
                    </div>
                    @error('category_id')
                        <p
                            class="mt-1.5 font-['DM_Sans'] text-[11.5px] text-red-500 dark:text-red-400
                               flex items-center gap-1">
                            <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Title --}}
                <div class="group">
                    <label for="title"
                        class="block font-['DM_Sans'] text-[11px] font-bold uppercase tracking-[0.8px] mb-1.5
                           text-[#6B7280] dark:text-[#555968]
                           group-focus-within:text-[#1D4ED8] dark:group-focus-within:text-[#3B82F6]
                           transition-colors duration-200">
                        Title
                    </label>
                    <div class="relative">
                        <div
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none
                                text-[#9CA3AF] dark:text-[#555968]
                                group-focus-within:text-[#3B82F6] transition-colors duration-200">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <line x1="21" y1="6" x2="3" y2="6" />
                                <line x1="15" y1="12" x2="3" y2="12" />
                                <line x1="17" y1="18" x2="3" y2="18" />
                            </svg>
                        </div>
                        <input id="title" name="title" type="text" value="{{ old('title', $expense->title) }}"
                            required
                            class="w-full pl-10 pr-4 py-2.5 rounded-[10px]
                                  font-['DM_Sans'] text-[13.5px]
                                  bg-[#F7F8FC] dark:bg-[rgba(255,255,255,0.03)]
                                  border border-[rgba(0,0,0,0.08)] dark:border-[rgba(255,255,255,0.07)]
                                  text-[#1A1A2E] dark:text-[#EEEAE2]
                                  placeholder:text-[#D1D5DB] dark:placeholder:text-[#374151]
                                  outline-none transition-all duration-200
                                  focus:border-[rgba(59,130,246,0.5)] focus:bg-[rgba(59,130,246,0.04)]
                                  focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]">
                    </div>
                    @error('title')
                        <p
                            class="mt-1.5 font-['DM_Sans'] text-[11.5px] text-red-500 dark:text-red-400
                               flex items-center gap-1">
                            <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Amount + Date --}}
                <div class="grid grid-cols-2 gap-4">

                    {{-- Amount --}}
                    <div class="group">
                        <label for="amount"
                            class="block font-['DM_Sans'] text-[11px] font-bold uppercase tracking-[0.8px] mb-1.5
                               text-[#6B7280] dark:text-[#555968]
                               group-focus-within:text-[#1D4ED8] dark:group-focus-within:text-[#3B82F6]
                               transition-colors duration-200">
                            Amount
                        </label>
                        <div class="relative">
                            <div
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none
                                    font-['IBM_Plex_Mono'] text-[13px] font-bold
                                    text-[#9CA3AF] dark:text-[#555968]
                                    group-focus-within:text-[#3B82F6] transition-colors duration-200">
                                $
                            </div>
                            <input id="amount" name="amount" type="number" step="0.01" min="0"
                                value="{{ old('amount', $expense->amount) }}" required
                                class="w-full pl-7 pr-4 py-2.5 rounded-[10px]
                                      font-['IBM_Plex_Mono'] text-[13.5px]
                                      bg-[#F7F8FC] dark:bg-[rgba(255,255,255,0.03)]
                                      border border-[rgba(0,0,0,0.08)] dark:border-[rgba(255,255,255,0.07)]
                                      text-[#1A1A2E] dark:text-[#EEEAE2]
                                      outline-none transition-all duration-200
                                      focus:border-[rgba(59,130,246,0.5)] focus:bg-[rgba(59,130,246,0.04)]
                                      focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]">
                        </div>
                        @error('amount')
                            <p class="mt-1 font-['DM_Sans'] text-[11px] text-red-500 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Date --}}
                    <div class="group">
                        <label for="date"
                            class="block font-['DM_Sans'] text-[11px] font-bold uppercase tracking-[0.8px] mb-1.5
                               text-[#6B7280] dark:text-[#555968]
                               group-focus-within:text-[#1D4ED8] dark:group-focus-within:text-[#3B82F6]
                               transition-colors duration-200">
                            Date
                        </label>
                        <div class="relative">
                            <div
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none
                                    text-[#9CA3AF] dark:text-[#555968]
                                    group-focus-within:text-[#3B82F6] transition-colors duration-200">
                                <svg width="14" height="14" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="18" rx="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                            </div>
                            <input id="date" name="date" type="date"
                                value="{{ old('date', $expense->date) }}" required
                                class="w-full pl-10 pr-4 py-2.5 rounded-[10px]
                                      font-['DM_Sans'] text-[13.5px]
                                      bg-[#F7F8FC] dark:bg-[rgba(255,255,255,0.03)]
                                      border border-[rgba(0,0,0,0.08)] dark:border-[rgba(255,255,255,0.07)]
                                      text-[#1A1A2E] dark:text-[#EEEAE2]
                                      outline-none transition-all duration-200
                                      focus:border-[rgba(59,130,246,0.5)] focus:bg-[rgba(59,130,246,0.04)]
                                      focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]">
                        </div>
                        @error('date')
                            <p class="mt-1 font-['DM_Sans'] text-[11px] text-red-500 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                {{-- Note --}}
                <div class="group">
                    <label for="note"
                        class="block font-['DM_Sans'] text-[11px] font-bold uppercase tracking-[0.8px] mb-1.5
                           text-[#6B7280] dark:text-[#555968]
                           group-focus-within:text-[#1D4ED8] dark:group-focus-within:text-[#3B82F6]
                           transition-colors duration-200">
                        Note
                        <span
                            class="normal-case tracking-normal font-normal
                                 text-[#D1D5DB] dark:text-[#374151] ml-1">
                            (optional)
                        </span>
                    </label>
                    <textarea id="note" name="note" rows="3" placeholder="Any additional details…"
                        class="w-full px-4 py-2.5 rounded-[10px] resize-none
                                 font-['DM_Sans'] text-[13.5px]
                                 bg-[#F7F8FC] dark:bg-[rgba(255,255,255,0.03)]
                                 border border-[rgba(0,0,0,0.08)] dark:border-[rgba(255,255,255,0.07)]
                                 text-[#1A1A2E] dark:text-[#EEEAE2]
                                 placeholder:text-[#D1D5DB] dark:placeholder:text-[#374151]
                                 outline-none transition-all duration-200
                                 focus:border-[rgba(59,130,246,0.5)] focus:bg-[rgba(59,130,246,0.04)]
                                 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]">{{ old('note', $expense->note) }}</textarea>
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
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                            <polyline points="7 3 7 8 15 8" />
                        </svg>
                        Update Expense
                    </button>
                    <a href="{{ route('admin.expenses.index') }}"
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
