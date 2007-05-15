<thead>
    <tr>
    <?php foreach ($dispArray as $key=>$val) if ($show[$key]) echo "<th class='col-$key'>$val</th>"; ?>
    </tr>
</thead>
<tbody>
<?php
foreach ($maintable as $row) {
    echo '<tr>';
    foreach ($dispArray as $key=>$val) if ($show[$key]) {
        echo "<td class='col-$key"
            ,(isset($row["$key.class"]))?" ".$row["$key.class"]:''
            ,"'"
            ,(isset($row["$key.title"]))?(' title="'.$row["$key.title"].'"'):''
            ,'>';
        switch ($key) {
            case 'title': 
                echo "<a href='itemReport.php?itemId={$row['id']}'>"
                    ,"<img src='themes/{$config['theme']}/report.gif' alt='Go to $row[$key] report' /></a>"
                    ,"<a href='item.php?itemId={$row['id']}'>"
                    ,"<img src='themes/{$config['theme']}/edit.gif' alt='Edit $row[$key]' /></a>"
                    ,"<a ",($row['NA'])?"class='nextactionlink'":''
                    ," title='Edit {$row[$key]}' href='item"
                    ,($row['doreport'])?'Report':''
                    ,".php?itemId={$row['id']}'>$row[$key]</a>";
                break;
            case 'checkbox':
                echo "<input name='{$row['checkboxname']}' value='{$row['checkboxvalue']}' type='checkbox' />";
                break;
            case 'NA':
                echo "<input type='checkbox' name='isNAs[]' value='{$row['id']}'"
                        ,($row[$key])?" checked='checked' ":''
                        ,' />';
                break;
            case 'flags':
                if ($row[$key]==='')
                    echo '&nbsp;';
                else
                    echo "<a class='noNextAction' title='"
                        ,($row[$key]==='noNA')?
                            "No next action - click to assign one' href='itemReport.php?itemId="
                            :("No children - click to create one' href='item.php?type=".$row['childtype'].'&amp;parentId=')
                        ,$row['id'],"'>!"
                        ,($row[$key]==='noChild')?'!':'&nbsp;'
                        ,"</a>";
                break;
            case 'category':
                if ($row[$key.'id'])
                    echo "<a href='editCat.php?field=category&amp;id=",$row[$key.'id'],"' title='Edit the {$row[$key]} category'>{$row[$key]}</a>";
                else
                    echo '&nbsp;';
                break;
            case 'parent':
                if ($row[$key.'id'])
                    echo "<a href='itemReport.php?itemId=", $row[$key.'id'],"' title='Go to the {$row[$key]} report'>{$row[$key]}</a>";
                else
                    echo '&nbsp;';
                break;
            case 'context':
                if ($row[$key]=='')
                    echo '&nbsp;';
                else
                    echo "<a href='reportContext.php#c",$row[$key.'id'],"' title='Go to the ",$row[$key]," context report'>{$row[$key]}</a>";
                break;
            case 'timeframe':
                if ($row[$key.'id'])
                    echo "<a href='editCat.php?field=time-context&amp;id=",$row[$key.'id'],"' title='Edit the {$row[$key]} time context'>{$row[$key]}</a>";
                else
                    echo '&nbsp;';
                break;
            case 'description': // flows through to case 'outcome' deliberately
            case 'outcome':
                echo trimTaggedString($row[$key],$config['trimLength']);
                break;
            default:
                echo $row[$key];
                break;
        }
        echo "</td>\n";
    }
    echo "</tr>\n";
} ?>
</tbody>
