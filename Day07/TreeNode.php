<?php

class TreeNode {
  protected $label;
  protected $size = 0;
  protected $type = '';
  protected $parent = null;
  protected $children = [];
  const TYPE_DIRECTORY = 'dir';
  const TYPE_FILE = 'file';

  public function __construct($label, $type) {
    $this->label = $label;
    $this->type = $type;
  }

  public function getLabel() {
    return $this->label;
  }

  public function getType() {
    return $this->type;
  }

  public function getSize() {
    return $this->size;
  }

  public function getParent() {
    return $this->parent;
  }

  public function addSize($size) {
    $this->size += $size;
    if ($this->parent) {
      $this->parent->addSize($size);
    }
  }

  public function setParent(TreeNode $parent) {
    $this->parent = $parent;
  }

  public function addChild(TreeNode $child) {
    $this->children[$child->getLabel()] = $child;
    $this->addSize($child->getSize());
    $child->setParent($this);
  }

  public function getChild($label) {
    return $this->children[$label];
  }

  public function getChildren() {
    return $this->children;
  }

}
