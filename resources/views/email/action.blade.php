<html>
<head>
    <style>
        body
        {
            font-family: monospace;
        }
    </style>
</head>
<body>
<p>
   Hi,<br><br>

</p>
     
<p>
    {!! $emailMessage !!}
    @if (!empty($object) && $object->forwarded)
       for {!! $object->user->name !!}
    @endif

</p>
<p>Click <a href="{{$link}}">here</a></p>

  

<p>
   Regards,<br>
   EMS<br>
</p>
</body>
</html>