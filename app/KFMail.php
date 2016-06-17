<?php

namespace App;

use Weblee\Mandrill\Mail;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */
class KFMail
{
    protected $templateData;
    protected $data;
    protected $useMandrill;
    protected $mandrillDebug;

    /**
     * @var Mail
     */
    private $mandrill;

    /**
     * KFMail constructor.
     * Set required and boilerplate data needed for Manrdill templates
     * @param Mail $mandrill
     */
    public function __construct(Mail $mandrill)
    {

        // we sometimes want to use mailgun in dev/qa because it's cheaper
        $this->useMandrill = env('MANDRILL_ENABLED', false);
        $this->mandrillDebug = env('MANDRILL_DEBUG', false);

        $this->mandrill = $mandrill;
        $this->data = [
            'from_email' => 'noreply@kidgifting.com',
            'from_name' => 'Kidgifting',
            'track_opens' => true,
            'track_clicks' => true,
        ];

        $this->templateData = [
            [
                'name' => 'current-year',
                'content' => date("Y")
            ],
            [
                'name' => 'company',
                'content' => 'Kidgifting, Inc.'
            ],
            [
                'name' => 'description',
                'content' => 'TKTK DESCRIPTION'
            ],
            [
                'name' => 'list_address_html',
                'content' => "93 Sterling Place<br>Brooklyn, NY 11217"
            ],
        ];


    }

    /**
     * Take template data and wrap in Mandrill format
     * Merge data with boilerplate
     * @param $template
     * @param $content
     * @param $subject
     * @param $toEmail
     * @param null $toName
     */
    private function sendTemplate($template, $content, $subject, $toEmail, $toName=null)
    {
        $templateData = [
            [
                'name' => 'kfbody',
                'content' => $content
            ]
        ];

        $data = [
            'subject' => $subject,
            'to' => [
                [
                    'email' => $toEmail,
                    'name' => $toName,
                    'type' => 'to'
                ]
            ]
        ];

        $fullTemplateData = [array_merge($templateData, $this->templateData)];
        $fullData = array_merge($data, $this->data);

        if ($this->useMandrill) {
            $this->mandrill->messages()->debug = true;
            $response = $this->mandrill->messages()->sendTemplate($template,
                $fullTemplateData[0],
                $fullData);

        } else {

            // we sometimes want to use mailgun in dev/qa because it's cheaper
            $to = $data['to'][0]['email'];
            $subject = $data['subject'];

            $mailgunData = print_r(array_merge($fullTemplateData, $fullData), true);
            \Illuminate\Support\Facades\Mail::send('emails.raw',
                ['data' => $mailgunData],
                function ($message) use ($to, $subject) {
                    $message->from('noreply@kidgifting.com', 'kidgifting');
                    $message->to($to)->subject($subject);
                });
        }

    }

    /**
     * @param $toEmail
     * @param $code
     * @param null $toName
     */
    public function sendBetaCodeMail($toEmail, $code, $toName = null)
    {

        $url = env('BRANCH_BETA_URL', 'https://link.kidgifting.com/dev-beta');
        $url .= "?\$deeplink_path=%2Fkidgifting%2Fbetacode%2F$code";
        
        $template = 'vignelli-1-for-mandrill';
        $subject = 'Welcome to Kidgifting!';
        $content = view('emails.betacode', ['url' => $url])
            ->render();

        return $this->sendTemplate($template, $content, $subject, $toEmail, $toName);
    }

    /**
     * @param $toEmail
     * @param $code
     * @param null $toName
     */
    public function sendInviteCodeMail($toEmail, $code, $toName = null)
    {

        $url = env('BRANCH_BETA_URL', 'https://link.kidgifting.com/dev-beta');
        $url .= "?\$deeplink_path=%2Fkidgifting%2Finvite%2F$code";
        
        $template = 'vignelli-1-for-mandrill';
        $subject = 'Welcome to Kidgifting!';
        $content = view('emails.invite', ['url' => $url])
            ->render();

        return $this->sendTemplate($template, $content, $subject, $toEmail, $toName);
    }

    /**
     * @param $toEmail
     * @param $subject
     * @param $body
     * @param null $toName
     */
    public function sendTemplatedMail($toEmail, $subject, $body, $toName=null)
    {
        $template = 'vignelli-1-for-mandrill';
        return $this->sendTemplate($template, $body, $subject, $toEmail, $toName);
    }

    /**
     * @param $subject
     * @param $body
     * @param null $toName
     */

    // TODO refactor to use sendTemplate
    // TODO Why was all that logic duplicated?
    public function sendRequestBetacodeMail($mail, $subject, $body)
    {
        $template = 'vignelli-1-for-mandrill';
        // return $this->sendTemplate($template, $body, $subject, $toEmail, $toName);

        $templateData = [
            [
                'name' => 'kfbody',
                'content' => $body
            ]
        ];

        $data = [
            'subject' => $subject,
            'to' => [
                [
                    'email' => 'admin@kidgifting.com',
                    'name' => 'kidgifting',
                    'type' => 'to'
                ]
            ]
        ];

        $fullTemplateData = [array_merge($templateData, $this->templateData)];
        $fullData = array_merge($data, $this->data);

        if ($this->useMandrill) {
            $this->mandrill->messages()->debug = true;
            $response = $this->mandrill->messages()->sendTemplate($template,
                $fullTemplateData[0],
                $fullData);

        } else {

            // we sometimes want to use mailgun in dev/qa because it's cheaper
            $to = $data['to'][0]['email'];
            $subject = $data['subject'];

            $mailgunData = print_r(array_merge($fullTemplateData, $fullData), true);
            \Illuminate\Support\Facades\Mail::send('emails.raw',
                ['data' => $mailgunData],
                function ($message) use ($mail, $to, $subject) {
                    $message->from($mail, 'User');
                    $message->to($to)->subject($subject);
                });
        }
    }
}
