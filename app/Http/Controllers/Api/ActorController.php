<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateActor;
use App\Actions\ListActors;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexActorsRequest;
use App\Http\Requests\StoreActorRequest;
use App\Http\Resources\ActorResource;
use App\Services\ActorExtraction\ActorExtractionPrompt;
use Illuminate\Http\JsonResponse;

class ActorController extends Controller
{
    public function index(IndexActorsRequest $request, ListActors $listActors): JsonResponse
    {
        $actors = $listActors
            ->executePaginated($request->perPage(), $request->page())
            ->appends($request->validated());

        return ActorResource::collection($actors)->response();
    }

    public function store(StoreActorRequest $request, CreateActor $createActor): JsonResponse
    {
        $actor = $createActor->execute($request->validated());

        return (new ActorResource($actor))
            ->response()
            ->setStatusCode(201);
    }

    public function promptValidation(ActorExtractionPrompt $prompt): JsonResponse
    {
        return response()->json([
            'message' => $prompt->text(),
        ]);
    }
}

