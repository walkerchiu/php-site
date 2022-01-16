<?php

namespace WalkerChiu\Site\Models\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Exceptions\NotFoundEntityException;
use WalkerChiu\Core\Models\Exceptions\NotMailableException;
use WalkerChiu\Core\Models\Services\CheckExistTrait;
use WalkerChiu\Site\Models\Services\EmailSenderJob;
use WalkerChiu\Site\Models\Services\SiteService;

class EmailService
{
    use CheckExistTrait;

    protected $repository;
    protected $channels;



    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = App::make(config('wk-core.class.site.emailRepository'));
        $this->channels = [];
    }

    /**
     * Send email to someone.
     *
     * @param Site          $site
     * @param String        $emailType
     * @param Array|String  $addressTypes
     * @param Mixed|User    $receivers
     * @param Array         $parameters
     * @param User          $sender
     * @param Mixed|User    $replyTo
     * @param Mixed|User    $cc
     * @param Mixed|User    $bcc
     * @return Bool
     *
     * @throws NotFoundEntityException
     * @throws NotMailableException
     */
    public function email($site, string $emailType, $addressTypes, $receivers, $parameters = [], $sender = null, $replyTo = [], $cc = [], $bcc = []): bool
    {
        if (empty($receivers))
            throw new NotMailableException($receivers);

        $email = $site->email($emailType);
        if (empty($email))
            throw new NotFoundEntityException($site);

        $from = empty($sender) ? $this->buildCard($site, 'site', $site->language)
                               : $this->buildCard($sender, 'contact', $site->language);
        $replyTo  = $this->buildCardCollection($replyTo, 'contact', $site->language);
        $cc       = $this->buildCardCollection($cc, 'contact', $site->language);
        $bcc      = $this->buildCardCollection($bcc, 'contact', $site->language);

        $channels = $this->buildChannel($site, $receivers);
        $flag = true;
        foreach ($channels as $key => $channel) {
            if (empty($channel))
                continue;

            $recipients = $this->buildCardCollection($channel, $addressTypes, $key);
            if (empty($recipients))
                continue;

            $flag = false;
            $parameters = $this->loadTemplate($site, $email, $recipients, $from, $key, $parameters);
            EmailSenderJob::dispatch($parameters, $recipients, $from, $replyTo, $cc, $bcc);
        }

        if ($flag)
            throw new NotMailableException($receivers);

        return true;
    }

    /**
     * Build channels.
     *
     * @param Site        $site
     * @param Mixed|User  $receivers
     * @return Array
     */
    public function buildChannel($site, $receivers): array
    {
        foreach ($site->language_supported as $language)
            $this->channels = array_merge($this->channels, [$language => []]);

        return $this->categorizeReceivers($site, $receivers);
    }

    /**
     * Categorize receivers.
     *
     * @param Site        $site
     * @param Mixed|User  $receiver
     * @return Array
     */
    public function categorizeReceivers($site, $receiver): array
    {
        if (is_iterable($receiver))
            return categorizeReceivers($site, $receiver);

        if (config('wk-site.onoff.account')) {
            if (array_key_exists($receiver->profile->language, $this->channels))
                array_push($this->channels[$receiver->profile->language], $receiver);
            else
                array_push($this->channels[$site->language], $receiver);
        } else {
            array_push($this->channels[$site->language], $receiver);
        }

        return $this->channels;
    }

    /**
     * Load email template.
     *
     * @param Site               $site
     * @param Email              $email
     * @param Mixed|User|String  $recipients
     * @param User               $sender
     * @param String             $language
     * @param Array              $variables
     * @return Array
     */
    public function loadTemplate($site, $email, $recipients, $sender, string $language, array $variables): array
    {
        $nowtime = Carbon::now()->setTimezone($site->timezone);
        if (config('wk-core.datetime.onoff'))
            $nowtime->format(config('wk-core.datetime.format'));

        $service = new SiteService();
        $site_address = $service->getSiteAddress();

        $variables = array_merge($variables, [
            'site_name'                  => $site->findLang($language, 'name'),
            'site_vat'                   => $site->vat,
            'site_timezone'              => $site->timezone,
            'site_address_email'         => empty($site_address) ? '' : $site_address['email'],
            'site_address_phone'         => empty($site_address) ? '' : $site_address['phone'],
            'site_address_area'          => empty($site_address) ? '' : $site_address['area'],
            'site_address_address_line1' => empty($site_address) ? '' : $site_address['address_line1'],
            'site_address_address_line2' => empty($site_address) ? '' : $site_address['address_line2'],
            'site_address_guide'         => empty($site_address) ? '' : $site_address['guide'],
            'nowtime'                    => $nowtime
        ]);
        if (count($recipients) == 1)
            $variables = array_merge($variables, [
                'recipient_username'      => $recipients[0]['username'],
                'recipient_name'          => $recipients[0]['name'],
                'recipient_email'         => $recipients[0]['email'],
                'recipient_phone'         => $recipients[0]['phone'],
                'recipient_area'          => $recipients[0]['area'],
                'recipient_address_line1' => $recipients[0]['address_line1'],
                'recipient_address_line2' => $recipients[0]['address_line2'],
                'recipient_guide'         => $recipients[0]['guide']
            ]);

        return [
            'language' => $language,
            'subject'  => $email->findLang($language, 'subject') ?? $email->findLang($site->language, 'subject'),
            'email_theme'  => $site->email_theme,
            'email_style'  => $site->findLang($language, 'email_style') ?? $site->findLang($site->language, 'email_style'),
            'email_header' => $site->findLang($language, 'email_header') ?? $site->findLang($site->language, 'email_header'),
            'email_footer' => $site->findLang($language, 'email_footer') ?? $site->findLang($site->language, 'email_footer'),
            'style'   => $email->findLang($language, 'style') ?? $email->findLang($site->language, 'style'),
            'header'  => $email->findLang($language, 'header') ?? $email->findLang($site->language, 'header'),
            'content' => $email->findLang($language, 'content') ?? $email->findLang($site->language, 'content'),
            'footer'  => $email->findLang($language, 'footer') ?? $email->findLang($site->language, 'footer'),
            'smtp' => [
                'mail.host'       => $site->smtp_host,
                'mail.port'       => $site->smtp_port,
                'mail.encryption' => $site->smtp_encryption,
                'mail.username'   => $site->smtp_username,
                'mail.password'   => $site->smtp_password,
                'mail.from' => [
                    'address' => $sender['email'], 
                    'name'    => $sender['name'],
                ]
            ],
            'variables' => $variables
        ];
    }

    /**
     * Build address card collection of user.
     *
     * @param Mixed|User|String  $users
     * @param Array|String       $addressTypes
     * @param String             $language
     * @return Array
     */
    public function buildCardCollection($users, $addressTypes, string $language): array
    {
        $collection = [];
        if (!is_iterable($users))
            $users = [$users];
        foreach ($users as $user) {
            $card = $this->buildCard($user, $addressTypes, $language);
            if (!empty($card))
                array_push($collection, $card);
        }

        return $collection;
    }

    /**
     * Build address card of user.
     *
     * @param User|String   $entity
     * @param Array|String  $addressTypes
     * @param String        $language
     * @return Array
     *
     * @throws NotMailableException
     */
    public function buildCard($entity, $addressTypes, string $language): array
    {
        if (empty($entity))
            return [];
        if (is_string($entity)) {
            if (filter_var($entity, FILTER_VALIDATE_EMAIL)) {
                return [
                    'username' => '',
                    'name'     => $entity,
                    'email'    => $entity,
                    'address'  => $entity,
                    'phone'         => '',
                    'area'          => '',
                    'address_line1' => '',
                    'address_line2' => '',
                    'guide'         => ''
                ];
            } else {
                throw new NotMailableException($entity);
            }
        }

        if (
            $addressTypes != 'user'
            && config('wk-site.onoff.morph-address')
        ) {
            $address = null;
            if (is_string($addressTypes))
                $addressTypes = [$addressTypes];
            foreach ($addressTypes as $type) {
                if ($type == 'site') {
                    $address = $entity->addresses($type)->first();
                    if (empty($address))
                        return [];
                    if (!filter_var($address->email, FILTER_VALIDATE_EMAIL))
                        throw new NotMailableException($address->email);
                    return [
                        'username' => $address->findLang($language, 'name') ?? $entity->identifier,
                        'name'     => $address->findLang($language, 'name') ?? $entity->identifier,
                        'email'    => $address->email,
                        'address'  => $address->email,
                        'phone'         => $address->phone,
                        'area'          => $address->area,
                        'address_line1' => $address->findLang($language, 'address_line1'),
                        'address_line2' => $address->findLang($language, 'address_line2'),
                        'guide'         => $address->findLang($language, 'guide')
                    ];
                } else {
                    if (
                        config('wk-site.onoff.account')
                        && $entity->profile
                        && method_exists($entity->profile, 'addresses')
                    ) {
                        $address = $entity->profile->addresses($type)->first();
                    }
                    if (
                        empty($address)
                        && method_exists($entity, 'addresses')
                    ) {
                        $address = $entity->addresses($type)->first();
                    }
                    if (!empty($address))
                        break;
                }
            }
            if (empty($address))
                return [];
            if (!filter_var($address->email, FILTER_VALIDATE_EMAIL))
                throw new NotMailableException($address->email);

            return [
                'username' => $entity->name,
                'name'     => $address->findLang($language, 'name'),
                'email'    => $address->email,
                'address'  => $address->email,
                'phone'         => $address->phone,
                'area'          => $address->area,
                'address_line1' => $address->findLang($language, 'address_line1'),
                'address_line2' => $address->findLang($language, 'address_line2'),
                'guide'         => $address->findLang($language, 'guide')
            ];
        } else {
            if (!filter_var($entity->email, FILTER_VALIDATE_EMAIL))
                throw new NotMailableException($entity->email);
            return [
                'username' => $entity->name,
                'name'     => $entity->name,
                'email'    => $entity->email,
                'address'  => $entity->email,
                'phone'         => '',
                'area'          => '',
                'address_line1' => '',
                'address_line2' => '',
                'guide'         => ''
            ];
        }
    }
}
