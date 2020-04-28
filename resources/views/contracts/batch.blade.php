<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Batch</title>
    <link rel="stylesheet" type="text/css" href="/home/qvl0gkhxoqm/public_html/new/public/bootstrap/css/bootstrap.css">

<style>


    html,
    body {
        direction: rtl;
        text-align: right;
        
    }

    body,
    .content {
        width: 100%;
        display: block;
        margin: auto;
        font-size:20px;
    }
    .content
    {
        border:1px solid #000;
        padding:2%;
        padding-top:4%;
        margin-top:20%;
    }
 /*   .content:before
    {
        background-image:url("/home/qvl0gkhxoqm/public_html/new/public/images/backgroundimg.jpeg");
        background-size:contain;
        content: "";
        display: block;
        position: absolute;
        top: 50%;
        left: 40%;
        width:20%;
        height: 100%;
        z-index: -2;
        opacity: 0.2;
        background-repeat: no-repeat;
    }*/
    .top-header {
        text-align: center;
    }

    .text-left {
        display: inline-block;
    }

    .bold {
        font-weight: bold;
        ;
    }
    .half-sec
    {
        width:50%;
    }
    ul{
        list-style:none;
    }
    li{
        padding-bottom:2%;
    }
</style>
</head>
<body>
    <div class="content">
        <div class="top-header">
            <div class="row">
                <div class="col"  style="font-weight:bold;font-size:19px"> مرسى قرية القطان</div>
                <div class="col"><img src="/home/qvl0gkhxoqm/public_html/new/public/images/topimg.jpeg" style="width: 150px;
                    height: 100px"/></div>
                <div class="col">
                    <p class="bold"><span style="font-size:19px;">AlqattanMarrine</p>
                </div>
            </div>
        </div>
        <!-- start Heading -->
        <h3 class="text-center"  style="font-weight:bold;margin-top:80px">سند القبض</h3>
        <!-- top data -->
        <ul>
            <li>التاريخ {{\Carbon\Carbon::today()->toDateString()}}</li>
            <li>استلمنا من السيد / {{$contract[0]->name}} </li>
            <li>مبلغ وقدر/{{$contract[0]->amount}} ريال فقط</li>
            <li>وذألك مقابل دفعه من الايجار الواسطة البحرية و المسماة  ({{$contract[0]->wastaname}})رقم({{$contract[0]->codewasta}}) </li>
            <li>الدفعه من ({{$contract[0]->from}}) الى ( {{$contract[0]->to}})</li>
            <li>التوقيع /</li>
            <li>اسم المستخدم / {{$contract[0]->owner}}</li>
            <li>الوقت / {{$contract['time']}}</li>
            <li>التاريخ / {{$contract['date']}}</li>
            <img src="/home/qvl0gkhxoqm/public_html/new/public/images/khetm.jpeg"
            style="display:block;margin:auto;width:150px;height:150px;margin-top:3%" />
            <hr style="background-color:#000;height:2px;margin-top:20%"/>
            <p class="bold text-center" style="font-size:12px"><p class="bold text-center">مستثمر مرسى قرية القطان (
                    مؤسسة: عبدالقادر إبراهيم بخاري) س ت : 4030332339 جوال : 0590123466   <br />
                    <span class="bold" style="color:blue"> البريد اﻹلكتروني : alqattan.marrine@gmail.com </span><br/>
                </p><span>
            <hr style="background-color:#000;height:2px;"/>
            </p>
        </ul>
    </div>
</body>

</html>