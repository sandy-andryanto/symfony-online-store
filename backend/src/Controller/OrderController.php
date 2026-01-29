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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Entity\ProductReview;
use App\Entity\ProductImage;
use App\Entity\ProductInventory;
use App\Entity\Order;
use App\Entity\OrderBilling;
use App\Entity\OrderDetail;
use App\Entity\Setting;
use App\Entity\Payment;
use App\Entity\User;
use App\Entity\Activity;

#[Route('api/order')]
class OrderController extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/list', methods: ["GET"])]
    public function list(Request $request) : JsonResponse
    {
        /** @var ?User $user */
        $user = $this->getUser();
        $request = $request->query->all();
        $data = $this->em->getRepository(Order::class)->getDataTable($request, $user);
        return new JsonResponse($data);
    }

    #[Route('/product', methods: ["GET"])]
    public function product() : JsonResponse
    {
        /** @var ?User $user */
        $user = $this->getUser();
        $order = $this->em->getRepository(Order::class)->findByPending($user, true);
        $cart = empty($order) ? [] : $this->em->getRepository(OrderDetail::class)->getListOrder($order["id"]);
        $wishlist = $this->em->getRepository(Product::class)->getWhislist($user->getId());

        $payload = [
            "order"     => $order,            
            "cart"      => $cart,
            "wishlist"  => $wishlist
        ];

        return new JsonResponse($payload, 200);
    }

    #[Route('/billing/{id}', methods: ["GET"])]
    public function billing(int $id) : JsonResponse
    {
        /** @var ?User $user */
        $user = $this->getUser();
        $authUser = $this->em->getRepository(User::class)->findByUserId($user);
        $order = $this->em->getRepository(Order::class)->find($id);

        if($order->getUser() != $user)
        {
            return new JsonResponse(["message"=> "We can't find a order with user account is invalid.!"], 403);
        }

        $payments = $this->em->getRepository(Payment::class)->getAll();
        $cart = empty($order) ? [] : $this->em->getRepository(OrderDetail::class)->getListOrder($id);
        $billing = $this->em->getRepository(OrderBilling::class)->getBilling($id);
        $orderData = $this->em->getRepository(Order::class)->findById($id);
        $orderData["paymentName"] = !is_null($order->getPayment()) ? $order->getPayment()->getName() : '';

        $payload = [
            "cart"      => $cart,
            "order"     => $orderData,
            "payments"  => $payments,
            "billing"   => empty($billing) ? $authUser : $billing,
        ];

        return new JsonResponse($payload, 200);
    }

     #[Route('/cancel', methods: ["GET"])]
    public function cancel() : JsonResponse
    {
        /** @var ?User $user */
        $user = $this->getUser();
        $order = $this->em->getRepository(Order::class)->findByPending($user, false);

        if(!is_null($order))
        {
            $order->setCarts([]);
            $this->em->getRepository(OrderBilling::class)->deleteByOrder($order);
            $this->em->getRepository(OrderDetail::class)->deleteByOrder($order);
            $this->em->remove($order);
            $this->em->flush();
        }

        $this->em->getRepository(Activity::class)->Create($user, "Cancel Order",  "Canceling current order", "Your order has been canceled");
        return new JsonResponse(["ok"], 200);   
    }

    #[Route('/cart/{id}', methods: ["GET"])]
    public function cartDetail(int $id) : JsonResponse
    {

        $product = $this->em->getRepository(Product::class)->getById($id);

        if($product == null)
        {
            return new JsonResponse(["message"=> "We can't find a product with that id is invalid.!"], 400);
        }

        $images  =  $this->em->getRepository(ProductImage::class)->getAll($id);
        $stocks  =  $this->em->getRepository(ProductInventory::class)->getAvailable($id);
        $colours =  $this->em->getRepository(ProductInventory::class)->getColours($id);
        $sizes   =  $this->em->getRepository(ProductInventory::class)->getSizes($id);
        $related =  $this->em->getRepository(Product::class)->getRecomended(4, $id);

        $bestSellers = $this->em->getRepository(Product::class)->getBestSellers();

        if(count($bestSellers) > 0)
        {   
            $top = $bestSellers[0];
            $product["totalRating"] = ((($product["totalRating"] / $top["totalRating"]) * 100) / 20);
            $product["totalRating"] = floor($product["totalRating"]);
        }

        
        
        $payload = [
            "product"   => $product,
            "images"    => $images,
            "stocks"    => $stocks,
            "related"   => $related,
            "sizes"     => $sizes,
            "colours"   => $colours,
        ];

        return new JsonResponse($payload, 200);
    }

     #[Route('/cart/{id}', methods: ["POST"])]
    public function cartAdd(int $id, Request $request) : JsonResponse
    {
        $input = json_decode($request->getContent(), true);
        $size_id = (int) $input['size_id'];
        $color_id = (int) $input['color_id'];
        $qty = (int) $input['qty'];

        $product = $this->em->getRepository(Product::class)->find($id);

        if($product == null)
        {
            return new JsonResponse(["message"=> "We can't find a product with that id is invalid.!"], 400);
        }

        /** @var ?User $user */
        $user = $this->getUser();
        $order = $this->em->getRepository(Order::class)->findByPending($user);
        $orderNumber = date("Ymd")."".(floor(microtime(true) * 1000));

        if(is_null($order))
        {
            $order = new Order();
            $order->setUser($user);
            $order->setInvoiceNumber($orderNumber);
            $order->setStatus(0);
        }

        $newItem = (int) $order->getTotalItem() + (int) $qty;
        $order->setTotalItem($newItem);
        $order->setCarts([$product]);
        $this->em->persist($order);
        $this->em->flush();

        $inventory = $this->em->getRepository(ProductInventory::class)->getByColourAndSize($id, $color_id, $size_id);
        $current = $this->em->getRepository(OrderDetail::class)->getByInventory($order->getId(), $inventory->getId());

        if(is_null($current))
        {
            $detail = new OrderDetail();
            $detail->setInventory($inventory);
            $detail->setOrder($order);
        }
        
        $detail->setPrice($product->getPrice());
        $detail->setQty($qty);
        $detail->setTotal($product->getPrice() * $qty);
        $detail->setStatus(1);
        $this->em->persist($detail);
        $this->em->flush();


        /** @var ?User $user */
        $user = $this->getUser();
        $order = $this->em->getRepository(Order::class)->findByPending($user);

        if(!is_null($order))
        {
            $subtotal =  (float) $this->em->getRepository(OrderDetail::class)->getSubtotal($order->getId());
            $totalQty =  (int) $this->em->getRepository(OrderDetail::class)->getTotalQty($order->getId());
            $discount = 0;
            $taxes = 0;
            $shipment = (float) $this->em->getRepository(Setting::class)->getByKey("total_shipment");

            $getDiscount = (float) $this->em->getRepository(Setting::class)->getByKey("discount_value");
            $getTaxes = (float) $this->em->getRepository(Setting::class)->getByKey("taxes_value");

            if($getDiscount > 0)
            {
                $discount = $subtotal * ($getDiscount/100);
            }

            if($getTaxes > 0)
            {
                $taxes = $subtotal * ($getTaxes/100);
            }
            
            $grandTotal = ($subtotal + $taxes +  $shipment) - $discount;
            $order->setTotalShipment($shipment);
            $order->setTotalDiscount($discount);
            $order->setTotalTaxes($taxes);
            $order->setTotalPaid($grandTotal);
            $order->setSubtotal($subtotal);
            $order->setTotalItem($totalQty);
            $this->em->persist($order);
            $this->em->flush();
        }
        
        $this->em->getRepository(Activity::class)->Create($user, "Add Product", "Adding Product ".$product->getName(), "Your cart has been added to product ".$product->getName());
        return new JsonResponse(["ok"], 200);
    }

    #[Route('/wishlist/{id}', methods: ["GET"])]
    public function wishlist(int $id) : JsonResponse
    {
        /** @var ?User $user */
        $user = $this->getUser();
        $product = $this->em->getRepository(Product::class)->find($id);

        if($product == null)
        {
            return new JsonResponse(["message"=> "We can't find a product with that id is invalid.!"], 400);
        }

        $newWishlist = [$this->getUser()];
        $product->setWishlists($newWishlist);
        $this->em->persist($product);
        $this->em->flush();

        $this->em->getRepository(Activity::class)->Create($user, "Product Wishlist", "Wishlist Product ".$product->getName(), "Your wishlist has been added to product ".$product->getName());
        return new JsonResponse($product, 200);
    }

    #[Route('/review/{id}', methods: ["GET"])]
    public function listReview(int $id) : JsonResponse
    {
        $reviews = $this->em->getRepository(ProductReview::class)->getAll($id);
        return new JsonResponse($reviews, 200);
    }

    #[Route('/review/{id}', methods: ["POST"])]
    public function createReview(Request $request, int $id) : JsonResponse
    {
        $product = $this->em->getRepository(Product::class)->find($id);

        if($product == null)
        {
            return new JsonResponse(["message"=> "We can't find a product with that id is invalid.!"], 400);
        }

         /** @var ?User $user */
        $user = $this->getUser();
        $input = json_decode($request->getContent(), true);
        $description = $input["review"];
        $review = new ProductReview();
        $review->setProduct($product);
        $review->setUser($this->getUser());
        $review->setRating($input["rating"]);
        $review->setReview($description);
        $this->em->persist($review);
        $this->em->flush();

        $this->em->getRepository(Activity::class)->Create($user, "Product Review", "Review Product ".$product->getName(), "Your review has been added to product ".$product->getName());
        return new JsonResponse($review, 200);
    }

    #[Route('/checkout/{id}', methods: ["POST"])]
    public function checkout(Request $request, int $id) : JsonResponse
    {

        /** @var ?User $user */
        $user = $this->getUser();
        $input = json_decode($request->getContent(), true);
        $payment = (int) $input["payment_id"];
        
        $order = $this->em->getRepository(Order::class)->find($id);

        if(is_null($order))
        {
            return new JsonResponse(["message"=> "We can't find a order with that id is invalid.!"], 400);
        }

        if($order->getUser() != $user)
        {
            return new JsonResponse(["message"=> "We can't find a order with user account is invalid.!"], 403);
        }

        $payment = $this->em->getRepository(Payment::class)->find($payment);
        $order->setPayment($payment);
        $order->setStatus(1);
        $this->em->persist($order);

        foreach($input as $name => $description)
        {
            $billing = new OrderBilling();
            $billing->setOrder($order);
            $billing->setName($name);
            $billing->setDescription($description);
            $billing->setStatus(1);
            $this->em->persist($billing);
        }

        $details = $this->em->getRepository(OrderDetail::class)->findAllByOrder($id);

        foreach($details as $detail)
        {
            $inv = $detail->getInventory();
            $inv->setStock($inv->getStock() - $detail->getQty());
            $this->em->persist($inv);

            $product = $inv->getProduct();
            $product->setTotalOrder($product->getTotalOrder() + $detail->getQty());
            $product->setWishlists([]);
            $this->em->persist($product);
        }

        $this->em->flush();
        $this->em->getRepository(Activity::class)->Create($user, "Checkout Order", "Checkout Current Order", "Your order has been finished.");
        return new JsonResponse(["message"=> "Your order has been completed."], 200);
    }

}