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
@if ($subject!='Documents Fill Reminder')

<div style="margin-top:10px;">
    <div style="background-color: #00607f; padding: 6px; color: white; font-size: 20px;width:100% ">
        User Logs
    </div>

    <table style="width:100%">
        <tr style="background-color: #f1f1f1; padding: 6px; color: black; font-size: 16px; float: left; width: 100%;">
            <td style="width:30%;float:left">
               Username:
            </td>
            <td style="width:70%">
                {{$user->email}}
            </td>
        </tr>
        <tr style="background-color: #f1f1f1; padding: 6px; color: black; font-size: 16px; float: left; width: 100%;">
            <td style="width:30%;float:left">
               Password:
            </td>
            <td style="width:70%">
                <code>{{$user->rawPassword}}</code>
            </td>
        </tr>
    </table>
</div>
@endif
<p>
    Please Click on the Below Link to Login & upload your documents
</p>
<p>
    <a href="{{$link}}" target="_blank"><button>Click Here</button></a>
</p>
<p>
   Regards,<br>
   EMS - IKA<br>
</p>
</body>
</html>
