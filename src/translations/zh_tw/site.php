<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site: Site
    |--------------------------------------------------------------------------
    |
    */

    'type'               => '種類',
    'serial'             => '編號',
    'vat'                => '統一編號',
    'identifier'         => '網域名稱',
        'identifier_placeholder' => '例如：example.com',
    'language'           => '語言',
    'language_supported' => '支援語系',
    'timezone'           => '時區',
    'area_supported'     => '支援地區',
    'currency_id'        => '預設貨幣',
    'currency_supported' => '支援貨幣',

    'smtp_host'          => '伺服器',
    'smtp_port'          => '連接埠',
    'smtp_encryption'    => '加密協議',
    'smtp_username'      => '帳號',
    'smtp_password'      => '密碼',

    'email_theme'        => '風格',
    'email_style'        => '樣式',
    'email_header'       => 'Header',
    'email_footer'       => 'Footer',

    'view_template'      => '版型模組（頁面）',
    'email_template'     => '版型模組（信件）',
    'skin'               => '風格樣式',

    'script_head'        => '在 Head 裡的 Script',
    'script_footer'      => '在 Footer 裡的 Script',
    'options'            => '選項',
    'images'             => '圖片',
    'can_guestOrder'     => '訪客是否可購買',
    'can_guestComment'   => '訪客是否可留言',
    'is_main'            => '是否為主要的',
    'is_enabled'         => '是否啟用',

    'name'               => '名稱',
    'description'        => '描述',
    'keywords'           => '關鍵字',
    'remarks'            => '備註',

    'list'   => '站臺列表',
    'create' => '新增站臺',
    'edit'   => '站臺修改',

    'form' => [
        'information' => '環境設定',
            'basicInfo'   => '基本資訊',
            'addressInfo' => '聯絡資訊',

        'icon&logo'   => '圖示和形象設定',

        'link'        => '社群設定',
        'smtp'        => 'SMTP 設定',
        'email' => [
            'header'  => 'Email 設定',
            'setting' => '樣板設定',
            'theme'   => '共用風格',
            'email'   => '共用內容'
        ],
        'layout' => [
            'header' => 'Layout 共用設定',
            'theme'  => '共用風格'
        ]
    ],

    'delete' => [
        'header' => '刪除站臺',
        'body'   => '確定要刪除這間站臺嗎？'
    ]
];
