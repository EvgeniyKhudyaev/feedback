<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Handle access denied exception by returning a 403 response with custom template.
     *
     * @param Request $request
     * @param AccessDeniedException $accessDeniedException
     * @return Response
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException): Response
    {
        $content = $this->twig->render('security/access_denied.html.twig');

        return new Response($content, Response::HTTP_FORBIDDEN);
    }
}
