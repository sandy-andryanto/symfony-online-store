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

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as Faker;
use App\Entity\User;
use App\Entity\Authentication;
use App\Entity\Setting;
use App\Entity\Category;
use App\Entity\Brand;
use App\Entity\Colour;
use App\Entity\Size;
use App\Entity\Payment;
use App\Entity\Product;
use App\Entity\ProductImage;
use App\Entity\ProductInventory;
use App\Entity\ProductReview;
use DateTime;

#[AsCommand(name: 'app:seed-database')]
class SeedCommand extends Command
{
    private EntityManagerInterface $em;
    private PasswordHasherFactoryInterface $passwordHasherFactory;
    private string $defaultPassword = "Qwerty123!";
    private int $maxUser = 10;

    public function __construct(EntityManagerInterface $em, PasswordHasherFactoryInterface $hasherFactory)
    {
        parent::__construct();
        $this->em = $em;
        $this->passwordHasherFactory = $hasherFactory;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('['.date('Y-m-d H:i:s').'] Begin seed data please wait... !!');
        $this->createUser();
        $this->createSetting();
        $this->createCategories();
        $this->createBrands();
        $this->createColours();
        $this->createPayment();
        $this->createSize();
        $this->createProduct();
        $output->writeln('['.date('Y-m-d H:i:s').'] Seed data has been finished... !!');
        return Command::SUCCESS;
    }

    private function createSetting(): void
    {
        $total = (int) $this->em->getRepository(Setting::class)->createQueryBuilder('x')->select('count(x.id)')->getQuery()->getSingleScalarResult();

        if($total == 0)
        {
             $settings = [
                "about_section"         => "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut.",
                "com_location"          => "West Java, Indonesia",
                "com_phone"             => "+62-898-921-8470",
                "com_email"             => "sandy.andryanto.official@gmail.com",
                "com_currency"          => "USD",
                "installed"             => 1,
                "discount_active"       => 1,
                "discount_value"        => 5,
                "discount_start"        => date("Y-m-d H:i:s"),
                "discount_end"          => date("Y-m-d H:i:s", strtotime("+7 day")),
                "taxes_value"           => 10,
                "total_shipment"        => 50
            ];

            foreach ($settings as $key => $value)
            {
                $setting = new Setting();
                $setting->setKeyName($key);
                $setting->setKeyValue($value);
                $this->em->persist($setting);
            }

            $this->em->flush();
        }

    }

    private function createCategories(): void
    {
        $total = (int) $this->em->getRepository(Category::class)->createQueryBuilder('x')->select('count(x.id)')->getQuery()->getSingleScalarResult();

        if($total == 0)
        {
            $items = [
                "Laptop"        => "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product01.png",
                "Smartphone"    => "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product02.png",
                "Camera"        => "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product03.png",
                "Accessories"   => "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product04.png",
                "Others"        => "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product05.png",
            ];

            $num = 1;
            foreach ($items as $name => $image) {
                $displayed = $num <= 3 ? 1 : 0;
                $cat = new Category();
                $cat->setName($name);
                $cat->setImage($image);
                $cat->setDisplayed($displayed);
                $this->em->persist($cat);
            }

            $this->em->flush();
        }
    }

    private function createBrands(): void
    {
        $total = (int) $this->em->getRepository(Brand::class)->createQueryBuilder('x')->select('count(x.id)')->getQuery()->getSingleScalarResult();

        if($total == 0)
        {
            $items = ["Samsung", "LG", "Sony", "Apple", "Microsoft"];
            foreach ($items as $item) {
                $brand = new Brand();
                $brand->setName($item);
                $this->em->persist($brand);
            }

            $this->em->flush();
        }
    }

    private function createColours(): void
    {
        $total = (int) $this->em->getRepository(Colour::class)->createQueryBuilder('x')->select('count(x.id)')->getQuery()->getSingleScalarResult();

        if($total == 0)
        {
             $colors = [
                "#FF0000"   => "Red",
                "#0000FF"   => "Blue",
                "#FFFF00"   => "Yellow",
                "#000000"   => "Black",
                "#FFFFFF"   => "White",
                "#666"      => "Dark Gray",
                "#AAA"      => "Light Gray"
            ];

            foreach ($colors as $key => $value) {
                $clr = new Colour();
                $clr->setCode($key);
                $clr->setName($value);
                $this->em->persist($clr);
            }

            $this->em->flush();
        }

    }

    private function createSize(): void
    {
        $total = (int) $this->em->getRepository(Size::class)->createQueryBuilder('x')->select('count(x.id)')->getQuery()->getSingleScalarResult();

        if($total == 0)
        {
            $items = ["11 to 12 Inches", "13 to 14 Inches", "15 to 16 Inches", "17 to 18 Inches"];
            foreach ($items as $item) {
                $size = new Size();
                $size->setName($item);
                $this->em->persist($size);
            }

            $this->em->flush();
        }
    }

    private function createPayment(): void
    {
        $total = (int) $this->em->getRepository(Payment::class)->createQueryBuilder('x')->select('count(x.id)')->getQuery()->getSingleScalarResult();

        if($total == 0)
        {
            $items = ["Direct Bank Transfer", "Cheque Payment", "Paypal System"];
            $description = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua";

            foreach ($items as $item) {
                $p = new Payment();
                $p->setName($item);
                $p->setDescription($description);
                $this->em->persist($p);
            }

            $this->em->flush();
        }

    }

    private function createProduct(): void
    {
        $total = (int) $this->em->getRepository(Product::class)->createQueryBuilder('x')->select('count(x.id)')->getQuery()->getSingleScalarResult();

        if($total == 0)
        {
           
            $colours = $this->em->getRepository(Colour::class)
                ->createQueryBuilder('x')
                ->getQuery()
                ->getResult();

            $sizes = $this->em->getRepository(Size::class)
                ->createQueryBuilder('x')
                ->getQuery()
                ->getResult();

            for ($i = 1; $i <= 9; $i++) 
            {   
                $thumbnail = $this->ProductImages()[rand(0, 8)];
                $brand = $this->em->getRepository(Brand::class)
                    ->createQueryBuilder('x')
                    ->setMaxResults(1)
                    ->orderBy('RAND()')
                    ->getQuery()
                    ->getOneOrNullResult();

                $categories = $this->em->getRepository(Category::class)
                    ->createQueryBuilder('x')
                    ->setMaxResults(3)
                    ->orderBy('RAND()')
                    ->getQuery()
                    ->getResult();

                $reviewers = $this->em->getRepository(User::class)
                    ->createQueryBuilder('x')
                    ->setMaxResults(5)
                    ->orderBy('RAND()')
                    ->getQuery()
                    ->getResult();

                $faker = Faker::create();
                $expiredDate = new DateTime();
                $expiredDate->modify('-1 day');
                $expiredDate->format('Y-m-d H:i:s');

                $product = new Product();
                $product->setCategories($categories);
                $product->setImage($thumbnail);
                $product->setBrand($brand);
                $product->setSku( "P00" . $i);
                $product->setName( "Product " . $i);
                $product->setTotalOrder(rand(100, 1000));
                $product->setTotalRating(rand(100, 1000));
                $product->setPrice(rand(100, 999));
                $product->setPublishedDate($expiredDate);
                $product->setDescription($faker->text);
                $product->setDetails($faker->text);
                $this->em->persist($product);

                foreach($reviewers as $row)
                {
                    $reviewer = new ProductReview();
                    $reviewer->setProduct($product);
                    $reviewer->setUser($row);
                    $reviewer->setRating(rand(0, 100));
                    $reviewer->setReview($faker->text);
                    $this->em->persist($reviewer);
                }

                for ($in = 0; $in < 3; $in++) {
                    $image = rand(0, 8);
                    $path = $this->ProductImages()[$image];
                    $pi = new ProductImage();
                    $pi->setProduct($product);
                    $pi->setPath($path);
                    $pi->setSort($in + 1);
                    $this->em->persist($pi);
                }

                 foreach ($sizes as $size) {
                    foreach ($colours as $colour) {
                        $inv = new ProductInventory();
                        $inv->setProduct($product);
                        $inv->setSize($size);
                        $inv->setColour($colour);
                        $inv->setStock(rand(1, 50));
                        $this->em->persist($inv);
                    }
                }
            }   

            $this->em->flush();
        }
    }

    private function createUser(): void
    {
        $total = (int) $this->em->getRepository(User::class)->createQueryBuilder('x')->select('count(x.id)')->getQuery()->getSingleScalarResult();
        $max = $this->maxUser;
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(User::class);
        $hash = $passwordHasher->hash($this->defaultPassword);

        if($total == 0)
        {
            for($i = 1; $i <= $max; $i++)
            {
                $faker = Faker::create();
                $gender = (int) rand(1, 2);
                $first_name = $gender == 1 ? $faker->firstNameMale : $faker->firstNameFemale;
                $email = $faker->safeEmail;
                $user = new User();
                $user->setEmail($email);
                $user->setPassword($hash);
                $user->setPhone($faker->phoneNumber);
                $user->setRoles(["ROLE_USER"]);
                $user->setStatus(1);
                $user->setFirstName($first_name);
                $user->setLastName($faker->lastName);
                $user->setGender($gender == 1 ? 'M' : 'F');
                $user->setCity($faker->city);
                $user->setZipCode($faker->postcode);
                $user->setCountry($faker->country);
                $user->setAddress($faker->streetAddress);
                $this->em->persist($user);

                $auth = new Authentication();
                $auth->setUser($user);
                $auth->setType("email-verification");
                $auth->setCredential($email);
                $auth->setToken($faker->uuid);
                $auth->setExpiredAt(new DateTime());
                $this->em->persist($auth);

            }

            $this->em->flush();
        }
    }

    private function ProductImages()
    {
        return [
            "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product01.png",
            "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product02.png",
            "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product03.png",
            "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product04.png",
            "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product05.png",
            "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product06.png",
            "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product07.png",
            "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product08.png",
            "https://5an9y4lf0n50.github.io/demo-images/demo-commerce/product09.png"
        ];
    }

}