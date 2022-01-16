<html>
    <head>
        <style>
            {!! $email_theme !!}
            {!! $email_style !!}
            {!! $style !!}
        </style>
    </head>
    <body>
        <div class="email_header">
            @if($header)
                {!! $header !!}
            @else
                {!! $email_header !!}
            @endif
        </div>
        <div class="email_content">{!! $content !!}</div>
        <div class="email_footer">
            @if($footer)
                {!! $footer !!}
            @else
                {!! $email_footer !!}
            @endif
        </div>
    </body>
</html>
