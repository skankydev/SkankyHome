<?php

namespace SkankyTest\TestCase\Form;

use PHPUnit\Framework\TestCase;
use SkankyDev\Form\FormBuilder;
use SkankyDev\Http\Routing\Router;
use SkankyDev\Http\UrlBuilder;

// Minimal concrete form for testing
class TestForm extends FormBuilder {
    public function build(): void {
        $this->add('name',  'text',   ['label' => 'Nom',   'rules' => ['required']]);
        $this->add('email', 'text',   ['label' => 'Email',  'rules' => ['email']]);
        $this->add('type',  'select', [
            'label'   => 'Type',
            'options' => ['a' => 'A', 'b' => 'B'],
        ]);
    }
}

class FormBuilderTest extends TestCase
{
    protected function setUp(): void {
        // Reset Singletons so each test starts clean
        $ref = new \ReflectionProperty(Router::class, '_instance');
        $ref->setValue(null, null);
        $ref = new \ReflectionProperty(UrlBuilder::class, '_instance');
        $ref->setValue(null, null);

        // Session superglobal must exist for Session::getAndClean()
        $_SESSION = [];

        Router::_findCurrentRoute('/test/index');
    }

    private function makeForm(): TestForm {
        return new TestForm();
    }

    public function testBuildPopulatesFields(): void {
        $form = $this->makeForm();
        $form->build();
        $fields = $form->getFields();
        $this->assertArrayHasKey('name',  $fields);
        $this->assertArrayHasKey('email', $fields);
        $this->assertArrayHasKey('type',  $fields);
    }

    public function testSetDataFillsValues(): void {
        $form = $this->makeForm();
        $form->setData(['name' => 'Simon', 'email' => 'simon@skankydev.com']);
        $this->assertEquals(['name' => 'Simon', 'email' => 'simon@skankydev.com'], $form->getData());
    }

    public function testSetDataAcceptsObject(): void {
        $obj = new \stdClass();
        $obj->name = 'Simon';
        $form = $this->makeForm();
        $form->setData($obj);
        $this->assertEquals(['name' => 'Simon'], $form->getData());
    }

    public function testValidateReturnsTrueOnValidData(): void {
        $form = $this->makeForm();
        $result = $form->validate(['name' => 'Simon', 'email' => 'simon@skankydev.com', 'type' => 'a']);
        $this->assertTrue($result);
        $this->assertEmpty($form->getErrors());
    }

    public function testValidateReturnsFalseOnInvalidData(): void {
        $form = $this->makeForm();
        $result = $form->validate(['name' => '', 'email' => 'not-an-email', 'type' => 'a']);
        $this->assertFalse($result);
        $this->assertNotEmpty($form->getErrors());
        $this->assertArrayHasKey('name', $form->getErrors());
    }

    public function testSetErrors(): void {
        $form = $this->makeForm();
        $form->build();
        $form->setErrors(['name' => ['Le champ est requis']]);
        $this->assertArrayHasKey('name', $form->getErrors());
        $this->assertTrue($form->getFields()['name']->hasErrors());
    }

    public function testSubmitIsChainable(): void {
        $form = $this->makeForm();
        $result = $form->submit('Enregistrer');
        $this->assertSame($form, $result);
    }

    public function testAddThrowsOnUnknownFieldType(): void {
        $this->expectException(\Exception::class);
        $form = $this->makeForm();
        $form->add('foo', 'nonexistent_type');
    }

    public function testRenderFieldThrowsWhenFieldMissing(): void {
        $this->expectException(\Exception::class);
        $form = $this->makeForm();
        $form->build();
        $form->renderField('does_not_exist');
    }
}
