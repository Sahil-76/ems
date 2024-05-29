<html>

<head>
    <style>
        body {
            font-family: monospace;
            color: white;
            margin: 0;
            background-color: #383457;
        }
        .name
        {
            font-size: 16px;
        }
        .wrap
        {
            padding: 10px;
        }
        .status
        {
            padding: 10px;
            background-color: white;
            color: #383457;
            width: auto;
            border-radius: 5px;
        }
        .response
        {
            border-bottom: 1px solid white;
        }
    </style>
</head>

<body>
    <div class="wrap">
    <label class="name">Profile Rejected </label>
     <br><br>
    <div class="status">Your  {{$data['field_name']}}  has been rejected and Update again</div>
    @if(!empty($data['remarks']))
    <br>
    <div class="status">Remarks:{{$data['remarks']}}</div>
    @endif
    </div>
    <hr>
    <div class="wrap">
    <p>
        Regards,<br>
        EMS - TKA.<br>
    </p>
    </div>
</body>

</html>