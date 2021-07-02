<?php

namespace SymfonySimpleSite\Menu\Controller;

use Symfony\Component\HttpFoundation\Response;
use SymfonySimpleSite\Menu\Entity\Menu;
use SymfonySimpleSite\Menu\Repository\MenuRepository;
use SymfonySimpleSite\Page\Controller\AbstractPageController;

class MenuController extends AbstractPageController
{
    public function topMenu(int $rootId, MenuRepository $menuRepository): Response
    {
        $items = $menuRepository->getItemsByIds($rootId, 1);
        return $this->render('@Menu/frontend/top_menu.html.twig', ['items' => $items]);
    }

    public function leftMenu(int $rootId, MenuRepository $menuRepository): Response
    {
        $items = $menuRepository->getItemsByIds($rootId);
        return $this->render('@Menu/frontend/left_menu.html.twig', [
            'items' => $items,
            'item_template' => '@Menu/frontend/tree_item.html.twig'
        ]);
    }
}
