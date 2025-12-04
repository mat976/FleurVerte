<?php

namespace App\Tests\Form;

use App\Entity\Fleur;
use App\Entity\Tag;
use App\Form\FleurType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Test\DoctrineTestHelper;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;

/**
 * Tests unitaires pour le formulaire FleurType
 */
class FleurTypeTest extends TypeTestCase
{
    private $entityManager;
    private $tagRepository;

    protected function getExtensions()
    {
        // Mock de l'EntityManager et du repository
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->tagRepository = $this->createMock(EntityRepository::class);
        
        $this->entityManager->method('getRepository')
                           ->with(Tag::class)
                           ->willReturn($this->tagRepository);
        
        // Configuration des tags de test
        $tag1 = new Tag();
        $tag1->setNom('Indica');
        $tag2 = new Tag();
        $tag2->setNom('Sativa');
        
        $this->tagRepository->method('findAll')
                           ->willReturn([$tag1, $tag2]);
        
        return [
            new ValidatorExtension(Validation::createValidator()),
            new DoctrineOrmExtension($this->entityManager)
        ];
    }

    public function testFormCreation(): void
    {
        $form = $this->factory->create(FleurType::class);
        
        $this->assertTrue($form->has('nom'));
        $this->assertTrue($form->has('description'));
        $this->assertTrue($form->has('prix'));
        $this->assertTrue($form->has('Thc'));
        $this->assertTrue($form->has('stock'));
        $this->assertTrue($form->has('isPinned'));
        $this->assertTrue($form->has('imageFile'));
        $this->assertTrue($form->has('tags'));
    }

    public function testFormSubmissionWithValidData(): void
    {
        $formData = [
            'nom' => 'Test Fleur',
            'description' => 'Description de test',
            'prix' => 25.99,
            'Thc' => 18.5,
            'stock' => 10,
            'isPinned' => true,
            'tags' => []
        ];
        
        $fleur = new Fleur();
        $form = $this->factory->create(FleurType::class, $fleur);
        
        $form->submit($formData);
        
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        
        $this->assertEquals('Test Fleur', $fleur->getNom());
        $this->assertEquals('Description de test', $fleur->getDescription());
        $this->assertEquals(25.99, $fleur->getPrix());
        $this->assertEquals(18.5, $fleur->getThc());
        $this->assertEquals(10, $fleur->getStock());
        $this->assertTrue($fleur->isIsPinned());
    }

    public function testFormSubmissionWithEmptyData(): void
    {
        $formData = [];
        
        $form = $this->factory->create(FleurType::class);
        $form->submit($formData);
        
        $this->assertTrue($form->isSynchronized());
        // Le formulaire peut être invalide selon les contraintes de validation
    }

    public function testNomFieldConfiguration(): void
    {
        $form = $this->factory->create(FleurType::class);
        $nomField = $form->get('nom');
        
        $this->assertEquals('Nom du produit', $nomField->getConfig()->getOption('label'));
    }

    public function testDescriptionFieldConfiguration(): void
    {
        $form = $this->factory->create(FleurType::class);
        $descriptionField = $form->get('description');
        
        $this->assertEquals('Description', $descriptionField->getConfig()->getOption('label'));
    }

    public function testPrixFieldConfiguration(): void
    {
        $form = $this->factory->create(FleurType::class);
        $prixField = $form->get('prix');
        
        $this->assertEquals('Prix', $prixField->getConfig()->getOption('label'));
        $this->assertEquals('EUR', $prixField->getConfig()->getOption('currency'));
    }

    public function testThcFieldConfiguration(): void
    {
        $form = $this->factory->create(FleurType::class);
        $thcField = $form->get('Thc');
        
        $this->assertEquals('Taux THC', $thcField->getConfig()->getOption('label'));
        $this->assertEquals(2, $thcField->getConfig()->getOption('scale'));
    }

    public function testStockFieldConfiguration(): void
    {
        $form = $this->factory->create(FleurType::class);
        $stockField = $form->get('stock');
        
        $this->assertEquals('Stock disponible', $stockField->getConfig()->getOption('label'));
        
        $attr = $stockField->getConfig()->getOption('attr');
        $this->assertEquals(0, $attr['min']);
        $this->assertEquals('Entrez la quantité en stock', $attr['placeholder']);
    }

    public function testIsPinnedFieldConfiguration(): void
    {
        $form = $this->factory->create(FleurType::class);
        $isPinnedField = $form->get('isPinned');
        
        $this->assertEquals('Épingler ce produit', $isPinnedField->getConfig()->getOption('label'));
        $this->assertFalse($isPinnedField->getConfig()->getOption('required'));
    }

    public function testImageFileFieldConfiguration(): void
    {
        $form = $this->factory->create(FleurType::class);
        $imageFileField = $form->get('imageFile');
        
        $this->assertEquals('Image du produit', $imageFileField->getConfig()->getOption('label'));
        $this->assertFalse($imageFileField->getConfig()->getOption('required'));
        $this->assertTrue($imageFileField->getConfig()->getOption('allow_delete'));
        $this->assertEquals('Supprimer l\'image', $imageFileField->getConfig()->getOption('delete_label'));
    }

    public function testTagsFieldConfiguration(): void
    {
        $form = $this->factory->create(FleurType::class);
        $tagsField = $form->get('tags');
        
        $this->assertEquals('Tags', $tagsField->getConfig()->getOption('label'));
        $this->assertTrue($tagsField->getConfig()->getOption('multiple'));
        $this->assertTrue($tagsField->getConfig()->getOption('expanded'));
        $this->assertFalse($tagsField->getConfig()->getOption('required'));
        $this->assertFalse($tagsField->getConfig()->getOption('by_reference'));
    }

    public function testFormDataClass(): void
    {
        $form = $this->factory->create(FleurType::class);
        
        $this->assertEquals(Fleur::class, $form->getConfig()->getDataClass());
    }

    public function testFormWithNegativeStock(): void
    {
        $formData = [
            'nom' => 'Test Fleur',
            'description' => 'Description',
            'prix' => 25.99,
            'Thc' => 18.5,
            'stock' => -5, // Stock négatif
            'isPinned' => false,
            'tags' => []
        ];
        
        $form = $this->factory->create(FleurType::class);
        $form->submit($formData);
        
        $this->assertTrue($form->isSynchronized());
        // Le comportement dépend des contraintes de validation définies sur l'entité
    }

    public function testFormWithZeroPrice(): void
    {
        $formData = [
            'nom' => 'Test Fleur',
            'description' => 'Description',
            'prix' => 0, // Prix à zéro
            'Thc' => 18.5,
            'stock' => 10,
            'isPinned' => false,
            'tags' => []
        ];
        
        $form = $this->factory->create(FleurType::class);
        $form->submit($formData);
        
        $this->assertTrue($form->isSynchronized());
    }

    public function testFormWithHighThcValue(): void
    {
        $formData = [
            'nom' => 'Test Fleur',
            'description' => 'Description',
            'prix' => 25.99,
            'Thc' => 99.99, // Valeur THC très élevée
            'stock' => 10,
            'isPinned' => false,
            'tags' => []
        ];
        
        $form = $this->factory->create(FleurType::class);
        $form->submit($formData);
        
        $this->assertTrue($form->isSynchronized());
    }

    public function testFormWithLongDescription(): void
    {
        $longDescription = str_repeat('A', 1000); // Description très longue
        
        $formData = [
            'nom' => 'Test Fleur',
            'description' => $longDescription,
            'prix' => 25.99,
            'Thc' => 18.5,
            'stock' => 10,
            'isPinned' => false,
            'tags' => []
        ];
        
        $form = $this->factory->create(FleurType::class);
        $form->submit($formData);
        
        $this->assertTrue($form->isSynchronized());
    }

    public function testFormWithSpecialCharactersInName(): void
    {
        $formData = [
            'nom' => 'Fleur Spéciale & Unique #1',
            'description' => 'Description avec caractères spéciaux: àéèùç',
            'prix' => 25.99,
            'Thc' => 18.5,
            'stock' => 10,
            'isPinned' => false,
            'tags' => []
        ];
        
        $fleur = new Fleur();
        $form = $this->factory->create(FleurType::class, $fleur);
        $form->submit($formData);
        
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals('Fleur Spéciale & Unique #1', $fleur->getNom());
    }
}