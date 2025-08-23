<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Giftbin API",
 *     version="1.0.0",
 *     description="API documentation for Giftbin event management system",
 *     @OA\Contact(
 *         email="admin@giftbin.test"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="https://giftbin.test/api",
 *     description="Giftbin API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Laravel Sanctum token authentication"
 * )
 * 
 * @OA\Schema(
 *     schema="Event",
 *     type="object",
 *     required={"id", "title", "start_date", "user_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Laravel Conference 2024"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Annual Laravel community conference"),
 *     @OA\Property(property="location", type="string", nullable=true, example="San Francisco, CA"),
 *     @OA\Property(property="start_date", type="string", format="date-time", example="2024-10-15T10:00:00Z"),
 *     @OA\Property(property="end_date", type="string", format="date-time", nullable=true, example="2024-10-15T18:00:00Z"),
 *     @OA\Property(property="price", type="number", format="float", nullable=true, example=99.99),
 *     @OA\Property(property="max_participants", type="integer", nullable=true, example=500),
 *     @OA\Property(property="status", type="string", enum={"draft", "published", "cancelled", "completed"}, example="published"),
 *     @OA\Property(property="category", type="string", nullable=true, example="Technology"),
 *     @OA\Property(property="metadata", type="object", nullable=true),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", example="john@example.com")
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "name", "email"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="john@example.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="PaginatedEvents",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Event")
 *     ),
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="total", type="integer", example=100),
 *     @OA\Property(property="last_page", type="integer", example=7),
 *     @OA\Property(property="from", type="integer", example=1),
 *     @OA\Property(property="to", type="integer", example=15)
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\AdditionalProperties(
 *             type="array",
 *             @OA\Items(type="string")
 *         ),
 *         example={"title": {"The title field is required."}}
 *     )
 * )
 */
abstract class Controller
{
    //
}
