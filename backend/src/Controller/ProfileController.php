<?php


/**
* This file is part of the Sandy Andryanto Online Store Website.
*
* @author     Sandy Andryanto <sandy.andryanto.official@gmail.com>
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
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Requests\ProfileUpdateRequest;
use App\Requests\ChangePasswordRequest;
use App\Entity\User;
use App\Entity\Activity;

#[Route('api/profile')]
class ProfileController extends AbstractController
{
    private EntityManagerInterface $em;
    private PasswordHasherFactoryInterface $passwordHasherFactory;

    public function __construct(EntityManagerInterface $em, PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->em = $em;
        $this->passwordHasherFactory = $hasherFactory;
    }

    #[Route('/detail', methods: ["GET"])]
    public function detail() : JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $result = $this->em->getRepository(User::class)->findByUserId($user);
        return new JsonResponse($result);
    }

    #[Route('/update', methods: ["POST"])]
    public function update(ProfileUpdateRequest $request) : JsonResponse
    {
        $errors = $request->validate();

        if($errors)
        {
            return new JsonResponse($errors, 400);
        }

        /** @var User $user */
        $user       = $this->getUser();
        $user_id    = $user->getId();
        $input      = $request->getInput();
        $email      = $input->email;
        $phone      = $input->phone;
        $firstName  = $input->firstName;
        $lastName   = $input->lastName;
        $gender     = $input->gender;
        $country    = $input->country;
        $zipCode    = $input->zipCode;
        $address    = $input->address;
        $city       = $input->city;
        $checkEmail = $this->em->getRepository(User::class)->findByEmail($email, $user_id);
        $checkPhone = $this->em->getRepository(User::class)->findByPhone($phone, $user_id);

        if(null !== $checkEmail)
        {
            return new JsonResponse(["message"=>"The email has already been taken.!"], 400);
        }

        if(null !== $checkPhone)
        {
            return new JsonResponse(["message"=>"The phone number has already been taken.!"], 400);
        }

        $user->setEmail($email);
        $user->setPhone($phone);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setGender($gender);
        $user->setCountry($country);
        $user->setCity($city);
        $user->setAddress($address);
        $user->setAddress($zipCode);
        
        $this->em->persist($user);
        $this->em->flush();

        $this->em->getRepository(Activity::class)->Create($user, "Update Profile", "Change Profile Account", "Edit user profile account");
        return new JsonResponse(["message"=> "Your user profile has been changed !!"]);
    }

    #[Route('/password', methods: ["POST"])]
    public function password(ChangePasswordRequest $request) : JsonResponse
    {
        $errors = $request->validate();

        if($errors)
        {
            return new JsonResponse($errors, 400);
        }

        /** @var User $user */
        $user = $this->getUser();

        $input = $request->getInput();
        $currentPassword = $input->curentPassword;
        $password = $input->password;
        $passwordConfirm = $input->passwordConfirm;
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(User::class);
        $hash = $passwordHasher->hash($password);

        if($password != $passwordConfirm)
        {
            return new JsonResponse(["message"=>"The password confirmation does not match.!"], 400);
        }
        
        $hasher = $this->passwordHasherFactory->getPasswordHasher(User::class);
        $validPassword = $hasher->verify($user->getPassword(), $currentPassword);

        if(!$validPassword)
        {
            return new JsonResponse(["message"=>"Your password was not updated, since the provided current password does not match.!!"], 400);
        }

        
        $user->setPassword($hash);
        $this->em->persist($user);
        $this->em->flush();

        $this->em->getRepository(Activity::class)->Create($user, "Change Password", "Change Current Password", "Change new password account");

        return new JsonResponse(["message"=> "Your password has been changed!!"]);
    }

    #[Route('/activity', methods: ["GET"])]
    public function history(Request $request) : JsonResponse
    {
        $user = $this->getUser();
        $request = $request->query->all();
        $data = $this->em->getRepository(Activity::class)->findListByUser($request, $user);
        return new JsonResponse($data);
    }

    #[Route('/upload', methods: ["POST"])]
    public function upload(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $image = $user->getImage();
       
        if($request->files->get('file_image'))
        {
            $uploadPath = $this->getParameter('kernel.project_dir') . '/public/uploads'; 
            if(!is_dir($uploadPath)){
                @mkdir($uploadPath);
            }

            if(!is_null($user->getImage())){
                $file_path_current = $this->getParameter('kernel.project_dir') . '/public/'.$user->getImage(); 
                if(file_exists($file_path_current)){
                    @unlink($file_path_current);
                }
            }

            $file = $request->files->get('file_image');
            $newFileName = md5(uniqid()) . '.' . $file->guessExtension();
            $move = $file->move($uploadPath, $newFileName);

            if($move)
            {
                $user->setImage("uploads/".$newFileName);
                $this->em->persist($user);
                $this->em->flush();
                $this->em->getRepository(Activity::class)->Create($user, "Upload Image", "Change Profile Image", "Upload new user profile image");
                $image = $user->getImage(); 
            }

        }

        return new JsonResponse(["message"=> "Your profile image has been changed", "data"=> $image]);
    }

}