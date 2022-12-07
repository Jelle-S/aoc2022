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

class Parser {
  protected TreeNode $root;
  protected TreeNode $current;
  protected bool $listing = false;

  public function __construct() {
    $this->root = new TreeNode('/', TreeNode::TYPE_DIRECTORY);
    $this->current = $this->root;
  }

  public function getRoot(): TreeNode {
    return $this->root;
  }

  public function parse($file) {
    $handle = fopen($file, 'r');
    while (($line = fgets($handle)) !== false) {
      // Build the tree.
      $this->parseLine(trim($line));
    }
  }

  protected function parseLine($line) {
    if (strpos($line, '$') === 0) {
      $this->parseCommand($line);
      return;
    }

    if ($this->listing) {
      $this->parseDirContent($line);
    }
  }

  protected function parseCommand($command) {
    $matches = [];
    preg_match('/^\$\s+(cd|ls)(.*)$/', $command, $matches);
    $this->listing = false;
    if ($matches[1] === 'cd') {
      $this->changeDirectory(trim($matches[2]));
    }

    if ($matches[1] === 'ls') {
      $this->listing = true;
    }
  }

  protected function changeDirectory($dir) {
    if ($dir === '..') {
      $this->current = $this->current->getParent();
      return;
    }

    if ($dir === '/') {
      $this->current = $this->root;
      return;
    }

    $this->current = $this->current->getChild($dir);
  }

  protected function parseDirContent($line) {
    $matches = [];
    preg_match('/^(\d+|dir)\s+(.*)$/', $line, $matches);
    if ($matches[1] === 'dir') {
      $this->current->addChild(new TreeNode($matches[2], TreeNode::TYPE_DIRECTORY));
      return;
    }
    $file = new TreeNode($matches[2], TreeNode::TYPE_FILE);
    $file->addSize($matches[1]);
    $this->current->addChild($file);
  }


  public function findDirectoriesWithMaxSize($maxSize, ?TreeNode $tree = null) {
    if (!$tree) {
      $tree = $this->getRoot();
    }

    $directories = [];
    if ($tree->getSize() <= $maxSize && $tree->getType() === TreeNode::TYPE_DIRECTORY) {
      $directories[] = $tree;
    }

    foreach ($tree->getChildren() as $child) {
      $directories = array_merge($directories, $this->findDirectoriesWithMaxSize($maxSize, $child));
    }

    return $directories;
  }

}



$parser = new Parser();
$parser->parse('input.txt');

$size = 0;
foreach ($parser->findDirectoriesWithMaxSize(100000) as $directory) {
  $size += $directory->getSize();
}

echo $size . PHP_EOL;