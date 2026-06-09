<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Services\MessageService;
use MoonShine\Crud\JsonResponse;
use MoonShine\Support\Enums\ToastType;
use Throwable;

class MessageController extends Controller
{
    public function __construct(
        protected readonly MessageService $messageService
    )
    {
    }

    /**
     * @param MessageRequest $request
     * @return JsonResponse
     */
    public function __invoke(MessageRequest $request): JsonResponse
    {
        try {
            $message = $this->messageService->send($request->validated());

            return JsonResponse::make([
                'message' => $message
            ])
                ->toast('Сообщение отправлено', ToastType::SUCCESS);

        } catch (Throwable $e) {
            return JsonResponse::make()->toast($e->getMessage(), ToastType::ERROR);
        }
    }
}
