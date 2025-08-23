<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/events",
     *     tags={"Events"},
     *     summary="List all events",
     *     description="Get paginated list of events with optional filters",
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by event status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"draft", "published", "cancelled", "completed"})
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="upcoming",
     *         in="query",
     *         description="Filter upcoming events only",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in event titles",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedEvents")
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with('user');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter upcoming events
        if ($request->boolean('upcoming')) {
            $query->upcoming();
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $events = $query->paginate($request->get('per_page', 15));

        return response()->json($events);
    }

    /**
     * @OA\Post(
     *     path="/events",
     *     tags={"Events"},
     *     summary="Create a new event",
     *     description="Create a new event (authentication required)",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "start_date"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Laravel Workshop"),
     *             @OA\Property(property="description", type="string", example="Learn Laravel basics"),
     *             @OA\Property(property="location", type="string", maxLength=255, example="San Francisco"),
     *             @OA\Property(property="start_date", type="string", format="date-time", example="2024-12-25T10:00:00Z"),
     *             @OA\Property(property="end_date", type="string", format="date-time", example="2024-12-25T18:00:00Z"),
     *             @OA\Property(property="price", type="number", format="float", minimum=0, example=99.99),
     *             @OA\Property(property="max_participants", type="integer", minimum=1, example=50),
     *             @OA\Property(property="status", type="string", enum={"draft", "published", "cancelled", "completed"}, example="draft"),
     *             @OA\Property(property="category", type="string", maxLength=255, example="Technology"),
     *             @OA\Property(property="metadata", type="object", example={"featured": true})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Event created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date|after:now',
            'end_date' => 'nullable|date|after:start_date',
            'price' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'status' => ['nullable', Rule::in(['draft', 'published', 'cancelled', 'completed'])],
            'category' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ]);

        $validated['user_id'] = $request->user()->id;

        $event = Event::create($validated);
        $event->load('user');

        return response()->json($event, 201);
    }

    /**
     * @OA\Get(
     *     path="/events/{id}",
     *     tags={"Events"},
     *     summary="Get event by ID",
     *     description="Get a specific event by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Event ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Event not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Event]."))
     *     )
     * )
     */
    public function show(Event $event): JsonResponse
    {
        $event->load('user');
        return response()->json($event);
    }

    /**
     * @OA\Put(
     *     path="/events/{id}",
     *     tags={"Events"},
     *     summary="Update an event",
     *     description="Update an existing event (owner only)",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Event ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", maxLength=255, example="Updated Laravel Workshop"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="location", type="string", maxLength=255, example="Los Angeles"),
     *             @OA\Property(property="start_date", type="string", format="date-time", example="2024-12-26T10:00:00Z"),
     *             @OA\Property(property="end_date", type="string", format="date-time", example="2024-12-26T18:00:00Z"),
     *             @OA\Property(property="price", type="number", format="float", minimum=0, example=149.99),
     *             @OA\Property(property="max_participants", type="integer", minimum=1, example=100),
     *             @OA\Property(property="status", type="string", enum={"draft", "published", "cancelled", "completed"}, example="published"),
     *             @OA\Property(property="category", type="string", maxLength=255, example="Education"),
     *             @OA\Property(property="metadata", type="object", example={"featured": false})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - You can only update your own events",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Event not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Event]."))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        // Check if user owns the event
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'nullable|date|after:start_date',
            'price' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'status' => ['nullable', Rule::in(['draft', 'published', 'cancelled', 'completed'])],
            'category' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ]);

        $event->update($validated);
        $event->load('user');

        return response()->json($event);
    }

    /**
     * @OA\Delete(
     *     path="/events/{id}",
     *     tags={"Events"},
     *     summary="Delete an event",
     *     description="Delete an existing event (owner only)",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Event ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event deleted successfully",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Event deleted successfully"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - You can only delete your own events",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthorized"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Event not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Event]."))
     *     )
     * )
     */
    public function destroy(Request $request, Event $event): JsonResponse
    {
        // Check if user owns the event
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }
}
