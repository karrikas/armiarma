<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Util\DomainFinder;
use AppBundle\Util\Url;

class DomainCommand extends Command
{
    public $domains = [];
    public $urls = [];

    protected function configure()
    {
        $this
            ->setName('app:find-domains')
            ->setDescription('Find Domain in a web page')
            ->addArgument('query', InputArgument::REQUIRED, 'Query start finding')
            ->addOption('deep', 'd', InputOption::VALUE_REQUIRED, 'How deep do you want go?', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $input->getArgument('query');
        $query = urlencode($query);
        $url = 'https://www.google.es/search?q='.$query.'&num=100&pws=0&hl=es';

        $this->deep = $input->getOption('deep');
        $this->output = $output;

        $dFinder = new DomainFinder();
        $urls = $dFinder->find($url);
        foreach ($urls as $key => $nUrl) {
            if (preg_match('/^\/url\?\q=([^&]+)/', $nUrl, $res)) {
                $this->finder($res[1]);
                $output->writeln($res[1]);
            }
        }
    }

    protected function finder($url, $currentDeep = 1)
    {
        if ($this->deep < $currentDeep) {
            return false;
        }

        if ($currentDeep > 1 && preg_match('/(google|goo\.gl)/', $url)) {
            return false;
        }

        $this->output->writeln('download: '.$url.' deep: '.$currentDeep);
        file_put_contents(ROOTDIR.'/var/urls.txt', $url."\n", FILE_APPEND | LOCK_EX);

        $dFinder = new DomainFinder();
        $urls = $dFinder->find($url);

        foreach ($urls as $key => $nUrl) {
            $return = Url::urlize($nUrl, $url);

            if (empty($return)) {
                continue;
            }

            $domain = Url::getDomain($return);
            if (!$domain) {
                continue;
            }

            if (!in_array($domain, $this->domains)) {
                $this->domains[] = $domain;
                $this->saveDomain($domain);
            }

            if (!in_array($return, $this->urls)) {
                $this->urls[] = $return;
            }

            $this->finder($return, $currentDeep+1);
        }
    }

    protected function saveDomain($domain)
    {
        $path = ROOTDIR.'/var/domains.txt';
        $domains = file_get_contents($path);
        $escDomain = preg_quote($domain, '/');
        if (!preg_match_all("/$escDomain/", $domains)) {
            file_put_contents($path, $domain."\n", FILE_APPEND | LOCK_EX);
        }
    }
}
