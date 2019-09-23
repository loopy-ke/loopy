<?php

namespace App\Console\Commands;


use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use PHPHtmlParser\Dom;

class TradeScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:trades';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrap data from meta trader statements';

    protected $urls = [
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=172985",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=176732",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=176740",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=176572",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=173443",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=175165",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=172847",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=171121",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=171470",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=156879",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=147293",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=176270",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=176846",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=174366",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=173447",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=174868",
        "https://www.forexfactory.com/explorerapi.php?content=tradeslist&id=173248",
    ];

    public function handle()
    {
        $telegram = new Telegram("886550947:AAHhiMlgboPotj_RSVK0pXZ8DRb9DF2OuRA", "TraidtsBot");
        $telegram->enableExternalMySql(\DB::connection()->getPdo());
        $telegram->handleGetUpdates();
        foreach ($this->urls as $url) {
            $key = 'traidts_data' . $url;
            if (!cache()->has($key)) {
                cache()->put($key, (new Client())->get($url)->getBody()->getContents(), Carbon::now()->addMinutes(5));
            }
            $data = json_decode(cache($key));
            if (isset($data->html)) {
                ini_set('memory_limit', '1024M');
                $dom = new Dom;
                $dom->load($data->html);
                $rows = $dom->find('.orders_open > table.explorer_tradeslist__table > tbody > tr.explorer_tradeslist__row');
                foreach ($rows as $row) {
                    $trade = $row->find('td');
                    $pair = $trade[0]->text;
                    $entry = trim($trade[2]->text);
                    $ticket = $row->getAttribute('data-ticket');
                    if (!cache()->has($ticket)) {
                        $results = Request::sendToActiveChats(
                            'sendMessage',
                            ['text' => "$pair\n$entry"],
                            [
                                'groups' => true,
                                'supergroups' => true,
                                'channels' => false,
                                'users' => true,
                            ]
                        );
                        cache()->put($ticket, $row, now()->addMonths(3));
                    }
                }
            }
        }
    }
}