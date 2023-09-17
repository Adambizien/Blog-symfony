<?php 
namespace App\Service;

use App\Entity\Article;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Component\Pager\PaginatorInterface;

class CommentService
{

    public function __construct(
        private RequestStack $requestStack,
        private CommentRepository $commentRepository,
        private PaginatorInterface $paginator,
    )
    {
        
    }

    public function getPaginatedComments(?Article $article = null)
    {
        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page',1);
        $limit = 5 ;

        $commentQuery = $this->commentRepository->findForPagination($article);
        return $this->paginator->paginate($commentQuery,$page,$limit);
    }
}