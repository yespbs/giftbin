<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Discover Events</h1>
        <p class="mt-2 text-sm text-gray-600">Find amazing events happening near you.</p>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Events</label>
                <input 
                    wire:model.live.debounce.500ms="search" 
                    type="text" 
                    id="search"
                    placeholder="Search by title, description, or location..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
            </div>

            <!-- Category Filter -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select wire:model.live="category" id="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort -->
            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select wire:model.live="sort" id="sort" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="start_date">Date (Earliest First)</option>
                    <option value="created_at">Newest First</option>
                    <option value="title">Title (A-Z)</option>
                    <option value="price">Price (Low to High)</option>
                </select>
            </div>
        </div>

        <!-- Status Filter and Clear Filters -->
        <div class="flex flex-wrap items-center justify-between mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-700">Status:</span>
                <label class="inline-flex items-center">
                    <input wire:model.live="status" type="radio" value="" class="form-radio text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Published Only</span>
                </label>
                <label class="inline-flex items-center">
                    <input wire:model.live="status" type="radio" value="published" class="form-radio text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Published</span>
                </label>
                <label class="inline-flex items-center">
                    <input wire:model.live="status" type="radio" value="draft" class="form-radio text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Draft</span>
                </label>
            </div>
            
            @if($search || $status || $category)
                <button 
                    wire:click="clearFilters"
                    class="text-sm text-indigo-600 hover:text-indigo-500 font-medium"
                >
                    Clear Filters
                </button>
            @endif
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading class="flex justify-center py-8">
        <div class="flex items-center space-x-2 text-gray-600">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Loading events...</span>
        </div>
    </div>

    <!-- Events Grid -->
    <div wire:loading.remove>
        @if($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($events as $event)
                    <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
                        <!-- Event Image Placeholder -->
                        <div class="h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <div class="text-white text-center">
                                <div class="text-4xl mb-2">🎉</div>
                                <div class="text-sm opacity-90">{{ $event->category ?? 'Event' }}</div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <!-- Event Title and Status -->
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 truncate flex-1">{{ $event->title }}</h3>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $event->status === 'published' ? 'bg-green-100 text-green-800' : 
                                       ($event->status === 'draft' ? 'bg-gray-100 text-gray-800' : 
                                        ($event->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </div>

                            <!-- Event Description -->
                            @if($event->description)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $event->description }}</p>
                            @endif

                            <!-- Event Details -->
                            <div class="space-y-2 mb-4">
                                <!-- Date -->
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>{{ $event->start_date->format('M j, Y \a\t g:i A') }}</span>
                                </div>

                                <!-- Location -->
                                @if($event->location)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="truncate">{{ $event->location }}</span>
                                    </div>
                                @endif

                                <!-- Price -->
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    <span class="font-medium">
                                        {{ $event->price ? '$' . number_format($event->price, 2) : 'Free' }}
                                    </span>
                                </div>

                                <!-- Organizer -->
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>by {{ $event->user->name }}</span>
                                </div>
                            </div>

                            <!-- View Details Button -->
                            <div class="flex justify-between items-center">
                                <button class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                    View Details →
                                </button>
                                
                                @if($event->max_participants)
                                    <span class="text-xs text-gray-500">
                                        Max: {{ number_format($event->max_participants) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-lg">
                {{ $events->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="mt-2 text-lg font-medium text-gray-900">No events found</h3>
                <p class="mt-2 text-gray-500">
                    @if($search || $status || $category)
                        No events match your current filters. Try adjusting your search criteria.
                    @else
                        There are no events available at the moment. Check back soon!
                    @endif
                </p>
                @if($search || $status || $category)
                    <div class="mt-6">
                        <button 
                            wire:click="clearFilters"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Clear Filters
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
