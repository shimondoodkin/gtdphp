<thead>
    <tr>
    <?php foreach ($dispArray as $key=>$val) if ($show[$key]) echo "<th class='col-$key'>$val</th>"; ?>
    </tr>
</thead>
<?php if (!empty($tfoot)) echo $tfoot; ?>
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
                $cleaned=makeclean($row[$key]);
                echo "<a href='itemReport.php?itemId={$row['itemId']}'>"
                    ,"<img src='themes/{$config['theme']}/report.gif' class='noprint' alt='Report /' /></a>"
                    ,"<a href='item.php?itemId={$row['itemId']}'>"
                    ,"<img src='themes/{$config['theme']}/edit.gif' class='noprint' alt='Edit ' /></a>"
                    ,"<a ",($row['NA'])?"class='nextactionlink'":''
                    ," title='Edit $cleaned' href='item"
                    ,($row['doreport'])?'Report':''
                    ,".php?itemId={$row['itemId']}'>$cleaned</a>";
                break;
            case 'assignType':
                echo "<a href='assignType.php?itemId={$row['itemId']}'>Set type</a>";
                break;
            case 'checkbox':
                echo "<input name='{$row['checkboxname']}' value='{$row['checkboxvalue']}' type='checkbox' />";
                break;
            case 'NA':
                echo "<input name='isNAs[]' value='{$row['itemId']}'"
                    ,"type='",($dispArray[$key.'.type']==='radio')?'radio':'checkbox',"'"
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
                        ,$row['itemId'],"'>!"
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
                if ($row[$key.'id']==='')
                    echo '&nbsp;';
                else {
                    $out='';
                    $brk='';
                    $pids=explode(',',$row['parentId']);
                    $pnames=explode($config['separator'],$row['ptitle']);
                    foreach ($pids as $pkey=>$pid) {
                        $thisparent=makeclean($pnames[$pkey]);
                        echo "$brk<a href='itemReport.php?itemId=$pid' title='Go to the $thisparent report'>$thisparent</a> ";
                        $brk="<br />\n";
                    }
                }
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
            case 'type':
                if (isset($row['isSomeday']) && $row['isSomeday']==='y')
                    echo 'Someday';
                else
                    echo getTypes($row[$key]);
                break;
            case 'description': // flows through to case 'outcome' deliberately
            case 'desiredOutcome':
                echo trimTaggedString($row[$key],$config['trimLength']);
                break;
            case 'fulldesc':
                echo trimTaggedString($row[$key],0);
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
