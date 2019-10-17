
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
                <th>bill id</th>
                <th>bill item id</th>
                <th>net unit price</th>
                <th>quantity</th>
                <th>delete</th>
            </thead>
        <?php foreach($bill_items as $k => $row) : ?>
        <tr>
            <td><?=$row->bil_id?></td>
            <td><?=$row->id?></td>
            <td><?=$row->net_unit_price?></td>
            <td><?=$row->quantity?></td>
            <td><a href="<?=admin_url('pos/delete_bill_item/'.$row->id)?>">Delete</a></td>
        </tr>
        <?php endforeach; ?>
        </table>
    </body>
    </html>
        