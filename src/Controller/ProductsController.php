<?php

namespace App\Controller;

use App\Cache\PromotionCache;
use App\DTO\LowestPriceEnquiry;
use App\Filter\PromotionsFilterInterface;
use App\Repository\ProductRepository;
use App\Service\DTOSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    public function __construct(private ProductRepository $repository, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/promotions/{id}/products', name: 'promotions')]
    public function index(int $id): JsonResponse
    {
        return $this->json([$id]);
    }

    #[Route('/promotion/{id}/lowest-price/products', name: 'lowest-price', methods: 'POST')]
    public function lowestPrice(
        Request $request,
        int $id,
        DTOSerializer $serializer,
        PromotionsFilterInterface $promotionsFilter,
        PromotionCache $promotionCache): Response
    {
        /* @var LowestPriceEnquiry $lowPriceEnquiry */
        $lowPriceEnquiry = $serializer->deserialize($request->getContent(), LowestPriceEnquiry::class, 'json');

        $product = $this->repository->find($id);

        if(!$product){
            throw new NotFoundHttpException('Product not found');
        }

        $lowPriceEnquiry->setProduct($product);

        $promotions = $promotionCache->findValidForProduct($product, $lowPriceEnquiry->getRequestDate());

        $modifiedEnquiry = $promotionsFilter->apply($lowPriceEnquiry, ...$promotions);
        $responseContent = $serializer->serialize($modifiedEnquiry, 'json');
        return new Response($responseContent);

    }
}
