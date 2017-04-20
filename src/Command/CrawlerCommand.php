<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Stash\Driver\FileSystem;
use Stash\Pool;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use AppBundle\Util\Url;
use AppBundle\Util\Html;

class CrawlerCommand extends Command
{
    private $urls = [];
    private $visitedUrls = [];
    private $domainError = [];

    protected function configure()
    {
        $this
            ->setName('app:crawl')
            ->setDescription('Crawl all pages in a domain')
            ->addArgument('url', InputArgument::REQUIRED, 'url to crawl, http://www.example.com');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $driver = new FileSystem(array('path' => __DIR__.'/../../var/cache/'));
        $cache = new Pool($driver);
        $client = new guzzleclient(array('timeout' => 60));

        $url = $input->getArgument('url');
        $domain = Url::getDomain($url);
        if (!$domain) {
            return $output->writeln('<error>wrong url!</error>');
        }

        $this->urls[] = $domain;
        $io->title("Start crawling $domain");

        while (count($this->urls) > 0) {
            $currentUrl = array_shift($this->urls);
            $this->visitedUrls[] = $currentUrl;

            $item = $cache->getItem(md5($currentUrl));
            $data = $item->get();
            $status = 'cache';

            try {
                if ($item->isMiss()) {
                    $item->lock();

                    $res = $client->request('GET', $currentUrl);
                    $data = (string) $res->getBody();
                    $status = $res->getStatusCode();

                    $cache->save($item->set($data));
                }
            } catch (\Exception $e) {
                continue;
            }

            //$io->note("crawl: $currentUrl status: ".$status);
            $urls = Html::findUrls($data);

            foreach ($urls as $url) {
                $url = Url::urlize($url, $currentUrl);

                if ($this->isExternal($url, $domain)) {
                    $externalDomain = Url::getDomain($url);
                    if (!$this->saveUrl($externalDomain)) {
                        continue;
                    }
                    try {
                        $res = $client->request('GET', $externalDomain);
                        $status = $res->getStatusCode();
                        if ($status >= 400) {
                            $io->text($externalDomain);
                            $this->domainError[] = $externalDomain;
                        }
                    } catch (\Exception $e) {
                        $io->text($externalDomain);
                        $this->domainError[] = $externalDomain;
                    }

                    continue;
                }
                $this->saveUrl($url);
            }
        }
    }

    protected function saveUrl($url)
    {
        if (in_array($url, $this->urls)) {
            return false;
        }

        if (in_array($url, $this->visitedUrls)) {
            return false;
        }

        $this->urls[] = $url;

        return true;
    }

    protected function isExternal($url, $domain)
    {
        if (strpos($url, $domain) !== 0) {
            return true;
        } else {
            return false;
        }
    }
}
