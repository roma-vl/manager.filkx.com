<?php

namespace App\Controller;

use App\Infrastructure\Inertia\InertiaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
abstract class BaseController extends AbstractController
{
    protected function validateCommand(object $command, ValidatorInterface $validator): array
    {
        $violations = $validator->validate($command);
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }
        return $errors;
    }

    protected function renderWithErrors(
        Request $request,
        InertiaService $inertia,
        string $component,
        array $errors,
        array $additionalProps = [],
        string $url = null
    ): Response {
        $props = array_merge(
            ['errors' => $errors],
            $additionalProps
        );

        return $inertia->withErrors($errors)->render($request, $component, $props);
    }



    protected function addFlashSuccessAndRedirect(InertiaService $inertia, string $route, string $message): Response
    {
        $this->addFlash('success', $message);
        return $inertia->redirect($route);
    }
}
