<?php

namespace WebDevBot\Commands;


use WebDevBot\Support\Logger;
use WebDevBot\Repositories\FacebookRepository;
use Symfony\Component\Console\Command\Command;
use WebDevBot\Validators\HashtagValidatorsTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReportNonCompliantPostsCommand extends Command
{
    use HashtagValidatorsTrait;

    /* @var FacebookRepository $facebook */
    private $facebook;

    /* @var Logger $logger */
    private $logger;

    private $reportedPostsIds = [];

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->facebook = new FacebookRepository();
        $this->logger = new Logger();

        parent::initialize($input, $output);
    }

    protected function configure()
    {
        $this
            ->setName('report')
            ->setDescription('Fetch the group to find posts to report.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $latestPosts = $this->facebook->getLatestPosts();

        foreach($latestPosts as $post) {
            if(!$this->areHashtagsOkFor($post)) {
                $postId = $this->extractPostId($post);

                $this->reportedPostsIds[] = $postId;
                $this->facebook->commentPostWithWarning($postId);
            }
        }

        file_put_contents('data.json', json_encode($this->reportedPostsIds));

        $this->logger->info(sprintf('Segnalati %s post non conformi al regolamento.', count($this->reportedPostsIds)));
    }

    private function extractPostId($post)
    {
        $postIdAsArray = explode('_', $post['id']);
        return $postIdAsArray[1];
    }
}
