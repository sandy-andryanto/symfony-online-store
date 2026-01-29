<?php

/**
* This file is part of the Sandy Andryanto Online Store Website.
*
* @author     Sandy Andryanto <sandy.andryanto.blade@gmail.com>
* @copyright  2025
*
* For the full copyright and license information,
* please view the LICENSE.md file that was distributed
* with this source code.
*/

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Requests\RegisterRequest;
use App\Requests\ForgotPasswordRequest;
use App\Requests\ResetPasswordRequest;
use Faker\Factory as Faker;
use App\Entity\User;
use App\Entity\Activity;
use App\Entity\Authentication;
use DateTime;

#[Route('api/auth')]
class AuthController extends AbstractController
{
    private EntityManagerInterface $em;
    private PasswordHasherFactoryInterface $passwordHasherFactory;

    public function __construct(EntityManagerInterface $em, PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->em = $em;
        $this->passwordHasherFactory = $hasherFactory;
    }

    #[Route('/register', methods: ["POST"])]
    public function register(RegisterRequest $request): JsonResponse
    {
        $errors = $request->validate();

        if($errors)
        {
            return new JsonResponse($errors, 400);
        }

        $input = $request->getInput();
        $email = $input->email;
        $name = $input->name;
        $password = $input->password;
        $passwordConfirm = $input->passwordConfirm;
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(User::class);
        $hash = $passwordHasher->hash($password);
        $faker = Faker::create();
        $token = $faker->uuid();
        $names = explode(" ", $name);

        if($password != $passwordConfirm)
        {
            return new JsonResponse(["message"=>"The password confirmation does not match.!"], 400);
        }

        $user = $this->em->getRepository(User::class)->findByEmail($email);

        if(null !== $user)
        {
            return new JsonResponse(["message"=>"The email has already been taken.!"], 400);
        }

        $user = new User();

        if(count($names) > 1){
            $last_name = array_slice($names, 1, (count($names) - 1));
            $last_name = implode(" ", $last_name);
            $user->setFirstName($names[0]);
            $user->setLastName($last_name);
        }else{
            $user->setFirstName($names[0]);
        }


        $user->setEmail($email);
        $user->setStatus(1);
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($hash);
        $this->em->persist($user);

        $expiredDate = new DateTime();
        $expiredDate->modify('+30 minutes');

        $auth = new Authentication();
        $auth->setUser($user);
        $auth->setType("email-verification");
        $auth->setCredential($email);
        $auth->setExpiredAt($expiredDate);
        $auth->setToken($token);
        $auth->setStatus(0);
        $this->em->persist($auth);

        $this->em->flush();

        $this->em->getRepository(Activity::class)->Create($user, "Sign Up", "Create New User", "Register new user account");

        $payload = [
            'message' => 'You need to confirm your account. We have sent you an activation code, please check your email.',
            'token'=> $token
        ];

        return new JsonResponse($payload, 200);
    }

    #[Route('/confirm/{token}', methods: ["GET"])]
    public function confirm(string $token): JsonResponse
    {
        $confirm = $this->em->getRepository(Authentication::class)->findByToken($token);

        if($confirm == null)
        {
            return new JsonResponse(["message"=> "We can't find a user with that  token is invalid.!"], 400);
        }

        $user = $confirm->getUser();
        $user->setStatus(1);

        $confirm->setStatus(2);
        $confirm->setExpiredAt(new DateTime());

        $this->em->persist($user);
        $this->em->persist($confirm);
        $this->em->flush();

        $this->em->getRepository(Activity::class)->Create($user, "Email Verification", "User Verification", "Confirm new member registration account");
        return new JsonResponse(["message"=> "Your e-mail is verified. You can now login."]);
    }

    #[Route('/email/forgot', methods: ["POST"])]
    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $errors = $request->validate();

        if($errors)
        {
            return new JsonResponse($errors, 400);
        }

        $input = $request->getInput();
        $email = $input->email;
        $user = $this->em->getRepository(User::class)->findByEmail($email);

        if($user == null)
        {
            return new JsonResponse(["message"=>"We can't find a user with that e-mail address."], 400);
        }

        $faker = Faker::create();
        $token = $faker->uuid();
        $expiredDate = new DateTime();
        $expiredDate->modify('+30 minutes');

        $auth = new Authentication();
        $auth->setUser($user);
        $auth->setType("reset-password");
        $auth->setCredential($email);
        $auth->setExpiredAt($expiredDate);
        $auth->setToken($token);
        $auth->setStatus(0);
        $this->em->persist($auth);

        $this->em->getRepository(Activity::class)->Create($user, "Forgot Password", "Sending Reset Password", "Request reset password link");
        $payload = ["message" => "We have e-mailed your password reset link!", "token"=> $token];
        return new JsonResponse($payload, 200);

    }

    #[Route('/email/reset/{token}', methods: ["POST"])]
    public function reset(string $token, ResetPasswordRequest $request): JsonResponse
    {
        $errors = $request->validate();

        if($errors)
        {
            return new JsonResponse($errors, 400);
        }

        $input = $request->getInput();
        $email = $input->email;
        $password = $input->password;
        $passwordConfirm = $input->passwordConfirm;
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(User::class);
        $hash = $passwordHasher->hash($password);

        $user = $this->em->getRepository(User::class)->findByEmail($email);
        if($user == null)
        {
            return new JsonResponse(["message"=>"We can't find a user with that e-mail address."], 400);
        }

        if($password != $passwordConfirm)
        {
            return new JsonResponse(["message"=>"The password confirmation does not match.!"], 400);
        }

        $confirm = $this->em->getRepository(Authentication::class)->findByUserToken($token, $user);

        if(null === $confirm)
        {
            return new JsonResponse(["message"=>"We can't find a user with that e-mail address or password reset token is invalid."], 400);
        }
        
        $user->setPassword($hash);
        $this->em->persist($user);
        $this->em->flush();

        $this->em->getRepository(Activity::class)->Create($user, "Reset Password", "Update Forgot Password", "Reset account password");
        return new JsonResponse(["message"=>"Your password has been reset!"]);

    }

}