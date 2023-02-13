<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\ValidatorService;
use App\Dto\ResetUserRequestDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\MailNotification;

class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public const SUCCESS_RESET_PASSWORD = 'Вам на почту отправлено письмо с ссылкой на восстановление пароля';

    /**
     * @var ResetPasswordHelperInterface
     */
    private $resetPasswordHelper;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorService
     */
    private $validatorService;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @param ResetPasswordHelperInterface $resetPasswordHelper
     * @param SerializerInterface $serializer
     * @param ValidatorService $validatorService
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        ResetPasswordHelperInterface $resetPasswordHelper,
        SerializerInterface $serializer,
        ValidatorService $validatorService,
        MessageBusInterface $messageBus
    ) {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->serializer = $serializer;
        $this->validatorService = $validatorService;
        $this->messageBus = $messageBus;
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset-password/reset/{token}", name="app_reset_password")
     */
    public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder, string $token = null): Response
    {

        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            throw $this->createNotFoundException($e->getReason());
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode the plain password, and set it.
            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return new Response('Пароль изменен');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    /**
     * @param string $email
     */
    private function processSendingPasswordResetEmail(string $email)
    {
        /**
         * @var User $user
         */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return;
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return;
        }

        $this->messageBus->dispatch(new MailNotification(
            $user->getEmail(),
            'Your password reset request',
            'reset_password/email.html.twig',
            ['resetToken' => $resetToken]
        ));

        $this->setTokenObjectInSession($resetToken);
    }

    /**
     * Восстановить пароль пользователя
     * @SWG\Parameter(
     *     name="body",
     *     type="string",
     *     in="body",
     *     required=true,
     *     @Nelmio\Model(type=ResetUserRequestDto::class)
     * )
     * @SWG\Response(
     *   response="200",
     *   description="Успешный ответ",
     *   @SWG\Schema(
     *      @SWG\Property(property="message", type="string", enum={self::SUCCESS_RESET_PASSWORD})
     *   )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetUser(Request $request): JsonResponse
    {
        $content = $request->getContent();

        /**
         * @var ResetUserRequestDto $resetUserRequest
         */
        $resetUserRequest = $this->serializer->deserialize($content, ResetUserRequestDto::class, 'json');
        $this->validatorService->validateDto($resetUserRequest);

        $this->processSendingPasswordResetEmail($resetUserRequest->getEmail());

        return new JsonResponse([
            'message' => self::SUCCESS_RESET_PASSWORD
        ]);
    }
}
