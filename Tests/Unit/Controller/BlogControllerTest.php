<?php

declare(strict_types=1);

namespace NITSAN\BlogSystem\Tests\Unit\Controller;

use TYPO3Fluid\Fluid\View\ViewInterface;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;

/**
 * Test case
 *
 * @author Arjun <arjunchavda@gmail.com>
 */
class BlogControllerTest extends UnitTestCase
{
    /**
     * @var \NITSAN\BlogSystem\Controller\BlogController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\NITSAN\BlogSystem\Controller\BlogController::class))
            ->onlyMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllBlogsFromRepositoryAndAssignsThemToView(): void
    {
        $allBlogs = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $blogRepository = $this->getMockBuilder(\::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $blogRepository->expects(self::once())->method('findAll')->will(self::returnValue($allBlogs));
        $this->subject->_set('blogRepository', $blogRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('blogs', $allBlogs);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenBlogToView(): void
    {
        $blog = new \NITSAN\BlogSystem\Domain\Model\Blog();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('blog', $blog);

        $this->subject->showAction($blog);
    }
}
