<?php
//+--------迭代器模式---------+
//+------让对象变得可迭代并表现得像对象集合------+
//+------例如：在文件中的所有行上逐行处理文件------+

/**
 * 继承 PHP 自带的用于日常任务的迭代器 Countable, Iterator
 */
class Book
{
  private $author;
  private $title;

  public function __construct(string $title, string $author)
  {
    $this->author = $author;
    $this->title = $title;
  }

  public function getAuthor(): string 
  {
    return $this->author;
  }

  public function getTitle(): string 
  {
    return $this->title;
  }

  public function getAuthorAndTitle(): string 
  {
    return $this->getTitle() . ' by ' . $this->getAuthor(); 
  }
}


class BookList implements \Countable, \Iterator
{
  private $books = [];
  private $currentIndex = 0;

  public function addBook(Book $book)
  {
    $this->books[] = $book;
  }

  public function removeBook(Book $bookToRemove)
  {
    foreach ($this->books as $key => $book) {
      if($book->getAuthorAndTitle() === $bookToRemove->getAuthorAndTitle()){
        unset($this->books[$key]);
      }
    }
    $this->books = array_values($this->books);
  }

  public function count(): int 
  {
    return count($this->books);
  }

  public function current(): Book 
  {
    return $this->books[$this->currentIndex];
  }

  public function key(): int 
  {
    return $this->currentIndex;
  }

  public function next()
  {
    $this->currentIndex++;
  }

  public function rewind()
  {
    $this->currentIndex = 0;
  }

  public function valid(): bool 
  {
    return isset($this->books[$this->currentIndex]);
  }
}

$books = new BookList();

for ($i=1; $i < 6; $i++) { 
  $books->addBook(new Book('title'.$i, 'author'.$i));
}

echo $books->count() . '<br>';
var_dump($books->current());
$books->next();
echo '<br>' . $books->key();

