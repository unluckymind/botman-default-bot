<?php

namespace App\Conversations;

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class MenuConversation extends Conversation
{
    public function askMenu()
    {
        $question = Question::create('Silahkan pilih menu berikut:')
        ->fallback('gagal memilih menu :( ')
        ->callbackId('pilih menu')
        ->addButtons([
            Button::create('Discount Product')->value('discount_product'),
            Button::create('Popular Product')->value('popular_product'),
        ]);

        $this->ask($question, function (Answer $answer) {
            if($answer->getText() === "discount_product"){
                $attachment = new Image('https://cdn.shopify.com/s/files/1/0053/2899/4419/files/flashsale.jpg?1505');
                $message = OutgoingMessage::create('produk diskon terbesar')->withAttachment($attachment);
                $this->say($message);
            }
            if($answer->getText() === "popular_product"){
                $attachment = new Image('https://amp.businessinsider.com/images/5cd45866021b4c45da371a87-750-563.jpg');
                $message = OutgoingMessage::create('produk paling populer ')->withAttachment($attachment);
                $this->say($message);
            }
        });
    }

    public function run()
    {
        $this->askMenu();
    }
}
