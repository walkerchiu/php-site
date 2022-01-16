<?php

namespace WalkerChiu\Site\Models\Services;

use WalkerChiu\Core\Models\Exceptions\NotMailableException;

class EmailTemplateService
{
    /**
     * EmailType.
     *
     * @var String
     */
    public $type;



    /**
     * Create a new service instance.
     *
     * @param String  $type
     * @return void
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Load email template.
     *
     * @param String  $item
     * @param String  $path
     * @param Mixed   $email_template
     * @return Array
     *
     * @throws NotMailableException
     */
    public function loadTemplate(string $item, string $path, $email_template = 0)
    {
        libxml_use_internal_errors(true);

        if (empty($item))
            return [
                'style'   => $this->loadTemplate('style'),
                'header'  => $this->loadTemplate('header'),
                'content' => $this->loadTemplate('content'),
                'footer'  => $this->loadTemplate('footer')
            ];

        $value = '';
        $view = view($path.'.'.$email_template.'.template_'.$this->type)
                    ->render();
        if (empty($view))
            throw new NotMailableException($view);

        $dom = new \DOMDocument();
        $dom->loadXML($view);
        if (count(libxml_get_errors())) {
            //
        }

        $element = $dom->getElementsByTagName($item)[0];
        foreach ($element->childNodes as $child)
            $value .= $child->ownerDocument->saveXML($child);

        return trim($value);
    }
}
