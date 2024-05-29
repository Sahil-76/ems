<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
</head>
<body>
    <div style="width:100%">
        <h4>
            Dear {{$user->name}},
        </h4><br>
        <h2>User Account Generated</h2>
        <br/>
      
        <h3>
            Your Account is Generated at {{$user->created_at}}
        </h3>
       
       
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
                        {{$user->employee->office_email}}
                    </td>
                </tr>
                <tr style="background-color: #f1f1f1; padding: 6px; color: black; font-size: 16px; float: left; width: 100%;">
                    <td style="width:30%;float:left">
                       Password:
                    </td>
                    <td style="width:70%">
                        {{$employee->rawPassword}}
                    </td>
                </tr>
            </table>
        <h4>Click <a href="{{route('login')}}">Here</a> to login Using Above Logs</h4>
            <h5>Note: Consider Changing Password</h5>
        </div>

   </div>
  
</body>
</html>