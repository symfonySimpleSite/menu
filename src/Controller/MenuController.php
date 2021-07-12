<?php

namespace SymfonySimpleSite\Menu\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SymfonySimpleSite\Menu\Repository\MenuRepository;
use SymfonySimpleSite\Page\Controller\AbstractPageController;

class MenuController extends AbstractPageController
{
    public function topMenu(string $name = 'Top menu', MenuRepository $menuRepository): Response
    {
        $items = $menuRepository->getItemsByName($name, 1);
        return $this->render('@Menu/frontend/top_menu.html.twig', ['items' => $items]);
    }

    public function leftMenu(Request $request, string $name = 'Left menu', MenuRepository $menuRepository): Response
    {
        $items = $menuRepository->getItemsByName($name);
        return $this->render('@Menu/frontend/left_menu.html.twig', [
            'items' => $items,
            'request' => $request,
            'item_template' => '@Menu/frontend/tree_item.html.twig'
        ]);
    }
}
