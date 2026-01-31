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
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;

#[Route('api/shop')]
class ShopController extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/filter', methods: ["GET"])]
    public function filter()
    {
        $categories = $this->em->getRepository(Product::class)->getGroupByCategory();
        $brands = $this->em->getRepository(Product::class)->getGroupByBrand();
        $topSellings  = $this->em->getRepository(Product::class)->getTopSelling(6, 'x.totalOrder');
        $payload = ["categories" => $categories, "brands" => $brands, "tops" => $topSellings];
        return new JsonResponse($payload);
    }

    #[Route('/list', methods: ["GET"])]
    public function list(Request $request) : JsonResponse
    {
        $request = $request->query->all();
        $data = $this->em->getRepository(Product::class)->findAllPaged($request);
        return new JsonResponse($data);
    }
}