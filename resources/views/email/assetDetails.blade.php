<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
</head>
<body>
<div style="width: 100%">
    <p>Hi,</p>
    <p>{{$data['user_name']}} has been marked Exit, following are the details of the assets unassigned:</p>

    <strong>
        <h3>
            Asset Details:
        </h3>
    </strong>
    <div style="margin-top: 10px;">
        <table style="width: 100%">
            <thead>
            <tr style="background-color: #17365d; padding: 6px; color: #fff; font-size: 16px; width: 100%">
                <th style="padding: 12px; text-align: left !important;"> Asset Type </th>
                <th style="padding: 12px; text-align: left !important;"> Barcode </th>
                <th style="padding: 12px; text-align: left !important;"> Company </th>
            </tr>
            </thead>
            <tbody>
            @foreach($data['assetAssignments'] as $asset)
                <tr style="background-color: #e4f2f5; padding: 12px; color: black; font-size: 16px; width: 100%;">
                    <td style="padding: 12px">{{$asset->assetSubType->name ?? 'N/A'}}</td>
                    <td style="padding: 12px">{{$asset->barcode}}</td>
                    <td style="padding: 12px">{{$asset->company->name ?? 'N/A'}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <br/>
    <p>
        Regards,<br>
        EMS
    </p>
</div>
</body>
</html>
