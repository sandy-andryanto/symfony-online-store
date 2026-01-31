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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Setting;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\NewsLetter;


#[Route('api/home')]
class HomeController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/ping', methods: ["GET"])]
    public function ping() : JsonResponse
    {
        return new JsonResponse(['status' => true, 'message' => 'Connected Established !!']);
    }

    #[Route('/component', methods: ["GET"])]
    public function component() : JsonResponse
    {
        $categories = $this->em->getRepository(Category::class)
            ->createQueryBuilder('x')
            ->getQuery()
            ->getArrayResult();

        $setting = $this->em->getRepository(Setting::class)->getAll();
        $payload = [
            "setting"=> $setting,
            "categories"=> $categories
        ];
        return new JsonResponse($payload);
    }

    #[Route('/page', methods: ["GET"])]
    public function page() : JsonResponse
    {

        $categories     = $this->em->getRepository(Category::class)->getDisplayed();
        $bestSellers    = $this->em->getRepository(Product::class)->getTopSelling(3, 'x.totalRating');
        $products       = $this->em->getRepository(Product::class)->getDisplayedHome();
        $topSellings    =  $this->em->getRepository(Product::class)->getTopSelling(6, 'x.totalOrder');
        $payload = [
            "categories"   => $categories,
            "products"     => $products,
            "topSellings"  => $topSellings,
            "bestSellers"  => $bestSellers
        ];
        return new JsonResponse($payload);
    }

    #[Route('/newsletter', methods: ["POST"])]
    public function newsletter(Request $request) : JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data["email"];
        $ipAddress = $request->getClientIp();

        $newsletter = new NewsLetter();
        $newsletter->setEmail($email);
        $newsletter->setIpAddress($ipAddress);
        $this->em->persist($newsletter);
        $this->em->flush();

        return new JsonResponse($newsletter);
    }

   
}