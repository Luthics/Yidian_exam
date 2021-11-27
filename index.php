<!DOCTYPE html>
<html lang="zh-CN">
  <head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta charset="utf-8">
    <title>亿点丨成绩查询系统</title>
	<link href="https://api.yztv.live/cdn/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
	<link rel="stylesheet" href="res/style.css"/>
    <script src="https://api.yztv.live/cdn/jquery.min.js"></script>
    <script src="https://api.yztv.live/cdn/bootstrap.min.js"></script>
    <script src="https://api.yztv.live/cdn/chart.min.js"></script>
	<script src="res/search.js"></script>
    <!-- Matomo -->
    <script>
      var _paq = window._paq = window._paq || [];
      /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
      _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
      (function() {
        var u="//stat.yztv.live/";
        _paq.push(['setTrackerUrl', u+'matomo.php']);
        _paq.push(['setSiteId', '7']);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
      })();
    </script>
    <!-- End Matomo Code -->
  </head>
  <body style="background:#f2f2f2">
	<div id="container">
        <div class="kup">
            <div style = "text-align:center;">
                <img src="res/logon.png" style = "width:80%;"/>
            </div>
            <div class="search kup">
                <div class = "cof  bar8">
                    <input type="text" id = "infoinput" onfocus="showHelp()" onblur="showOri()" onkeydown="var e=window.event || arguments[0];if(e.keyCode==13){showResult(infoinput.value)}" placeholder="请输入关键词..." autofocus = "autofocus"/>
                    <button class="sqrbtn" onclick = "showResult(infoinput.value)"></button>
                </div>
                <div style = "text-align: center;">
                    <br/>
                    <p id="sbar" style = "font-size:12px">最新更新：<?php 
                        require "res/header.php";
                        $sql="SELECT * FROM `update_logs` ORDER BY time DESC";
                        $upd=mysqli_query($conn, $sql);
                        $tec = mysqli_fetch_row($upd);
                        echo $tec[1]."丨已成功查询 <strong>";
                        $sql="SELECT count(*) FROM `log`";
                        $cou=mysqli_query($conn, $sql);
                        echo mysqli_fetch_row($cou)[0];
                    ?></strong> 次</p>
                </div>
            </div>
        </div>
		<div id = "dataArea">
				<table class="table dis" id="resultTable">
					<thead>
						<tr>
							<th>年级</th>
                            <th>班级</th>
							<th>姓名</th>
                            <th>上次考试排名</th>
						</tr>
					</thead>
					<tbody id = "resultArea">
					</tbody>
				</table>
			</div>
	</div>
  </body>
  <script>
      var ori_info = $("p#sbar").html();
  </script>
</html>