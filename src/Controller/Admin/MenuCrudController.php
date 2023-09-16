<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuCrudController extends AbstractCrudController
{
    const MENU_PAGES = 0;
    const MENU_ARTICLES = 1;
    const MENU_LINKS = 2;
    const MENU_CATEGORIES =3;

    public function __construct( private RequestStack $requestStack,private MenuRepository $menuRepos)
    {
        
    }
    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $subMenuIndex = $this->getSubMenuIndex();
        return $this->menuRepos->getIndexQueryBuilder($this->getfieldNameFromSubMenuIndex($subMenuIndex));
    }
    public function configureCrud(Crud $crud): Crud
    {
        $subMenuIndex = $this->getSubMenuIndex();
        $entityLabelInSingular = 'un menu';
        $entityLabelInPlural = match($subMenuIndex){
            self::MENU_ARTICLES =>'Articles',
            self::MENU_CATEGORIES => 'Catégories',
            self::MENU_LINKS => 'Liens personnalisés',
            default =>'Pages'
        };
        return $crud->setEntityLabelInSingular($entityLabelInSingular)
        ->setEntityLabelInPlural($entityLabelInPlural);
    }
    public function configureFields(string $pageName): iterable
    {
        $subMenuIndex = $this->getSubMenuIndex();

        yield  TextField::new('name','Titre de la navigation');
        yield   NumberField::new('menuOrder','Ordre');

        yield $this->getFieldFromSubMenuIndex($subMenuIndex)->setRequired(true);

        yield   BooleanField::new('isVisible','Visible');
        yield   AssociationField::new('subMenus','Sous-éléments');
    }
    
    private function getfieldNameFromSubMenuIndex(int $subMenuIndex)
    {
        
        return match($subMenuIndex){
            self::MENU_ARTICLES =>'article',
            self::MENU_CATEGORIES => 'category',
            self::MENU_LINKS => 'link',
            default =>'page'
        };
    }
    private function getFieldFromSubMenuIndex(int $subMenuIndex)
    {
        $fieldName = $this->getfieldNameFromSubMenuIndex($subMenuIndex);

        return ($fieldName === 'link') ? TextField::new($fieldName,'lien') : AssociationField::new($fieldName);
    }
    private function getSubMenuIndex(): int
    {
        $url = $this->requestStack->getMainRequest()->query->all();
        foreach ($url as $key => $value) {
            if( 'referrer' === $key){
                $val = strstr($value, 'submenuIndex');
                $val = substr($val,13);
                return $val;
            }
        }
        return $this->requestStack->getMainRequest()->query->getInt('submenuIndex');
    }
    
    
    
}
