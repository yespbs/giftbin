<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Events')]
class EventsList extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $status = '';

    #[Url]
    public $category = '';

    #[Url]
    public $sort = 'start_date';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'status', 'category']);
        $this->resetPage();
    }

    public function getEventsProperty()
    {
        $query = Event::with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('location', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->category, function ($query) {
                $query->where('category', $this->category);
            });

        // Default to published events if no status filter
        if (!$this->status) {
            $query->where('status', 'published');
        }

        return $query->orderBy($this->sort, $this->sort === 'start_date' ? 'asc' : 'desc')
                    ->paginate(12);
    }

    public function getCategoriesProperty()
    {
        return Event::distinct()
                   ->whereNotNull('category')
                   ->pluck('category')
                   ->sort()
                   ->values();
    }

    public function render()
    {
        return view('livewire.events.events-list', [
            'events' => $this->events,
            'categories' => $this->categories,
        ]);
    }
}
