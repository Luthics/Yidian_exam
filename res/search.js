function showHelp(){
    $("p#sbar").html('可使用班级、姓名、年级等关键词限定范围，多个关键词间用<strong>空格</strong>分隔开');
}
function showOri(){
    if($("p#sbar").html()=='可使用班级、姓名、年级等关键词限定范围，多个关键词间用<strong>空格</strong>分隔开') $("p#sbar").html(ori_info);
}
function showResult(str) {
    if (str.length == 0) {
        str = "failed";
        $("p#sbar").html("请输入一些东西再进行搜索~");
        return;
    }
    if (window.XMLHttpRequest) {// IE7+, Firefox, Chrome, Opera, Safari 浏览器执行
        xmlhttp = new XMLHttpRequest();
    }
    else {// IE6, IE5 浏览器执行
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            if(xmlhttp.responseText.length == 0){
                $("p#sbar").html("什么都没有找到呢，请换个关键词再试试");
            }
            else {
                $("table.dis").css("display", "table");
                $("#resultArea").html(xmlhttp.responseText);
                $("p#sbar").html("结果数：<strong>"+$("table.dis tr#row-1").length+"</strong>丨点击姓名可以查看更详细的信息");
            }
        }
    }
    xmlhttp.open("GET", "search.php?name=" + str, true);
    xmlhttp.send();
}
function showRank(str, sub) {
    if (str.length == 0) {
        str = "failed";

    }
    if (window.XMLHttpRequest) {// IE7+, Firefox, Chrome, Opera, Safari 浏览器执行
        xmlhttp = new XMLHttpRequest();
    }
    else {// IE6, IE5 浏览器执行
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var score = JSON.parse(xmlhttp.responseText);
            var scores = [], labels = [];
            console.log(score.body);
            for (i = 0; i < score.body.length; i++) {
                labels.push(score.body[i].name);
                scores.push(score.body[i].score);
            }
            chart_destory("ttctx");
            make_chart_rank("ttctx", scores, labels);
        }
    }
    xmlhttp.open("GET", "api.php?opt=1&grade=2022&class=L3&sub=" + sub + "&name=" + str, true);
    xmlhttp.send();
}
function showSub(str, sub) {
    if (str.length == 0) {
        str = "failed";

    }
    if (window.XMLHttpRequest) {// IE7+, Firefox, Chrome, Opera, Safari 浏览器执行
        xmlhttp = new XMLHttpRequest();
    }
    else {// IE6, IE5 浏览器执行
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var score = JSON.parse(xmlhttp.responseText);
            var scores = [], labels = [];
            var table = $("table#data");
            console.log(score.body);
            for (i = 0; i < score.body.length; i++) {
                table.append("<tr><td>" + score.body[i].name + "</td><td>" + score.body[i].score + "</td></tr>");
                labels.push(score.body[i].name);
                scores.push(score.body[i].score);
            }
            chart_destory("ttctx");
            //alert(score.body[0].score);
            make_chart_score("ttctx", scores, labels);
        }
    }
    xmlhttp.open("GET", "api.php?opt=1&grade=2022&class=L3&sub=" + sub + "&name=" + str, true);
    xmlhttp.send();
}