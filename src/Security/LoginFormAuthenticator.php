<?php
//
//namespace App\Security;
//
//use Symfony\Component\HttpFoundation\RedirectResponse;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
//use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
//use Symfony\Component\Security\Core\Exception\AuthenticationException;
//use Symfony\Component\Security\Core\Security;
//use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
//use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
//use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
//use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
//use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
//use Symfony\Component\Security\Http\SecurityRequestAttributes;
//use Symfony\Component\Security\Http\Util\TargetPathTrait;
//
//class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
//{
//    use TargetPathTrait;
//
//        public const LOGIN_ROUTE = 'login';
//
//    public function __construct(private UrlGeneratorInterface $urlGenerator)
//    {
//    }
//
//    public function authenticate(Request $request): Passport
//    {
//        $username = $request->request->get('username', '');
//        $request->getSession()->set(Security::LAST_USERNAME, $username);
//        return new Passport(
//            new UserBadge($username),
//            new PasswordCredentials($request->request->get('password', '')),
//            [
//                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
//            ]
//        );
//    }
//
//    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
//    {
//        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
//            return new RedirectResponse($targetPath);
//        }
//
//        // For example:
//        return new RedirectResponse('/profile');
////        throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
//    }
//
//    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
//    {
//        if ($request->hasSession()) {
//            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
//        }
//        return new RedirectResponse("/");
//    }
//
//    protected function getLoginUrl(Request $request): string
//    {
//        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
//    }
//}
