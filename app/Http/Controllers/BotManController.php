<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\OrderMenu;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\ElementButton;
use App\Conversations\MenuConversation;
use App\Conversations\RegisterConversation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BotManController extends Controller
{
    public function handle()
    {
        $botman = app('botman');

        $botman->hears('menu', function(BotMan $bot) {
            $bot->startConversation(new MenuConversation);
        });

        $botman->hears('hi', function (BotMan $bot) {
            $bot->startConversation(new RegisterConversation);
        });

        $botman->hears('{imgtxt}.png', function (BotMan $bot, $imgtxt) {
            $bot->reply("attach: \n" . $imgtxt .'.png');

        });

        $botman->hears('order', function (BotMan $bot) {
            $attachment = new Image('https://ecs7.tokopedia.net/img/cache/700/product-1/2018/12/14/1819553/1819553_0dc14200-ce63-4216-8ca5-850e04d5aaec_570_570.jpg');
            $message = OutgoingMessage::create('berikut ini dress yang kamu order: ')->withAttachment($attachment);
            $bot->reply($message);
        });

        $botman->hears('help', function($bot) {
            $bot->reply("Menu Bantuan Hana:\n\n===============================\nberikut ini list percakapan yang aku suka: \n\n1.order  : list order belanjaan kamu\n2.hi   : form registrasi member\n3.menu    : menampilkan menu pilihan produk\n4.help   : daftar bantuan untuk pengguna baru\n===============================\n\nYuk tanya hana!");
        });

        $botman->fallback(function($bot) {
            $bot->reply("Maaf, Hana tidak mengerti apa maksudnya.\n🤔 Mau Hana bantu untuk daftar atau kamu mau menghubungin tim HALOSIS?");
        });

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function chat()
    {
        return view('chatbot');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
}