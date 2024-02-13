<?php

namespace App\Http\Controllers;

use App\Http\Requests\AIQuestionRequest;
use App\Jobs\AIAnswer;
use Illuminate\Http\JsonResponse;

class OpenAIController extends Controller
{
    /**
     * Send question to OpenAI.
     *
     * @param AIQuestionRequest $request
     * @return JsonResponse
     */
    public function answerQuestion(AIQuestionRequest $request): JsonResponse
    {
        AIAnswer::dispatch($request->validated('question'));

        return response()->json([
            'message' => 'The question was successfully sent.',
        ]);
    }
}
