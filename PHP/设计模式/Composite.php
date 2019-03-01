<?php
//+------组合模式-------+
//+------一组对象与该对象的单个实例的处理方式一致--------+

interface RenderableInterface
{
  public function render(): string;
}

class Form implements RenderableInterface
{
  private $elements;

  public function render(): string 
  {
    $formCode = '<form>';

    foreach ($this->elements as $element) {
      $formCode .= $element->render();
    }

    $formCode .= '</form>';
    return $formCode;
  }

  public function addElement(RenderableInterface $element)
  {
    $this->elements[] = $element;
  }
}

class InputElement implements RenderableInterface
{
  public function render(): string 
  {
    return '<input type="text" />';
  }
}

class TextElement implements RenderableInterface
{
  public function render(): string 
  {
    return '<textarea name=""></textarea>';
  }
}


$form = new Form();
$form->addElement(new InputElement());
$form->addElement(new TextElement());
echo $form->render();
