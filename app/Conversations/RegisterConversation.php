<?php

namespace App\Conversations;

use Validator;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class RegisterConversation extends Conversation
{
    protected $name;
    protected $instagram;
    protected $email;
    protected $whatsapp;
    protected $transactionPerDay;

    public function selfIntroduce()
    {
        $this->say("👋 Hai, saya Hana, asisten virtual Halosis.\nSaya akan bantu kamu bergabung di Halosis 📘\nProsesnya gampang loh!");
        $this->askRegistration();
    }

    public function askRegistration()
    {
        $question = Question::create('Ingin Mendaftar?')
        ->fallback('gagal memilih menu :( ')
        ->callbackId('daftar')
        ->addButtons([
            Button::create('Daftar sekarang')->value('yes'),
            Button::create('Tidak')->value('no'),
            ]);

        $this->ask($question, function (Answer $answer)    {
            if($answer->getText() == "yes"){
                $this->askName();
            }else{
                $this->say('Ok deh! Kalau kamu berubah pikiran, nanti kasih tau Hana kalau mau "daftar" ya!');
                $this->say("Hana dan teman-teman di HALOSIS mau jadi sahabat UKM untuk mengembangkan usaha kamu.\n\nJangan sungkan untuk hubungi kami ya!\n\nSampai jumpa! 👋");
            }
        });
    }

    public function askName()
    {
        $this->ask('Siapa nama kamu?', function(Answer $answer) {
            $this->bot->userStorage()->save([
                'name' => $answer->getText(),
            ]);
            $this->askInstagram();
        });
    }

    public function askInstagram()
    {
        $this->ask('apa akun instagram kamu? ', function(Answer $answer) {
            $this->bot->userStorage()->save([
                'instagram' => $answer->getText(),
            ]);
            $this->askEmail();
        });
    }

    public function askEmail()
    {
        $this->ask('Apa alamat email kamu?', function(Answer $answer) {

            $validator = Validator::make(['email' => $answer->getText()], [
                'email' => 'email',
            ]);

            if ($validator->fails()) {
                return $this->repeat('Mohon maaf kamu belum mengisi email dengan benar, Mohon mencoba kembali');
            }

            $this->bot->userStorage()->save([
                'email' => $answer->getText(),
            ]);

            $this->askWhatsapp();
        });
    }

    public function askWhatsapp()
    {
        $this->ask('Berapa nomor whatsapp kamu?', function(Answer $answer) {

            $validator = Validator::make(['whatsapp' => $answer->getText()], [
                'whatsapp' => 'numeric',
            ]);

            if ($validator->fails()) {
                return $this->repeat('Mohon maaf kamu belum mengisi nomor whatsapp dengan benar, Mohon mencoba kembali');
            }

            $this->bot->userStorage()->save([
                'number' => $answer->getText(),
            ]);

            $this->askTransaction();
        });
    }

    public function askTransaction()
    {
        $question = Question::create('Berapa banyak transaksi kamu dalam 1 bulan?')
        ->fallback('gagal memilih menu :( ')
        ->callbackId('pilih')
        ->addButtons([
            Button::create('Dibawah 100')->value('low_transaction'),
            Button::create('101 - 300')->value('middle_transaction'),
            Button::create('Diatas 300')->value('high_transaction'),
            ]);
            $this->ask($question, function (Answer $answer)
            {
                if($answer->getText() === "low_transaction"){
                    $this->bot->userStorage()->save([
                        'transaction' => 'kurang dari 100',
                    ]);
                    $this->resultOf();
                }
                if($answer->getText() === "middle_transaction"){
                    $this->bot->userStorage()->save([
                        'transaction' => "lebih dari 100 dan kurang dari 300",
                    ]);
                    $this->resultOf();
                }
                if($answer->getText() === "high_transaction"){
                    $this->bot->userStorage()->save([
                        'transaction' => ' lebih dari 300',
                    ]);
                    $this->resultOf();
                }
            });
    }

    public function resultOf()
    {
        $user = $this->bot->userStorage()->find();

        $message = "\n-------------------------------------- \n";
        $message .= "Name : " . $user->get("name") . "\n";
        $message .= "Instagram : " . $user->get("instagram") . "\n";
        $message .= "Email : " . $user->get("email") . "\n";
        $message .= "Whatsapp : " . $user->get("whatsapp") . "\n";
        $message .= "Transaksi per bulan : " . $user->get("transaction") . "\n";
        $message .= "--------------------------------------- \n";

        $this->say('informasi yang kamu isi: ' .$message);
    }

    public function run()
    {
        $this->selfIntroduce();
    }
}
