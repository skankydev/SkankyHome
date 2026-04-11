<?php

namespace SkankyTest\TestCase\Utilities;

use PHPUnit\Framework\TestCase;
use SkankyDev\Utilities\Paginator;

class PaginatorTest extends TestCase
{
    private array $data;
    private array $option;

    protected function setUp(): void {
        $this->data = range(0, 10);
        $this->option = [
            'limit' => 10,
            'page'  => 1,
            'total' => 184,
            'range' => 5,
            'sort'  => ['slug' => 1],
        ];
    }

    public function testGetOptionComputesPaginationInfo(): void {
        $paginator = new Paginator($this->data, $this->option);
        $result    = $paginator->getOption();

        $this->assertEquals(19, $result['pages']);
        $this->assertEquals(1,  $result['first']);
        $this->assertEquals(19, $result['last']);
        $this->assertEquals(2,  $result['next']);
        $this->assertEquals(1,  $result['prev']);
        $this->assertEquals(1,  $result['start']);
        $this->assertEquals(4,  $result['stop']);
        // getOption() always attaches link and get keys
        $this->assertArrayHasKey('link', $result);
        $this->assertArrayHasKey('get',  $result);
    }

    public function testGetOptionAttachesLinkAndGet(): void {
        $paginator = new Paginator($this->data, $this->option);
        $link = ['controller' => 'Module', 'action' => 'index'];
        $get  = ['search' => 'test'];
        $result = $paginator->getOption($link, $get);

        $this->assertEquals($link, $result['link']);
        $this->assertEquals($get,  $result['get']);
    }

    public function testSortParamsTogglesOrder(): void {
        $paginator = new Paginator($this->data, $this->option);

        // 'slug' is the current sort field (order 1) → clicking it inverts to -1
        $this->assertEquals(['page' => 1, 'field' => 'slug', 'order' => -1], $paginator->sortParams('slug'));

        // 'title' is not sorted → clicking it sets order to 1
        $this->assertEquals(['page' => 1, 'field' => 'title', 'order' => 1], $paginator->sortParams('title'));
    }

    public function testLastPageBoundary(): void {
        $paginator = new Paginator($this->data, [
            'limit' => 10,
            'page'  => 19,
            'total' => 184,
            'range' => 5,
            'sort'  => ['slug' => 1],
        ]);
        $result = $paginator->getOption();

        $this->assertEquals(19, $result['next']); // already on last page
        $this->assertEquals(18, $result['prev']);
    }

    public function testIterable(): void {
        $paginator = new Paginator($this->data, $this->option);
        $count = 0;
        foreach ($paginator as $item) {
            $count++;
        }
        $this->assertEquals(count($this->data), $count);
    }
}
