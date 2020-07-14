<?php
/**
 * Created by PhpStorm.
 * User: sacha
 * Date: 14/04/2020
 * Time: 22:33
 */

namespace App\Service;


use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\OrderRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Scalar\MagicConst\Dir;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Stripe\Stripe;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;


class MakeJsonFormat
{
    private $entityManager;
    private $orderRepo;

    public function __construct(EntityManagerInterface $em, OrderRepository $orderRepo)
    {
        $this->em = $em;
        $this->orderRepo = $orderRepo;
    }

    /* Return Json for statistic author Graph */
    public function get30LastDaysCommandsForAuthor($user) 
    {
        $BeforeDay1 = 1;
        dump(count($this->orderRepo->getByDateByAuthor(new DateTime('-0days'), $user, 0)));


        $Array30LastCommand = [
            'j-0' => count($this->orderRepo->getByDateByAuthor(new DateTime('-0days'), $user)),
            'j-1' => count($this->orderRepo->getByDateByAuthor(new DateTime('-1days'), $user)),
            'j-2' => count($this->orderRepo->getByDateByAuthor(new DateTime('-2days'), $user)),
            'j-3' => count($this->orderRepo->getByDateByAuthor(new DateTime('-3days'), $user)),
            'j-4' => count($this->orderRepo->getByDateByAuthor(new DateTime('-4days'), $user)),
            'j-5' => count($this->orderRepo->getByDateByAuthor(new DateTime('-5days'), $user)),
            'j-6' => count($this->orderRepo->getByDateByAuthor(new DateTime('-6days'), $user)),
            'j-7' => count($this->orderRepo->getByDateByAuthor(new DateTime('-7days'), $user)),
            'j-8' => count($this->orderRepo->getByDateByAuthor(new DateTime('-8days'), $user)),
            'j-9' => count($this->orderRepo->getByDateByAuthor(new DateTime('-9days'), $user)),
            'j-10' => count($this->orderRepo->getByDateByAuthor(new DateTime('-10days'), $user)),
            'j-11' => count($this->orderRepo->getByDateByAuthor(new DateTime('-11days'), $user)),
            'j-12' => count($this->orderRepo->getByDateByAuthor(new DateTime('-12days'), $user)),
            'j-13' => count($this->orderRepo->getByDateByAuthor(new DateTime('-13days'), $user)),
            'j-14' => count($this->orderRepo->getByDateByAuthor(new DateTime('-14days'), $user)),
            'j-15' => count($this->orderRepo->getByDateByAuthor(new DateTime('-15days'), $user)),
            'j-16' => count($this->orderRepo->getByDateByAuthor(new DateTime('-16days'), $user)),
            'j-17' => count($this->orderRepo->getByDateByAuthor(new DateTime('-17days'), $user)),
            'j-18' => count($this->orderRepo->getByDateByAuthor(new DateTime('-18days'), $user)),
            'j-19' => count($this->orderRepo->getByDateByAuthor(new DateTime('-19days'), $user)),
            'j-20' => count($this->orderRepo->getByDateByAuthor(new DateTime('-20days'), $user)),
            'j-21' => count($this->orderRepo->getByDateByAuthor(new DateTime('-21days'), $user)),
            'j-22' => count($this->orderRepo->getByDateByAuthor(new DateTime('-22days'), $user)),
            'j-23' => count($this->orderRepo->getByDateByAuthor(new DateTime('-23days'), $user)),
            'j-24' => count($this->orderRepo->getByDateByAuthor(new DateTime('-24days'), $user)),
            'j-25' => count($this->orderRepo->getByDateByAuthor(new DateTime('-25days'), $user)),
            'j-26' => count($this->orderRepo->getByDateByAuthor(new DateTime('-26days'), $user)),
            'j-27' => count($this->orderRepo->getByDateByAuthor(new DateTime('-27days'), $user)),
            'j-28' => count($this->orderRepo->getByDateByAuthor(new DateTime('-28days'), $user)),
            'j-29' => count($this->orderRepo->getByDateByAuthor(new DateTime('-29days'), $user)),
            'j-30' => count($this->orderRepo->getByDateByAuthor(new DateTime('-30days'), $user)),
        ];
        
        return $Array30LastCommand;

        /* possible in repository */
        /*
        $qb = $this->createQueryBuilder('m');
 
        $qb->where('m.dateUpload < :date')
           ->setParameter('date', new \dateTime('7days'))
           ->orderBy('m.dateUpload', 'DESC')
           ->orderBy('m.view', 'DESC')
           ->setMaxResults($nombre);
 
        return $qb->getQuery()->getResult();
        */
    }

}