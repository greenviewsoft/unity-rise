<?php

namespace App\Console\Commands;

use App\Models\Bounceemail;
use App\Models\Campaign;
use App\Models\Error;
use App\Models\History;
use App\Models\Processingemail;
use App\Models\Sentemail;
use App\Models\Setting;
use App\Models\Smtpprovider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;

class Emailsend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:emailsend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $campaigns = Campaign::where('schedule', '<', Carbon::now())
            ->where('status', '1')
            ->where('type', 'email')
            ->get();
        foreach ($campaigns as $campaign) {
            $processingMails = Processingemail::where('campagin_id', $campaign->id)->get();

            foreach ($processingMails as $processingMail) {
                $smtp_server = Smtpprovider::find($processingMail->smtp_id);

                $config = [
                    'driver' => 'smtp',
                    'host' => $smtp_server->hostname,
                    'port' => $smtp_server->port,
                    'username' => $smtp_server->username,
                    'password' => $smtp_server->password,
                    'encryption' => $smtp_server->connection,
                    'from' => ['address' => $processingMail->from_email, 'name' => $processingMail->sender_name],
                    'reply_to' => [
                        'address' => $processingMail->reply_to,
                        'name' => $processingMail->sender_name,
                    ],
                    'sendmail' => '/usr/sbin/sendmail -bs',
                    'pretend' => false,
                ];
                Config::set('mail', $config);

                try {
                    $email = $processingMail->recipient_email;
                    $from = $smtp_server->username;
                    $sender_name = $processingMail->sender_name;
                    $subject = $processingMail->subject;
                    $body = $processingMail->body;

                    $sendmal = Sentemail::orderBy('id', 'desc')->first();
                    if (isset($sendmal)) {
                        $mailid = $sendmal->id + 1;
                    } else {
                        $mailid = 1;
                    }

                    Mail::send(
                        'email.marketing',
                        [
                            'email' => $email,
                            'from' => $from,
                            'sender_name' => $sender_name,
                            'subject' => $subject,
                            'body' => $body,
                            'mailid' => $mailid,
                        ],
                        function ($m) use ($email, $from, $sender_name, $subject) {
                            $m->from($from, $sender_name);
                            $m->to($email)->subject($subject);
                        },
                    );

                    //success mail store database
                    $sentmail = new Sentemail();
                    $sentmail->user_id = $processingMail->user_id;
                    $sentmail->smtp_id = $smtp_server->id;
                    $sentmail->subject = $processingMail->subject;
                    $sentmail->body = $processingMail->body;
                    $sentmail->file = $processingMail->file;

                    $sentmail->reply_to = $processingMail->reply_to;
                    $sentmail->recipient_email = $processingMail->recipient_email;
                    $sentmail->from_email = $processingMail->from_email;
                    $sentmail->request_id = $processingMail->request_id;
                    $sentmail->campagin_id = $processingMail->campagin_id;
                    $sentmail->save();

                    $processingMail->delete();
                } catch (Exception $e) {
                    //push a error
                    $error = new Error();
                    $error->user_id = $processingMail->user_id;
                    $error->type = 'email';
                    $error->message = $e->getMessage();
                    $error->save();

                    //failed mail store database
                    $sentmail = new Bounceemail();
                    $sentmail->user_id = $processingMail->user_id;
                    $sentmail->smtp_id = $smtp_server->id;
                    $sentmail->subject = $processingMail->subject;
                    $sentmail->body = $processingMail->body;
                    $sentmail->file = $processingMail->file;

                    $sentmail->reply_to = $processingMail->reply_to;
                    $sentmail->recipient_email = $processingMail->recipient_email;
                    $sentmail->from_email = $processingMail->from_email;
                    $sentmail->request_id = $processingMail->request_id;
                    $sentmail->campagin_id = $processingMail->campagin_id;
                    $sentmail->save();

                    $processingMail->delete();
                }
            }
        }

        $this->cronHit();
    }

    public function cronHit()
    {
        // Create a Guzzle client
        $client = new Client();

        // Specify the API endpoint
        $apiEndpoint = $cron->url;

        // Make a GET request to the external API
        $response = $client->get($apiEndpoint);

        // Get the response body as a string
        $responseBody = $response->getBody()->getContents();
    }
}
