<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject }}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>

<div class="container mt-5">

    <div class="card">
    
        <div class="card-body">

            <p>Hello {{$user->name }},</p>

            <div class="alert alert-success" role="alert">
                An asset - <strong>{{ $asset->assetSubType->name }}</strong> has been assigned to {{ $employee->user->name }}.
            </div>

            <table class="table">
            
                <tbody>
                    <tr>
                        <td>Barcode Number</td>
                        <td>{{ $asset->barcode ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Biometric Id</td>
                        <td>{{ $employee->biometric_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Assigned By</td>
                        <td>{{ $assignedBy }}</td>
                    </tr>
                </tbody>
            </table>

            <p>Regards,</p>
            <p>EMS</p>
            
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>

