<?php

namespace WebDevBot\Commands;


use WebDevBot\Support\Logger;
use WebDevBot\Repositories\FacebookRepository;
use Symfony\Component\Console\Command\Command;
use WebDevBot\Validators\HashtagValidatorsTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessReportedPostsCommand extends Command
{
    use HashtagValidatorsTrait;

    /* @var FacebookRepository $facebook */
    private $facebook;

    /* @var Logger $logger */
    private $logger;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->facebook = new FacebookRepository();
        $this->logger = new Logger();

        parent::initialize($input, $output);
    }

    protected function configure()
    {
        $this
            ->setName('process')
            ->setDescription('Process reported facebook posts to unlock or delete them.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $deletedPostsCount = 0;
        $reportedPostsIds = json_decode(file_get_contents('data.json'), true);

        foreach($reportedPostsIds as $postId) {
            try {
                $post = $this->facebook->findPostById($postId);

                if(!$this->areHashtagsOkFor($post)) {
                    $this->facebook->deletePostById($postId);
                    $deletedPostsCount++;
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        $this->logger->info(sprintf('Rimossi %s post non conformi al regolamento.', $deletedPostsCount));
    }
}
