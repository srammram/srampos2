
    <html>
    <head>
        <meta charset="utf-8">
        <title><?=$page_title . " " . lang("no") . " " . $inv->id;?></title>
        <base href="<?=base_url()?>"/>
        <meta http-equiv="cache-control" content="max-age=0"/>
        <meta http-equiv="cache-control" content="no-cache"/>
        <meta http-equiv="expires" content="0"/>
        <meta http-equiv="pragma" content="no-cache"/>
        <link rel="shortcut icon" href="<?=$assets?>images/favicon.ico"/>       
        
        <link rel="stylesheet" href="<?=$assets?>styles/helpers/bootstrap.min.css" type="text/css" />
        <link rel="stylesheet" href="<?=$assets?>styles/theme.css" type="text/css"/>
         
        </head>
    <body>
        <table>
            <thead>
                <th class="col-md-2">total</th>
                <th class="col-md-2">tax</th>
                <th class="col-md-2">grand_total</th>
                <th class="col-md-2">no_of_items</th>
            </thead>
      <tbody>
        <tr>
            <td><?=$bils['total']?></td>
            <td><?=$bils['total_tax']?></td>
            <td><?=$bils['grand_total']?></td>
            <td><?=$bils['total_items']?></td>
            <td><a href="<?=admin_url('pos/update_bill/'.$bill_id)?>">Update</a></td>
        </tr>
       </tbody>
        </table>
    </body>
    </html>
        