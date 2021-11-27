<?php
require "res/header.php";
function sql($sql_s)
{
    global $conn;
    return mysqli_query($conn, $sql_s);
}
$stu_name = $_REQUEST["name"];
$stu_class = $_REQUEST["class"];
$stu_grade = $_REQUEST["grade"];
$sql = "INSERT INTO `log` (`id`, `time`, `ip`, `keyword`) VALUES (NULL, '" . date("Y-m-d H:i:s") . "', '" . $_SERVER["REMOTE_ADDR"] . "', '" . $stu_name . "')
";
mysqli_query($conn, $sql);
$sql = "show columns from list";
$cols = mysqli_query($conn, $sql);
for ($lid = 0; $row_c = mysqli_fetch_row($cols); $lid++) {
    $exam_s[] = $row_c[0];
    $exam_struct[$row_c[0]] = $lid;
}
$sql = "show columns from students";
$cols = mysqli_query($conn, $sql);
for ($lid = 0; $row_c = mysqli_fetch_row($cols); $lid++) {
    $col_s[] = $row_c[0];
    $student_struct[$row_c[0]] = $lid;
}
$sql = "SELECT * FROM `list` ORDER BY time";
$examlist = sql($sql);
for ($lid = 0; $row = mysqli_fetch_row($examlist); $lid++) {
    foreach ($row as $key => $data) {
        $exam_list[$row[$exam_struct['grade']]][$row[$exam_struct['kind']]][$lid][$exam_s[$key]] = $data;
    }
}
$sql = "SELECT * FROM `list` ORDER BY time";
$elist = mysqli_query($conn, $sql);
if ($stu_class[0] == 'L') $tid = 1;
else if ($stu_class[0] == 'W') $tid = 2;
else $tid = 0;
foreach ($exam_list[$stu_grade][$tid] as $row) {
    $sql = "SELECT count(*) FROM " . $stu_grade . "_" . $row['name'] . " WHERE 姓名='{$stu_name}'";
    $re_count = sql($sql);
    if (mysqli_fetch_row($re_count)[0] == 0) continue;
    $sql = "SELECT * FROM `" . $stu_grade . "_" . $row['name'] . "` WHERE 姓名 like '{$stu_name}' and 班级 like '{$stu_class}'";
    $testdetail = sql($sql);
    $col_s = [];
    $sql = "show columns from " . $stu_grade . "_" . $row['name'] . "";
    $cols = sql($sql);
    while ($row_c = mysqli_fetch_row($cols)) {
        $col_s[] = $row_c[0];
    }
    while ($row_r = mysqli_fetch_row($testdetail)) {
        foreach ($row_r as $rol_r => $data_r) {
            if ($col_s[$rol_r] == "总分" && count($col_s) < 10) continue;
            else {
                if ($data_r == 0 || $data_r == "未扫，不计排名" || $data_r == "0，不计排名" || $data_r == "缺考，不计排名") {
                } else {
                    $scores[$col_s[$rol_r]][] = [$row['name'], $data_r];
                }
                $exam_scores[$row['name']][$col_s[$rol_r]] = $data_r;
            }
        }
    }
}
$ss = ["语文" => "yw", "英语" => "yy", "物理" => "wl", "化学" => "hx", "生物" => "sw", "数学" => "sx", "政治" => "zz", "历史" => "ls", "地理" => "dl", "总分" => "total", "班次" => "brank", "校次" => "arank"];
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8">
    <title><?php echo $stu_name . "丨成绩汇总" ?></title>
    <link href="https://api.yztv.live/cdn/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <link href="res/styleexam.css" rel="stylesheet" />
    <script src="https://api.yztv.live/cdn/jquery.min.js"></script>
    <script src="https://api.yztv.live/cdn/bootstrap.min.js"></script>
    <script src="https://api.yztv.live/cdn/chart.min.js"></script>
    <!-- Matomo -->
    <script>
        var _paq = window._paq = window._paq || [];
        /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
        _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u = "//stat.yztv.live/";
            _paq.push(['setTrackerUrl', u + 'matomo.php']);
            _paq.push(['setSiteId', '7']);
            var d = document,
                g = d.createElement('script'),
                s = d.getElementsByTagName('script')[0];
            g.async = true;
            g.src = u + 'matomo.js';
            s.parentNode.insertBefore(g, s);
        })();
    </script>
    <!-- End Matomo Code -->
</head>

<body><!--
    <div id="settings">
        <svg width="80" height="80" style="position: absolute;top: 0;border: 0;left: 0;transform: scale(-1, 1);width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1">
            <path d="M567.722667 806.912l27.733333-8.021333a299.008 299.008 0 0 0 123.648-71.338667l20.864-19.925333 81.834667 36.821333 46.805333-80.896-72.789333-52.224 6.826666-27.989333A298.922667 298.922667 0 0 0 811.306667 512c0-24.32-2.901333-48.213333-8.576-71.338667l-6.869334-27.989333 72.789334-52.224-46.805334-80.896-81.834666 36.821333-20.864-19.925333a299.008 299.008 0 0 0-123.605334-71.338667l-27.733333-8.021333L558.762667 128h-93.610667l-8.96 89.088-27.733333 8.021333a299.008 299.008 0 0 0-123.605334 71.338667l-20.864 19.925333-81.834666-36.821333-46.805334 80.896 72.789334 52.224-6.826667 27.989333A298.922667 298.922667 0 0 0 212.693333 512c0 24.32 2.901333 48.213333 8.576 71.338667l6.869334 27.989333-72.789334 52.224 46.805334 80.896 81.834666-36.821333 20.864 19.925333a299.008 299.008 0 0 0 123.605334 71.338667l27.733333 8.021333 8.96 89.088h93.610667l8.96-89.088z m79.445333 64.725333L636.202667 981.333333H387.797333l-10.965333-109.696a384.554667 384.554667 0 0 1-109.226667-63.018666l-100.736 45.397333L42.666667 639.317333l89.728-64.384a385.664 385.664 0 0 1 0-125.866666L42.666667 384.64l124.16-214.613333 100.821333 45.354666a384.554667 384.554667 0 0 1 109.184-63.018666L387.797333 42.666667h248.405334l10.965333 109.696c39.68 14.890667 76.544 36.224 109.226667 63.018666l100.736-45.397333L981.333333 384.682667l-89.728 64.384a385.664 385.664 0 0 1 0 125.866666L981.333333 639.36l-124.16 214.613333-100.821333-45.354666a384.554667 384.554667 0 0 1-109.184 63.018666zM512 682.666667a170.666667 170.666667 0 1 1 0-341.333334 170.666667 170.666667 0 0 1 0 341.333334z m0-85.333334a85.333333 85.333333 0 1 0 0-170.666666 85.333333 85.333333 0 0 0 0 170.666666z" />
        </svg>
    </div>-->
    <div id="container">
        <div class="row clearfix" style="margin: 0 10%;">
            <h1 style="text-align: center;"><?php echo $stu_name . "丨成绩汇总" ?></h1>
            <br>
            <div class="col-md-4 column">
                <canvas id="arank" width="400" height="400"></canvas>
                <canvas id="yw" width="400" height="400"></canvas>
                <canvas id="wl" width="400" height="400"></canvas>
            </div>
            <div class="col-md-4 column">
                <canvas id="total" width="400" height="400"></canvas>
                <canvas id="sx" width="400" height="400"></canvas>
                <canvas id="hx" width="400" height="400"></canvas>
            </div>
            <div class="col-md-4 column">
                <canvas id="brank" width="400" height="400"></canvas>
                <canvas id="yy" width="400" height="400"></canvas>
                <canvas id="sw" width="400" height="400"></canvas>
            </div>
            <br>
            <div class="col-md-12 column">
                <table class="table" style="display: table;">
                    <thead>
                        <tr>
                            <th>考试</th>
                            <th>语文</th>
                            <th>数学</th>
                            <th>英语</th>
                            <?php
                            if ($stu_class[0] != 'W') {
                                echo "<th>物理</th>" . PHP_EOL . "<th>化学</th>" . PHP_EOL . "<th>生物</th>" . PHP_EOL;
                            }
                            if ($stu_class[0] != 'L') {
                                echo "<th>政治</th>" . PHP_EOL . "<th>历史</th>" . PHP_EOL . "<th>地理</th>" . PHP_EOL;
                            }
                            ?>
                            <th>总分</th>
                            <th>班次</th>
                            <th>校次</th>
                        </tr>
                    </thead>
                    <tbody id="score_detail">
                        <?php
                        $subs = ["语文", "数学", "英语"];
                        if ($stu_class[0] != 'W') array_push($subs, "物理", "化学", "生物");
                        if ($stu_class[0] != 'L') array_push($subs, "政治", "历史", "地理");
                        array_push($subs, "总分", "班次", "校次");
                        foreach ($exam_scores as $exam_name => $data) {
                            echo "<tr>";
                            echo "<th>" . $exam_name . "</th>";
                            for ($lid = 0; $lid < count($subs); $lid++) {
                                echo "<td>";
                                if (array_key_exists($subs[$lid], $data) && $data[$subs[$lid]] != '0') {
                                    echo $data[$subs[$lid]];
                                } else echo "-";
                                echo "</td>";
                            }
                            echo "</tr>" . PHP_EOL;
                        }
                        /*
                            echo "<pre>";
                                print_r($exam_scores);
                            echo "</pre>";*/
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
<script>
    const up = (ctx, value) => ctx.p0.parsed.y <= ctx.p1.parsed.y ? value : undefined;
    const down = (ctx, value) => ctx.p0.parsed.y > ctx.p1.parsed.y ? value : undefined;

    function getRandomColor() {
        var letters = '0123456789ABCDEF'.split('');
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
    <?php
    echo "var stu_kind = '" . $stu_class[0] . "';" . PHP_EOL;
    foreach ($scores as $subj => $data) {
        if (array_key_exists($subj, $ss)) {
            echo "var " . $ss[$subj] . "_labels=[";
            foreach ($data as $lid => $score) {
                //if(count($data)-$lid>5) continue;
                if (substr($score[0], -9, 9) == "【文】") $exam_name = substr($score[0], 0, strlen($score[0]) - 9);
                else $exam_name = $score[0];
                echo "'" . $exam_name . "'";
                if ($lid + 1 != count($data)) echo ",";
            }
            echo "];" . PHP_EOL;
            echo "var " . $ss[$subj] . "_datas=[";
            foreach ($data as $lid => $score) {
                //if(count($data)-$lid>5) continue;
                echo "" . $score[1] . "";
                if ($lid + 1 != count($data)) echo ",";
            }
            echo "];" . PHP_EOL;
        }
    }
    ?>
    var chart = new Array();

    function make_chart(id, kind, labelname, datas, labels) {
        var cr = 'rgb(247,49,49,0.8)',
            cg = 'rgb(14,190,152)';
        var rank_display = false,
            score_display = false;
        if (kind == 'rank') {
            [cr, cg] = [cg, cr];
            rank_display = true;
        } else {
            score_display = true;
        }
        newChart = document.getElementById(id).getContext('2d');
        const rank_data = {
            labels: labels,
            datasets: [{
                type: 'line',
                label: labelname,
                data: datas,
                pointRadius: 3,
                pointBackgroundColor: [
                    'rgba(69,185,124, 0.3)',
                    'rgba(54, 162, 235, 0.3)',
                    'rgba(255, 206, 86, 0.3)',
                    'rgba(75, 192, 192, 0.3)',
                    'rgba(153, 102, 255, 0.3)',
                    'rgba(255, 159, 64, 0.3)'
                ],
                borderWidth: 2,
                pointBorderColor: [
                    'rgba(69,185,124,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                segment: {
                    borderColor: ctx => up(ctx, cr) || down(ctx, cg),
                },
                borderColor: cr,
                backgroundColor: cg,
                yAxisID: kind
            }]
        };
        chart[id] = new Chart(newChart, {
            data: rank_data,
            options: {
                aspectRatio: 1,
                scales: {
                    y: {
                        display: false
                    },
                    'score': {
                        type: 'linear',
                        position: 'left',
                        display: score_display
                    },
                    'rank': {
                        type: 'linear',
                        position: 'left',
                        reverse: true,
                        display: rank_display
                    }
                }
            }
        });
    }
    if (typeof(arank_datas) != "undefined") make_chart('arank', 'rank', "校次排名", arank_datas, arank_labels);
    if (typeof(brank_datas) != "undefined") make_chart('brank', 'rank', "班次排名", brank_datas, brank_labels);
    if (typeof(total_datas) != "undefined") make_chart('total', 'score', "总分", total_datas, total_labels);
    if (typeof(yy_datas) != "undefined") make_chart('yy', 'score', "英语", yy_datas, yy_labels);
    if (typeof(yw_datas) != "undefined") make_chart('yw', 'score', "语文", yw_datas, yw_labels);
    if (typeof(sx_datas) != "undefined") make_chart('sx', 'score', "数学", sx_datas, sx_labels);
    if (stu_kind != 'W') {
        if (typeof(wl_datas) != "undefined") make_chart('wl', 'score', "物理", wl_datas, wl_labels);
        if (typeof(hx_datas) != "undefined") make_chart('hx', 'score', "化学", hx_datas, hx_labels);
        if (typeof(sw_datas) != "undefined") make_chart('sw', 'score', "生物", sw_datas, sw_labels);
    }
    if (stu_kind != 'L') {
        if (typeof(zz_datas) != "undefined") make_chart('wl', 'score', "政治", zz_datas, zz_labels);
        if (typeof(ls_datas) != "undefined") make_chart('hx', 'score', "历史", ls_datas, ls_labels);
        if (typeof(dl_datas) != "undefined") make_chart('sw', 'score', "地理", dl_datas, dl_labels);
    }
</script>

</html>