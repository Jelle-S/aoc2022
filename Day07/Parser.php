<?php

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

  public function getDirectoryClosestToMinSize($spaceToFree): TreeNode {
    $directories = $this->getDirectoriesWithMinSize($spaceToFree);

    usort($directories, function (TreeNode $a, TreeNode $b) {
      return $a->getSize() - $b ->getSize();
    });

    return reset($directories);
  }

  public function getDirectoriesWithMinSize($minSize, ?TreeNode $tree = null) {
    if (!$tree) {
      $tree = $this->getRoot();
    }

    $directories = [];
    if ($tree->getSize() >= $minSize && $tree->getType() === TreeNode::TYPE_DIRECTORY) {
      $directories[] = $tree;
    }

    foreach ($tree->getChildren() as $child) {
      $directories = array_merge($directories, $this->getDirectoriesWithMinSize($minSize, $child));
    }

    return $directories;
  }

}