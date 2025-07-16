<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Blogidium API Documentation",
 *     description="Comprehensive API documentation for the Blogidium project.",
 *
 *     @OA\Contact(
 *         name="Nima",
 *         email="nima_8a@yahoo.com",
 *     ),
 * ),
 *
 * @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *         bearerFormat="JWT"
 *     )
 */
abstract class Controller
{
    //
}
